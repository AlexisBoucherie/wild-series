<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->renderForm('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, MailerInterface $mailer, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {
        // Create a new Category Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Create a slug
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);

            // Set the program's owner
            $program->setOwner($this->getUser());

            $programRepository->save($program, true);
            // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
            $this->addFlash('success', 'The new program has been created');

            // send email when a new program is added
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('alexis.boucherie@gmail.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);

            // Redirect to programs list
            return $this->redirectToRoute('program_index', ['program' => $program]);
        }

        // Render the form
        return $this->renderForm('program/new.html.twig', [
            'program_form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    #[Route('/{slug}/season/{season_id}', name: 'season_show')]
    #[Entity('program', options: ['mapping' => ['slug' => 'slug']])]
    #[Entity('season', options: ['mapping' => ['season_id' => 'id']])]
    public function showSeason(Program $program, Season $season): Response
    {
        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    #[Route('/{slug}/season/{season_id}/episode/{episode_id}', name: 'episode_show')]
    #[Entity('program', options: ['mapping' => ['slug' => 'slug']])]
    #[Entity('season', options: ['mapping' => ['season_id' => 'id']])]
    #[Entity('episode', options: ['mapping' => ['episode_id' => 'id']])]
    public function showEpisode( Program $program, Season $season, Episode $episode): Response
    {
        $allComments = $episode->getComments();

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'comments' => $allComments,
        ]);
    }

    #[Route('/comment/new', name: 'new_comment', methods: ['GET', 'POST'])]
    public function newComment(Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);
            $this->addFlash('success', 'Your new comment has been added');

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/new_comment.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }
}