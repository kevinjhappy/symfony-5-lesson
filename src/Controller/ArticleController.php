<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     */
    public function newAction(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_articles');

        }
        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
