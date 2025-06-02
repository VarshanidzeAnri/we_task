<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;

class CommentService
{
    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addComment(Comment $comment): void
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function deleteComment(Comment $comment): int
    {
        $newsId = $comment->getNews()->getId();
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
        
        return $newsId;
    }
}
