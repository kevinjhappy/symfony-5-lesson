<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user_list", name="user_list")
     */
    public function index()
    {
        $userList = $this->userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'userList' => $userList,
        ]);
    }

    /**
     * @Route("/user-create", name="user-create")
     */
    public function newAction(Request $request, EntityManagerInterface $entityManager)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // objet User rempli avec les infos du formulaire
            $userData = $form->getData();
            // pas besoin de cette ligne si on injecte ENtityManagerInterface en dépendance
//            $entityManager = $this->getDoctrine()->getManager();
            // dire à Doctrine que cet objet est nouveau
            $entityManager->persist($userData);
            // enregistrer les nouveaux objets et object modifié en base de donnée
            $entityManager->flush();

            return $this->redirectToRoute('user');

        }
        return $this->render('user/new-2.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}
