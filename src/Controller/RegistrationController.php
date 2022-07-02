<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Service\MailerService;

class RegistrationController extends AbstractController
{
    private $mailerService;
    private $logService;

    public function __construct(MailerService $mailerService, LogService $logService)
    {
        $this->mailerService = $mailerService;
        $this->logService = $logService;
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new User();

        //init user's data not in form
        $user->setDateRegister(new \DateTime());
        $user->setDateConnection(new \DateTime());
        $user->setActive(true);
        $user->setValidated(false);
        $user->setCodeValidation(rand(100001, 999999));

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        //validation retapePassword
        $pass1 = $form->get('plainPassword')->getData();
        $pass2 = $form->get('retape_password')->getData();
        $passwordIsValid = $this->retapePasswordValid($pass1, $pass2);

        $inscriptionOk = false;

        if ($form->isSubmitted() && $form->isValid() && $passwordIsValid == true) {
            try {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setPseudo($form->get('pseudo')->getData());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $inscriptionOk = true;

            }catch (\Exception $e){
                $this->addFlash('warning', "Une erreur est survenu pendant la création du compte. Veuillez contacter un administrateur.");
                $this->logService->newLogError($e->getMessage());
            }

            if ($inscriptionOk == true){
                //envoi mail avec le code d'activation.
                $content = $this->renderView(
                    'email/emailCodeValidation.html.twig',[
                    'userPseudo' => $user->getPseudo(),
                    'codeValidation' => $user->getCodeValidation(),
                ]);
                $this->mailerService->sendMailCodeActivation($user->getEmail(), $content);
                $log = $this->logService->newLogCreate($user->getEmail());
                $entityManager->persist($log);
                $entityManager->flush();

                $this->addFlash('success', "Félicitation " .  $form->get('pseudo')->getData() . ", votre compte a été créé. Veuillez saisir le code de validation envoyé par email pour finaliser l'inscription. (Pensez à regarder dans les spams!) ");

                $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'); // firewall name in security.yaml

                //redirige vers la page de validation du user
                return $this->redirectToRoute('app_register_validation', [
                    'id' => $user->getId(),
                ]);
            }

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function retapePasswordValid($pass1, $pass2): bool
    {
        if ($pass1 == $pass2) {
            return true;
        } else {
            $this->addFlash('warning', "Les deux champs mots de passe ne sont pas identiques");
            return false;
        }
    }

    /**
     * @Route("/register/validation/{id}", name="app_register_validation")
     */
    public function validationUser(int $id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->find($id);

        if ($user->getCodeValidation()) {

            if ( $request->getMethod() == "POST" && $request->get('codeValidation') && strlen($request->get('codeValidation')) == 6 ){
                //check codeValidation
                if($user->getCodeValidation() === $request->get('codeValidation')){
                    $user->setValidated(true);
                    $user->setRoles([]);
                    $user->setRoles(["ROLE_USER"]);
                    $entityManager->flush();

                    //envoi mail confiramtion validation.
                    $content = $this->renderView(
                        'email/emailUserValidated.html.twig',[
                        'userPseudo' => $user->getPseudo(),
                    ]);
                    $this->mailerService->sendUserValidated($user->getEmail(), $content);

                    //new log
                    $log = $this->logService->newLogValidate($user->getEmail());
                    $entityManager->persist($log);
                    $entityManager->flush();
                    $this->addFlash('success', "Félicitation " . $user->getPseudo() ." ! Votre compte a été validé.");
                    return $this->render('main/home.html.twig');
                }
                else{
                    $this->addFlash('warning', "erreur code");
                }
            }

        } else {
            $this->addFlash('warning', "Vous n'avez pas de code de validation, veuillez contacter un administrateur.");
            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/validationUser.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/register/activation/{id}", name="app_register_activation")
     */
    public function activationUser(int $id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->find($id);

        if ($user->getCodeValidation()) {

            if ( $request->getMethod() == "POST" && $request->get('codeActivation') && strlen($request->get('codeActivation')) == 6 ){
                //check codeValidation
                if($user->getCodeValidation() === $request->get('codeActivation')){

                    $user->setActive(true);
                    $entityManager->flush();

                    //envoi mail confirmation activation.
                    $content = $this->renderView(
                        'email/emailUserActivated.html.twig',[
                        'userPseudo' => $user->getPseudo(),
                    ]);
                    $this->mailerService->sendUserValidated($user->getEmail(), $content);

                    //new log
                    $log = $this->logService->newLogValidate($user->getEmail());
                    $entityManager->persist($log);
                    $entityManager->flush();
                    $this->addFlash('success', "Félicitation " . $user->getPseudo() ." ! Votre compte a été activé.");
                    return $this->render('main/home.html.twig');
                }
                else{
                    $this->addFlash('warning', "erreur code");
                }
            }

        } else {
            $this->addFlash('warning', "Vous n'avez pas de code d'activation', veuillez contacter un administrateur.");
            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/activationUser.html.twig', [
            'user' => $user,
        ]);
    }

}
