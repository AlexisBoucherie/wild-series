<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('program/index.html.twig');
    }

    #[Route('/{page}', name: 'show', requirements: ['page'=>'\d+'], methods: ['GET'])]
    public function show(int $page = 1): Response
    {
        return $this->render('program/program.html.twig', [
            'page' => $page
        ]);
    }
}
