<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\CommentForm;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    private CommentService $commentService;
    
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }
    
    #[Route('/news/{id<\d+>}', name: 'comment_add')]
    public function add(News $news, Request $request): Response
    {
        $comment = new Comment();
        $comment->setNews($news);
        
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->addComment($comment);
        }
        
        return $this->redirectToRoute('news_details', ['id' => $news->getId()]);
    }
    
    #[Route('/{id<\d+>}/delete', name: 'comment_delete')]
    public function delete(Comment $comment): Response
    {
        $newsId = $this->commentService->deleteComment($comment);
        
        return $this->redirectToRoute('news_details', ['id' => $newsId]);
    }
}
