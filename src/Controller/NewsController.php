<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\NewsView;
use App\Form\NewsForm;
use App\Form\CommentForm;
use App\Service\FileUploader;
use App\Service\NewsViewService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/news')]
final class NewsController extends AbstractController
{
    public function __construct(
        private NewsViewService $newsViewService,
    ){}

    #[Route('/{id<\d+>}', name: 'news_details')]
    public function details(News $news, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $this->newsViewService->store($news, $entityManager);
        }
        
        $comments = $entityManager->getRepository(Comment::class)
            ->findBy(['news' => $news], ['id' => 'DESC']);
        
        $comment = new Comment();
        $commentForm = $this->createForm(CommentForm::class, $comment);

        return $this->render('news/details.html.twig', [
            'news' => $news,
            'comments' => $comments,
            'comment_form' => $commentForm,
        ]);
    }

    #[Route('/new', name: 'news_new')]
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

            return $this->redirectToRoute('home');
        }

        return $this->render('news/new.html.twig', [
            'form' => $form,
            'categories' => $categories,    
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'news_edit')]
    public function edit(News $news, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(NewsForm::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            
            if ($pictureFile) {
                if ($news->getPicture()) {
                    $existingPicturePath = $this->getParameter('news_files') . '/' . basename($news->getPicture());
                    if (file_exists($existingPicturePath)) {
                        unlink($existingPicturePath);
                    }
                }
                $filename = $fileUploader->uploader($pictureFile);
                $pictureFile->move($this->getParameter('news_files'), $filename);
                
                $news->setPicture('news/' . $filename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('news/edit.html.twig', [
            'form' => $form,
            'news' => $news,
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'news_delete')]
    public function delete(News $news, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($news);
        if ($news->getPicture()) {
            $picturePath = $this->getParameter('news_files') . '/' . basename($news->getPicture());
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }
        }
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
