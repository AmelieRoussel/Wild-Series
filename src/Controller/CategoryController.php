<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/categories", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * Show all rows from Category's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * The controller for the category add form
     *
     * @Route("/new", name="new")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        // Create a new Category Object
        $category = new Category();
        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            /// Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $entityManager->persist($category);
            // Flush the persisted object
            $entityManager->flush();

            $this->addFlash('success', 'La nouvelle catégorie a bien été ajoutée');

            // Finally redirect to categories list
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Getting all programs by category's name
     *
     * @Route("/{category<^[0-9]+$>}", methods={"GET"}, name="show")
     * @param Category $category
     * @return Response
     */
    public function show(Category $category): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'DESC']);
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('category/show.html.twig', [
            'programs' => $programs,
            'category' => $category
        ]);
    }
}