<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchProgramFormType;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;

/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     *
     * @Route("/", name="index")
     * @param ProgramRepository $programRepository
     * @param Request $request
     * @return Response A response instance
     */
    public function index(ProgramRepository $programRepository, Request $request): Response
    {
        $form = $this->createForm(SearchProgramFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['Rechercher'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Getting a program by id
     *
     * @Route("/{slug}", methods={"GET"}, name="show")
     * @param Program $program
     * @return Response
     */
    public function show(Program $program): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $seasons = $program->getSeasons();
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * Getting a season by id
     *
     * @Route("/{program_slug}/season/{season<^[0-9]+$>}", methods={"GET"}, name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @param Program $program
     * @param Season $season
     * @return Response
     */
    public function showSeason(Program $program, Season $season): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        if (!$season) {
            throw $this->createNotFoundException(
                'No season found in season\'s table.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season
        ]);
    }

    /**
     * @Route("/{program_slug}/seasons/{season<^[0-9]+$>}/episodes/{episode_slug}", methods={"GET","POST"}, name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode_slug": "slug"}})
     * @param Program $program
     * @param Season $season
     * @param Episode $episode
     * @param Request $request
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setEpisode($episode);
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a bien été ajouté');

            return $this->redirectToRoute('program_episode_show', [
                'program_slug' => $program->getSlug(),
                'season' => $season->getId(),
                'episode_slug' => $episode->getSlug()
            ]);
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/watchlist", methods={"GET","POST"}, name="watchlist")
     * @param Program $program
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addToWatchList(Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getWatchlist()->contains($program)) {
            $this->getUser()->removeWatchlist($program);
        } else {
            $this->getUser()->addWatchlist($program);
        }
        $entityManager->flush();

        return $this->json([
            'isInWatchlist' => $this->getUser()->isInWatchlist($program)
        ]);
    }
}
