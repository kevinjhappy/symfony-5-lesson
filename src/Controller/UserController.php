<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


class UserController extends AbstractController
{
    private $userRepository;
    private $eventDispatcher;

    public function __construct(UserRepository $userRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/user_list", name="user_list")
     * @IsGranted("ROLE_USER")
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
    public function newAction(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // objet User rempli avec les infos du formulaire
//            $userData = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            // pas besoin de cette ligne si on injecte ENtityManagerInterface en dépendance
//            $entityManager = $this->getDoctrine()->getManager();
            // dire à Doctrine que cet objet est nouveau
            $entityManager->persist($user);
            // enregistrer les nouveaux objets et object modifié en base de donnée
            $entityManager->flush();
            $userRegisteredEvent = new UserRegisteredEvent($user);
            $this->eventDispatcher->dispatch($userRegisteredEvent);

            return $this->redirectToRoute('user_list');

        }
        return $this->render('user/new-2.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}
