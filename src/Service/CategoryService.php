<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryService
{
    public function create(Category $category, EntityManagerInterface $entityManager): void
    {
        $entityManager->persist($category);
        $entityManager->flush();
    }
    
    public function delete(Category $category, EntityManagerInterface $entityManager): void
    {
        $entityManager->remove($category);
        $entityManager->flush();
    }

    public function getPaginatedNews(Category $category, CategoryRepository $categoryRepository,PaginatorInterface $paginator, Request $request,int $itemsPerPage = 10) {
        $queryBuilder = $categoryRepository->getNewsForCategoryQueryBuilder($category);
        return $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1), 
            $itemsPerPage
        );
    }
}
