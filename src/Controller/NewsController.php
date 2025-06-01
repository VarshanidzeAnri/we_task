<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Category;
use App\Form\NewsForm;
use App\Service\FileUploader;
use App\Service\RandomStringGenerator;
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
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $news = new News();
        $form = $this->createForm(NewsForm::class, $news);
        $form->handleRequest($request);
        
        $categories = $entityManager->getRepository(Category::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            
            if ($pictureFile) {
                $filename = $fileUploader->uploader($pictureFile);
                $pictureFile->move($this->getParameter('news_files'), $filename);
                
                $news->setPicture('news/' . $filename);
            }

            $entityManager->persist($news);
            $entityManager->flush();
            $this->addFlash('success', 'News created successfully!');

            return $this->redirectToRoute('home');
        }

        return $this->render('news/new.html.twig', [
            'form' => $form,
            'categories' => $categories,    
        ]);
    }
}
