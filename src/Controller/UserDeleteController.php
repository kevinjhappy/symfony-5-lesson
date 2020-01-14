<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserDeleteController extends AbstractController
{
    /**
     * @Route("/user/delete", name="user_delete")
     */
    public function index()
    {
        return $this->render('user_delete/index.html.twig', [
            'controller_name' => 'UserDeleteController',
        ]);
    }
}
