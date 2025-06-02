<?php

namespace App\Service;

use App\Entity\NewsView;
use Doctrine\ORM\EntityManagerInterface;

class NewsViewService
{
    public function store($news, EntityManagerInterface $entityManager): void
    {
            $newsView = new NewsView();
            $newsView->setNews($news);
            
            $entityManager->persist($newsView);
            $entityManager->flush();
    }
}
