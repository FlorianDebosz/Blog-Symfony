<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    #[Route('/', name: "home")]
    public function home(): Response
    {
        return $this->render('blog/home.html.twig');
    }


    #[Route('/blog/new', name: "blog_create")]
    #[Route('blog/{id}/edit', name: "blog_edit")]
    public function form(Article $article = null, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$article) {
            $article = new Article();
        }

        // $form = $this->createFormBuilder($article)
        //     ->add('title')
        //     ->add('content')
        //     ->add('image')
        //     ->getForm();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form, //->createView() pas obligatoire
            'editMode' => $article->getId() !== null
        ]);
    }

    #[Route('/blog/{id}', name: "blog_show")]
    public function show(Article $article): Response
    {
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }
}
