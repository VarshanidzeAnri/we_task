<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/news/new', name: 'news_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $news = new News();

        // dd($news);
        $form = $this->createForm(NewsForm::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($news);
            $entityManager->flush();
            $this->addFlash('success', 'News created successfully!');

            return $this->redirectToRoute('home');
        }

        return $this->render('news/new.html.twig', [
            'form' => $form,
        ]);
    }
}
