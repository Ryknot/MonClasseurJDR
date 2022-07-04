<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\LogService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="user_profil")
     */
    public function profil(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        return $this->render('user/profil.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profil/{id}/update", name="user_profilUpdate")
     */
    public function profilUpdate(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, LogService $logService, MailerService $mailerService): Response
    {
        $user = $userRepository->find($id);
        $post = false;
        $postPseudo = $user->getPseudo();
        $postEmail = $user->getEmail();

        if ($_POST && $_POST['post'] == "post1" && $_POST['pseudo'] != "" && $_POST['email'] != "")
        {
            try {
                if ($_POST['pseudo'] != $user->getPseudo())
                {
                    $postPseudo = $_POST['pseudo'];
                    $post = true;
                    $this->addFlash('success', "Votre pseudo est en cours de modification. ");
                }
                if ($_POST['email'] != $user->getEmail())
                {
                    $postEmail = $_POST['email'];
                    $post = true;
                    $this->addFlash('success', "Votre email est en cours de modification. ");
                }
                if ($post == true)
                {
                    //nouveau code de validation
                    $user->setLastCodeValidation($user->getCodeValidation());
                    $user->setCodeValidation(rand(100001, 999999));

                    //envoi mail avec le nouveau code de validation.
                    $content = $this->renderView(
                        'email/emailResendCodeValidation.html.twig',[
                        'userPseudo' => $user->getPseudo(),
                        'codeValidation' => $user->getCodeValidation(),
                    ]);
                    $mailerService->sendMailCodeActivation($postEmail, $content);
                    $log = $logService->newLogMailValidation($postEmail);
                    $entityManager->persist($log);
                    $entityManager->flush();
                    $this->addFlash('success', "Un code de validation à 6 chiffres vous a été envoyé par mail. ");
                }
                else
                {
                    $this->addFlash('warning', "Aucune modification n'a été faite. ");
                }

                return $this->render('user/profilUpdate.html.twig', [
                    'user' => $user,
                    'post' => $post,
                    'postPseudo' => $postPseudo,
                    'postEmail' => $postEmail,
                ]);
            }catch (\Exception $e)
            {
                $this->addFlash('warning', "Erreur traitement des informations. ");
                $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                $entityManager->persist($log);
                $entityManager->flush();
            }
        }
        elseif($_POST && $_POST['post'] == "post2" && $_POST['postPseudo'] != "" && $_POST['postEmail'] != "")
        {
            if ($_POST['codeValidation'] == $user->getCodeValidation())
            {
                try {
                    if ($_POST['postPseudo'] != $user->getPseudo())
                    {
                        $user->setPseudo($_POST['postPseudo']);
                    }
                    if ($_POST['postEmail'] != $user->getEmail())
                    {
                        $user->setEmail($_POST['postEmail']);
                    }
                    $entityManager->flush();
                    $this->addFlash('success', "Votre profil a été modifié avec succès. ");
                    return $this->render('user/profil.html.twig', [
                        'user' => $user,
                    ]);

                }catch (\Exception $e) {
                    $this->addFlash('warning', "Erreur traitement du code validation. ");
                    $log = $logService->newLogError($e->getMessage() . "|| " . $e->getFile() . "||" . $e->getLine());
                    $entityManager->persist($log);
                    $entityManager->flush();
                }
            }
            else
            {
                $this->addFlash('warning', "Saisie du code erronée");
                return $this->render('user/profilUpdate.html.twig', [
                    'user' => $user,
                    'post' => $post,
                    'postPseudo' => $postPseudo,
                    'postEmail' => $postEmail,
                ]);
            }

        }


        return $this->render('user/profilUpdate.html.twig', [
            'user' => $user,
            'post' => $post,
        ]);
    }


    /**
     * @Route("/profil/{id}/updatePassword", name="user_profilUpdatePassword")
     */
    public function profilUpdatePassword(int $id, UserRepository $userRepository,UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, LogService $logService, MailerService $mailerService): Response
    {
        $user = $userRepository->find($id);
        $post = false;
        $postPswd1 = "";
        $postPswd2 = "";

        if ($_POST && $_POST['post'] == "post1" && $_POST['password1'] != "" && $_POST['password2'] != "")
        {
            if($_POST['password1'] === $_POST['password2'])
            {
                try {
                    $post = true;
                    $postPswd1 = $_POST['password1'];
                    $postPswd2 = $_POST['password2'];
                    //nouveau code de validation
                    $user->setLastCodeValidation($user->getCodeValidation());
                    $user->setCodeValidation(rand(100001, 999999));

                    //envoi mail avec le nouveau code de validation.
                    $content = $this->renderView(
                        'email/emailResendCodeValidation.html.twig',[
                        'userPseudo' => $user->getPseudo(),
                        'codeValidation' => $user->getCodeValidation(),
                    ]);
                    $mailerService->sendMailCodeActivation($user->getEmail(), $content);
                    $log = $logService->newLogMailValidation($user->getEmail());
                    $entityManager->persist($log);
                    $entityManager->flush();
                    $this->addFlash('success', "Un code de validation à 6 chiffres vous a été envoyé par mail. ");
                }
                catch (\Exception $e)
                {
                    $this->addFlash('warning', "Erreur traitement des informations. ");
                    $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                    $entityManager->persist($log);
                    $entityManager->flush();
                }
            }
            else
            {
                $this->addFlash('warning', "Les deux saisies ne sont pas identiques. ");
            }

            return $this->render('user/profilUpdatePassword.html.twig', [
                'user' => $user,
                'post' => $post,
                'postPswd1' => $postPswd1,
                'postPswd2' => $postPswd2,
            ]);

        }
        elseif($_POST && $_POST['post'] == "post2" && $_POST['postPswd1'] != "" && $_POST['postPswd2'] != "")
        {
            if ($_POST['codeValidation'] === $user->getCodeValidation())
            {
                try {
                    // encode the plain password
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $_POST['postPswd1']
                        )
                    );
                    $entityManager->flush();
                    $this->addFlash('success', "Votre mot de passe a été modifié avec succès. ");
                    return $this->render('user/profil.html.twig', [
                        'user' => $user,
                    ]);

                }catch (\Exception $e) {
                    $this->addFlash('warning', "Erreur traitement du code validation. ");
                    $log = $logService->newLogError($e->getMessage() . "|| " . $e->getFile() . "||" . $e->getLine());
                    $entityManager->persist($log);
                    $entityManager->flush();
                }
            }
            else
            {
                $this->addFlash('warning', "Saisie du code erronée");
                return $this->render('user/profilUpdatePassword.html.twig', [
                    'user' => $user,
                    'post' => $post,
                    'postPswd1' => $postPswd1,
                    'postPswd2' => $postPswd2,
                ]);
            }

        }


        return $this->render('user/profilUpdatePassword.html.twig', [
            'user' => $user,
            'post' => $post,
        ]);
    }
}
