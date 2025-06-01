<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\CommentForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/news/{id<\d+>}/comment', name: 'comment_add')]
    public function add(News $news, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setNews($news);
        
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your comment has been added');
        }
        
        return $this->redirectToRoute('news_details', ['id' => $news->getId()]);
    }
    
    #[Route('/comment/{id<\d+>}/delete', name: 'comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $newsId = $comment->getNews()->getId();
        $entityManager->remove($comment);
        $entityManager->flush();
        
        $this->addFlash('success', 'Comment deleted');
        return $this->redirectToRoute('news_details', ['id' => $newsId]);
    }
}
