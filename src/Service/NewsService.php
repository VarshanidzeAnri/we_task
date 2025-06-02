<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\NewsView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NewsService
{
    private $newsFilesDirectory;
    private $fileUploader;
    
    public function __construct(
        string $newsFilesDirectory,
        FileUploader $fileUploader
    ) {
        $this->newsFilesDirectory = $newsFilesDirectory;
        $this->fileUploader = $fileUploader;
    }
    
    public function create($news, $form, EntityManagerInterface $entityManager): void
    {
        $this->handlePictureUpload($news, $form);
        
        $entityManager->persist($news);
        $entityManager->flush();
    }
    
    public function update($news, $form, EntityManagerInterface $entityManager): void
    {
        $this->handlePictureUpload($news, $form, true);
        
        $entityManager->flush();
    }
    
    public function delete(News $news, EntityManagerInterface $entityManager): void
    {
        $entityManager->remove($news);
        
        if ($news->getPicture()) {
            $picturePath = $this->newsFilesDirectory . '/' . basename($news->getPicture());
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }
        }
        
        $entityManager->flush();
    }
    
    private function handlePictureUpload($news, $form, $isUpdate = false): void
    {
        $pictureFile = $form->get('picture')->getData();
        
        if ($pictureFile) {
            if ($isUpdate && $news->getPicture()) {
                $existingPicturePath = $this->newsFilesDirectory . '/' . basename($news->getPicture());
                if (file_exists($existingPicturePath)) {
                    unlink($existingPicturePath);
                }
            }
            
            $filename = $this->fileUploader->uploader($pictureFile);
            $pictureFile->move($this->newsFilesDirectory, $filename);
            
            $news->setPicture('news/' . $filename);
        }
    }
}
