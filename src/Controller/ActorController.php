<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ActorType;
use Symfony\Component\HttpFoundation\Request;




#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ActorRepository $actorRepository): Response
    {
        $actors = $actorRepository->findAll();

        return $this->render('actor/index.html.twig', [
            'actors' => $actors
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
        public function new(Request $request, ActorRepository $actorRepository)
        {
            $actor = new Actor();
            $form = $this->createForm(ActorType::class, $actor);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $actorRepository->save($actor, true);
                $this->addFlash('success', 'L\'acteur a été ajouté!');
                return $this->redirectToRoute('actor_index');
            }
            return $this->render('actor/new.html.twig', [
                'form' => $form,
            ]);
        }

        #[Route('/{id<^[0-9]+$>}', name: 'show')]
            public function show(Actor $actor): Response
            {
                return $this->render('actor/show.html.twig',[
                    'actor' => $actor
                ]);
            }

}