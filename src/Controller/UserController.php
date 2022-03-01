<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function profil(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $listeFiche = $user->getFichePersos();

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'listeFiche' => $listeFiche
        ]);
    }
}
