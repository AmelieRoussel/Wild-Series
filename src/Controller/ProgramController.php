<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Form\ProgramType;
use App\Repository\ProgramRepository;
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
     * @return Response A response instance
     */
    public function index(ProgramRepository $programRepository): Response
    {
        return $this->render('program/index.html.twig', [
            'programs' => $programRepository->findAll()
        ]);
    }

    /**
     * Getting a program by id
     *
     * @Route("/{id<^[0-9]+$>}", methods={"GET"}, name="show")
     * @return Response
     */
    public function show(Program $program): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program found in promgram\'s table.'
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
     * @Route("/{program<^[0-9]+$>}/season/{season<^[0-9]+$>}", methods={"GET"}, name="season_show")
     * @return Response
     */
    public function showSeason(Program $program, Season $season)
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
     * @Route("/{program<^[0-9]+$>}/seasons/{season<^[0-9]+$>}/episodes/{episode<^[0-9]+$>}", methods={"GET"}, name="episode_show")
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}