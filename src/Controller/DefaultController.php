<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function index(ProgramRepository $programRepository): Response
    {
        return $this->render('index.html.twig', [
            'website' => 'Wild Séries',
            'programs' => $programRepository->findBy([], ['id' => 'DESC'], 4),
        ]);
    }

    /**
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function navbarTop(CategoryRepository $categoryRepository): Response
    {
        return $this->render('includes/navbartop.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC'])
        ]);
    }
}
