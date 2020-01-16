<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/articles")
 */
class ArticleController extends AbstractController
{
    private $articleRepository;
    private $entityManager;

    public function __construct(
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="list_articles", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        // $entityManager = $this->getDoctrine()->getManager();

        $articles = $this->articleRepository->findAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/create", name="article_create", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function newAction(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTime());
            $this->entityManager->persist($article);
            $this->entityManager->flush();
            $this->addFlash('notice', "L'article a bien été créé");

            return $this->redirectToRoute('list_articles');
        }
        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete-bis/{id}", name="article_delete_bis")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteBis(string $id, EntityManagerInterface $entityManager)
    {
        $article = $this->articleRepository->find($id);
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('list_articles');
    }

    /**
     * @Route("/delete/{id}", name="article_delete")
     * @ParamConverter("article", options={"mapping"={"id"="id"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Article $article, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('list_articles');
    }

    /**
     * @Route("/edit/{id}", name="article_edit")
     * @ParamConverter("article", options={"mapping"={"id"="id"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();
            $this->addFlash('notice', "L'article a bien été modifié");

            return $this->redirectToRoute('list_articles');
        }
        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
