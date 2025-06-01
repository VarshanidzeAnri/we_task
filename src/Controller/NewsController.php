<?php

namespace App\Controller;

use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewsController extends AbstractController
{
    #[Route('/news/{id<\d+>}', name: 'news_details')]
    public function details(News $news): Response
    {
        return $this->render('news/details.html.twig', [
            'news' => $news,
        ]);
    }
}
