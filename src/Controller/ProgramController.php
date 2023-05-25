<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]

    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', [
       'programs' => $programs
    ]);
    }

    #[Route('/show/{id<^[0-9]+$>}', name: 'show')]
    public function show(int $id, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);
        // same as $program = $programRepository->find($id);

        if (!$program) {
            throw $this->createNotFoundException(
                'Aucune série avec le numéro : '.$id.' n\'a été trouvée dans la liste des séries.'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
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