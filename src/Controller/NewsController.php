<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Category;
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
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Fetch categories for the dropdown
        $categories = $em->getRepository(Category::class)->findAll();
        
        $errors = [];
        
        // Handle form submission
        if ($request->isMethod('POST')) {
            // Validate CSRF token
            $submittedToken = $request->request->get('token');
            if (!$this->isCsrfTokenValid('news_form', $submittedToken)) {
                $errors[] = 'Invalid CSRF token. Please try again.';
            } else {
                // Create a new News entity
                $news = new News();
                
                // Handle form data
                $newsData = $request->request->get('news');
                
                // Set basic fields
                $news->setTitle($newsData['title'] ?? '');
                $news->setDescription($newsData['description'] ?? '');
                $news->setContent($newsData['content'] ?? '');
                $news->setInsertDate(new \DateTime());
                
                // Handle file upload
                $pictureFile = $request->files->get('news')['picture'] ?? null;
                if ($pictureFile) {
                    $newFilename = 'news-'.uniqid().'.'.$pictureFile->guessExtension();
                    
                    try {
                        $pictureFile->move(
                            $this->getParameter('news_pictures_directory'),
                            $newFilename
                        );
                        $news->setPicture('uploads/news/'.$newFilename);
                    } catch (FileException $e) {
                        $errors[] = 'Error uploading file: ' . $e->getMessage();
                    }
                }
                
                // Handle categories
                if (isset($newsData['categories']) && is_array($newsData['categories'])) {
                    foreach ($newsData['categories'] as $categoryId) {
                        $category = $em->getRepository(Category::class)->find($categoryId);
                        if ($category) {
                            $news->addCategory($category);
                        }
                    }
                }
                
                // Validate the entity
                $validator = $this->container->get('validator');
                $validationErrors = $validator->validate($news);
                
                if (count($validationErrors) === 0 && count($errors) === 0) {
                    // Save to database
                    $em->persist($news);
                    $em->flush();
                    
                    $this->addFlash('success', 'News created successfully!');
                    return $this->redirectToRoute('news_details', ['id' => $news->getId()]);
                } else {
                    // Add validation errors
                    foreach ($validationErrors as $error) {
                        $errors[] = $error->getMessage();
                    }
                }
            }
        }
        
        return $this->render('news/new.html.twig', [
            'categories' => $categories,
            'errors' => $errors
        ]);
    }
}
