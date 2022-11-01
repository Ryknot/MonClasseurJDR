<?php

namespace App\Controller;

use App\Repository\CarteMJRepository;
use App\Repository\UserRepository;
use App\Service\GlobalService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security(" (user.getActive() == true && user.getValidated() == true) || is_granted('ROLE_ADMIN') ")
 */
class InterfaceMJController extends AbstractController
{
    /**
     * @Route("/interfacemj/{id}", name="interfaceMJ")
     */
    public function affichageInterfaceMJ(int $id, UserRepository $userRepository, CarteMJRepository $carteMJRepository, GlobalService $globalService): Response
    {
        $user = $userRepository->find($id);

        //TODO: gestion stockage image
        $globalService->checkFileExistImageCarte($user);

        $cartesMJ = $carteMJRepository->findBy(['user' => $user],['nom' => 'ASC']);

        return $this->render('interface_mj/interfaceMJ.html.twig', [
            'user'=>$user,
            'cartesMJ'=>$cartesMJ,
        ]);
    }


}
