<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
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
}
