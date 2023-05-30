<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Service\ProgramDuration;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]

    public function index(ProgramRepository $programRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        if (!$session->has('total')) {
            $session->set('total', 0); // if total doesn’t exist in session, it is initialized.
        }

        $total = $session->get('total'); // get actual value in session with ‘total' key.

        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', [
       'programs' => $programs
    ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository, SluggerInterface $slugger) : Response
    {
        // Create a new Category Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // For example : persiste & flush the entity
            // And redirect to a route that display the result

            // Generate the slug
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            
            $programRepository->save($program, true);

            // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
            $this->addFlash('success', 'La série a été ajoutée!');
    
            // Redirect to categories list
            return $this->redirectToRoute('program_index');
        }
    
        // Render the form
        return $this->render('program/new.html.twig', [
            'program' =>$program,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    public function show(string $slug, Program $program, ProgramRepository $programRepository, ProgramDuration $programDuration): Response
    {
        $program = $programRepository->findOneBy(['slug' => $slug]);
        // same as $program = $programRepository->find($id);

        if (!$program) {
            throw $this->createNotFoundException(
                'Aucune série avec le numéro : '.$slug.' n\'a été trouvée dans la liste des séries.'
            );
        }

        $duration = $programDuration->calculate($program);

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'duration' => $duration,
        ]);
    }

    #[Route("/{programId}/seasons/{seasonId}", requirements: ['programId' => '\d+', 'seasonId' => '\d+'], name: "season_show", methods: ['GET'])]

    /**
     * This method is used to display a season by id
     *
     * @param Program $program
     * @param Season $season
     * @param EpisodeRepository $episodeRepository
     * @return Response
     */

    public function showSeason(int $programId, int $seasonId, ProgramRepository $programRepository, SeasonRepository $seasonRepository, EpisodeRepository $episodeRepository): Response
    {
        $programs = $programRepository->find($programId);
        $seasons = $seasonRepository->find($seasonId);
        $episodes = $episodeRepository->findBy(['season' => $seasonId]);
        if (!$programs) {
            throw $this->createNotFoundException( 'Aucune série avec le numéro : ' . $programId . ' n\'a été trouvée dans la liste des séries.');
        }
        if (!$seasons) {
            throw $this->createNotFoundException('Aucune saison avec le numéro : ' . $seasonId . ' n\'a été trouvée dans la liste des saisons.');
        }
        return $this->render('program/season_show.html.twig', [
            'program' => $programs,
            'seasons' => $seasons,
            'episodes' => $episodes,
        ]);
    } 


    /**
     * This method is used to display an episode by id
     *
     * @param Program $program
     * @param Season $season
     * @param Episode $episode
     * @return Response
     */
    #[Route("/{programId}/seasons/{seasonId}/episodes/{episodeId}", requirements: ['programId' => '\d+', 'seasonId' => '\d+', 'episodeId' => '\d+'], name: "episode_show", methods: ['GET'])]
    #[Entity('program', options: ['mapping' => ['programId' => 'id']])]
    #[Entity('season', options: ['mapping' => ['seasonId' => 'id']])]
    #[Entity('episode', options: ['mapping' => ['episodeId' => 'id']])]

    public function showEpisode(Program $program, Season $season, Episode $episode): Response 
     {
        if (!$program) {
            throw $this->createNotFoundException(
                'Aucune série avec le numéro : ' . $program->getId() . ' n\'a été trouvée dans la liste des séries.'
            );
        }
        if (!$season) {
            throw $this->createNotFoundException(
                'Aucune saison avec le numéro : ' . $season->getId() . ' n\'a été trouvée dans la liste des saisons.'
            );
        }
        if (!$episode) {
            throw $this->createNotFoundException(
                'Aucun épisode avec le numéro : ' . $episode->getId() . ' n\'a été trouvée dans la liste des épisodes.'
            );
        }
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'seasons' => $season,
            'episodes' => $episode,
        ]);
    }
}