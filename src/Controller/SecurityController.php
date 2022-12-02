<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/checkemail", name="user_checkEmail")
     */
    public function profilCheckEmail(UserRepository $userRepository, EntityManagerInterface $entityManager, LogService $logService): Response
    {
        $postEmail = "";

        //Formulaire email
        if ($_POST && $_POST['postEmail'] == "postEmail" && $_POST['email'] != "")
        {
            try {
                $email = $_POST['email'];

                //recherche user suivant adresse email saisie
                $user = $userRepository->findOneBy(['email' => $email]);

                if($user != null)
                {
                    return $this->redirectToRoute('user_profilUpdatePassword', [
                        'id' => $user->getId(),
                    ]);
                }
                else
                {
                    $this->addFlash('error', "Aucun compte lié à cet email.");
                }
            }
            catch (\Exception $e)
            {
                $this->addFlash('warning', "Erreur traitement des informations. ");
                $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                $entityManager->persist($log);
                $entityManager->flush();
            }
        }

        return $this->render('user/profilCheckEmail.html.twig', [
            
        ]);
    }
}
