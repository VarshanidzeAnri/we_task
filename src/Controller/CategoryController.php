<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryForm;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category/{id<\d+>}', name: 'category_show')]
    public function show(Category $category, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $categoryRepository->getNewsForCategoryQueryBuilder($category);
        
        $newsPagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'news' => $newsPagination,
        ]);
    }

    #[Route('/category/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category created successfully!');

            return $this->redirectToRoute('home');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/category/{id<\d+>}/edit', name: 'category_edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryForm::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully!');

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);

        }

        return $this->render('category/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/category/{id<\d+>}/delete', name: 'category_delete')]
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'Category deleted successfully!');

        return $this->redirectToRoute('home');
    }
}
