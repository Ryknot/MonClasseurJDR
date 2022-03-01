<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new User();

        //init dateRegister user
        $user->setDateRegister(new \DateTime());

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $pass1 = $form->get('plainPassword')->getData();
        $pass2 = $form->get('retape_password')->getData();

        //validation retapePassword
        $boolean = false;
        dump($boolean);
        $boolean = $this->retapePasswordValid($pass1, $pass2);
        dump($boolean);

        if ($form->isSubmitted() && $form->isValid() && $boolean == true) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function retapePasswordValid($pass1, $pass2): bool
    {
        dump($pass1);
        dump($pass2);
        if ($pass1 == $pass2) {
            return true;
        } else {
            return false;
        }
    }

}
