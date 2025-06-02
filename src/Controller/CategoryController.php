<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryForm;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    private CategoryService $categoryService;
    
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    #[Route('/{id<\d+>}', name: 'category_show')]
    public function show(Category $category, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $newsPagination = $this->categoryService->getPaginatedNews($category, $categoryRepository, $paginator, $request);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'news' => $newsPagination,
        ]);
    }

    #[Route('/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->categoryService->create($category, $entityManager);

            return $this->redirectToRoute('home');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'category_edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'category_delete')]
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $this->categoryService->delete($category, $entityManager);

        return $this->redirectToRoute('home');
    }
}
