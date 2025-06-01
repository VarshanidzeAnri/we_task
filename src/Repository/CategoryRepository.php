<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }


    public function findAllWithLimitedNews(): array
    {
        $categories = $this->findAll();
        $em = $this->getEntityManager();

        foreach ($categories as $category) {
            $limitedNews = $em->createQueryBuilder()
                ->select('n')
                ->from('App\Entity\News', 'n')
                ->where(':category MEMBER OF n.categories')
                ->orderBy('n.id', 'DESC')
                ->setParameter('category', $category)
                ->setMaxResults(3)
                ->getQuery()
                ->getResult();

            $category->limitedNews = $limitedNews;
        }

        return $categories;
    }

    public function getNewsForCategoryQueryBuilder(Category $category)
    {
        $em = $this->getEntityManager();
        
        return $em->createQueryBuilder()
            ->select('n')
            ->from('App\Entity\News', 'n')
            ->where(':category MEMBER OF n.categories')
            ->setParameter('category', $category)
            ->orderBy('n.id', 'DESC');
    }

    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
