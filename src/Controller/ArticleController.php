<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

///**
// * @Route("/article")
// */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),

        ]);
    }

    /**
     * @Route("/flows/{id}", name="flow_show", methods={"GET"})
     */
    public function flowShow(Category $category): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $category->getArticles()
        ]);
    }
    /**
     * @Route("/tags/{id}", name="tags_show", methods={"GET"})
     */
    public function showTags (Tag $tag): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $tag->getArticles()
        ]);

    }

    /**
     * @Route@Route("/article/{id}", name="comment_show", methods={"POST"})

     */
    public function CommentShow(Comment $comment): Response
    {
        return $this->render('article/show.html.twig', [
            'comment' => $comment,
        ]);
    }
    /**
     * @Route("/admin/article/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show", methods={"GET","POST"})
     */
    public function show(Article $article): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }

}
