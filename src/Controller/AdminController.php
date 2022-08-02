<?php

namespace App\Controller;

use App\Repository\LogRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\LogService;
use App\Service\MailerService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function dashboard(UserRepository $userRepository, LogRepository $logRepository, MessageRepository $messageRepository): Response
    {

        // nombre de users actifs
        $activeUsers = $userRepository->findActiveUsers();

        //nombre de users non validés
        $notValidatedUsers = $userRepository->findBy(['validated' => false]);

        //nombre total de fiches créées
        $nbTotFiches = 0;
        $nbTotCartesMJ = 0;
        $users = $userRepository->findAll();
        foreach ($users as $user)
        {
            $nbTotFiches = $nbTotFiches + count($user->getFichePersos());
            $nbTotCartesMJ = $nbTotCartesMJ + count($user->getCartesMJ());
        }

        //nombre d'admin
        $admins = $userRepository->findAdmin();

        //10 derniers messages
        $lastMessages = $messageRepository->findBy([], ['id' => 'DESC'], 10);

        //10 derniers logs
        $lastLogs = $logRepository->findBy([], ['id' => 'DESC'], 10);

        //----------------- test ---------------------
        $lien = $this->getParameter('images_directory');
        //---------------------------------------------

        return $this->render('admin/adminDashboard.html.twig', [
            'activeUsers' => $activeUsers,
            'admins' => $admins,
            'notValidatedUsers' => $notValidatedUsers,
            'nbTotFiches' => $nbTotFiches,
            'nbTotCartesMJ' => $nbTotCartesMJ,
            'lastLogs' => $lastLogs,
            'lastMessages' => $lastMessages,
            'lien' => $lien
        ]);
    }

    /**
     * @Route("/admin/usersList", name="admin_usersList")
     */
    public function usersList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/message", name="admin_message")
     */
    public function message(MessageRepository $messageRepository, MessageService $messageService): Response
    {
        $MESSAGES_MAX_COUNT = 200;
        $MARGE_MESSAGES_MAX = 50;

        //check nb total logs pour supprimer si 200+
        $messages = $messageRepository->findBy([], ['id' => 'ASC']);
        if(count($messages) > $MESSAGES_MAX_COUNT && (count($messages)-$MESSAGES_MAX_COUNT+$MARGE_MESSAGES_MAX) < count($messages))
        {
            $messageService->checkLogsLenght($messages, $MESSAGES_MAX_COUNT,$MARGE_MESSAGES_MAX);
        }
        $messages = $messageRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/adminMessages.html.twig', [
            'messages' => $messages,
            'MESSAGES_MAX_COUNT' => $MESSAGES_MAX_COUNT,
        ]);
    }

    /**
     * @Route("/admin/log", name="admin_log")
     */
    public function log(LogRepository $logRepository, LogService $logService): Response
    {
        $LOGS_MAX_COUNT = 200;
        $MARGE_LOGS_MAX = 50;

        //check nb total logs pour supprimer si 200+
        $logs = $logRepository->findBy([], ['id' => 'ASC']);
        if(count($logs) > $LOGS_MAX_COUNT && (count($logs)-$LOGS_MAX_COUNT+$MARGE_LOGS_MAX) < count($logs))
        {
            $logService->checkLogsLenght($logs, $LOGS_MAX_COUNT,$MARGE_LOGS_MAX);
        }

        $logs = $logRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/adminLogs.html.twig', [
            'logs' => $logs,
            'LOGS_MAX_COUNT' => $LOGS_MAX_COUNT,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/delete", name="admin_userDelete")
     */
    public function userDelete(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, LogService $logService, MessageService $messageService): Response
    {
        $user = $userRepository->find($id);

        if ($user)
        {
            try {
                //JOUEUR - suppression des champs/ressources/image de chaque fiche.
                $fiches = $user->getFichePersos();
                foreach ($fiches as $fiche)
                {

                    $champs = $fiche->getChamps();
                    $ressources = $fiche->getRessources();
                    //suppression des champs
                    foreach ($champs as $champ){
                        $fiche->removeChamp($champ);
                    }
                    //suppression des ressources
                    foreach ($ressources as $ressource){
                        $fiche->removeRessource($ressource);
                    }
                    //suppression du fichier image
                    $image = $fiche->getImage();
                    if ($image && $image != "icon_d20_mini.png" && file_exists($this->getParameter('images_directory') . '/' . $image))
                    {
                        //suppression du fichier de l'image
                        unlink($this->getParameter('images_directory') . '/' . $image);
                        $fiche->setImage("");
                    }
                    $entityManager->remove($fiche);
                }
                $this->addFlash('success', "Fiches JOUEUR supprimées avec succès.");

                //MJ - suppression des cartes MJ et leur image
                $cartesMJ = $user->getCartesMJ();
                foreach ($cartesMJ as $carte)
                {
                    $image = $carte->getImage();
                    if ($image && $image != "icon_d20_mini.png" && file_exists($this->getParameter('images_directory') . '/' . $image)) {
                        //suppression du fichier de l'image
                        unlink($this->getParameter('images_directory') . '/' . $image);
                        $carte->setImage("");
                    }
                    $user->removeCartesMJ($carte);
                }
                $this->addFlash('success', "Cartes MJ supprimées avec succès.");

                //message delete user
                $messageService->newMessageDeleteUser($user->getEmail(), $this->getUser());
                //log delete user
                $log = $logService->newLogDeleteUser($user->getEmail());

                $entityManager->remove($user);
                $this->addFlash('success', "User supprimé avec succès.");
            }
            catch (\Exception $e){
                $this->addFlash('warning', "Erreur pendant la suppression du user.");
                $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            }
            $entityManager->persist($log);
            $entityManager->flush();
        }

        $users = $userRepository->findAll();
        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/log/{id}/delete", name="admin_logDelete")
     */
    public function logDelete(int $id, LogRepository $logRepository, LogService $logService, EntityManagerInterface $entityManager): Response
    {
        try {
            $log = $logRepository->find($id);
            if ($log)
            {
                $entityManager->remove($log);
                $entityManager->flush();
                $this->addFlash('success', "Suppression du log réussie.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur suppression du log. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);
            $entityManager->flush();
        }

        $logs = $logRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/adminLogs.html.twig', [
            'logs' => $logs,
        ]);
    }

    /**
     * @Route("/admin/message/{id}/delete", name="admin_messageDelete")
     */
    public function messageDelete(int $id, MessageRepository $messageRepository, LogService $logService, EntityManagerInterface $entityManager): Response
    {
        try {
            $message = $messageRepository->find($id);
            if ($message)
            {
                $entityManager->remove($message);
                $entityManager->flush();
                $this->addFlash('success', "Suppression du message réussie.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur suppression du message. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);
            $entityManager->flush();
        }

        $messages = $messageRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/adminMessages.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/validated", name="admin_validatedUser")
     */
    public function validatedUser(int $id, UserRepository $userRepository, LogService $logService, EntityManagerInterface $entityManager): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user)
            {
                $user->setValidated(true);
                $entityManager->flush();
                $this->addFlash('success', "Validation du compte du user réussie.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur validation du compte du user. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);
            $entityManager->flush();
        }

        $users = $userRepository->findAll();
        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/unvalidated", name="admin_unvalidatedUser")
     */
    public function unvalidatedUser(int $id, UserRepository $userRepository, LogService $logService, EntityManagerInterface $entityManager): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user)
            {
                $user->setValidated(false);
                $codeValidation = $user->getCodeValidation();
                if ($codeValidation)
                {
                    $user->setLastCodeValidation($codeValidation);
                }
                $user->setCodeValidation("");
                $entityManager->flush();
                $this->addFlash('success', "Annulation de la validité du compte du user réussie.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur annulation de la validité du compte du user. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);
            $entityManager->flush();
        }

        $users = $userRepository->findAll();
        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/active", name="admin_activeUser")
     */
    public function activeUser(int $id, UserRepository $userRepository, LogService $logService, EntityManagerInterface $entityManager): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user)
            {
                $user->setActive(true);
                $entityManager->flush();
                $this->addFlash('success', "Activation du compte du user réussie.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur activation du compte du user. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);
            $entityManager->flush();
        }

        $users = $userRepository->findAll();
        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/desactive", name="admin_desactiveUser")
     */
    public function desactiveUser(int $id, UserRepository $userRepository, LogService $logService, EntityManagerInterface $entityManager): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user)
            {
                $user->setActive(false);
                $codeValidation = $user->getCodeValidation();
                if ($codeValidation)
                {
                    $user->setLastCodeValidation($codeValidation);
                }
                $user->setCodeValidation("");
                $entityManager->flush();
                $this->addFlash('success', "Désactivation du compte du user réussie.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur désactivation du compte du user. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);
            $entityManager->flush();
        }

        $users = $userRepository->findAll();
        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/mailCodeValidation", name="admin_mailCodeValidation")
     */
    public function mailCodeValidation(int $id, UserRepository $userRepository, LogService $logService, MailerService $mailerService, EntityManagerInterface $entityManager): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user && $user->getCodeValidation() === "")
            {
                $user->setCodeValidation(rand(100001, 999999));

                if ($user->getCodeValidation() != "" && strlen($user->getCodeValidation()) == 6)
                {
                    //envoi mail avec le nouveau code de validation.
                    $content = $this->renderView(
                        'email/emailNewCodeValidation.html.twig',[
                        'userPseudo' => $user->getPseudo(),
                        'codeValidation' => $user->getCodeValidation(),
                    ]);
                    $mailerService->sendMailCodeActivation($user->getEmail(), $content);
                    $log = $logService->newLogMailValidation($user->getEmail());
                    $entityManager->persist($log);
                    $this->addFlash('success', "Envoi du mail nouveau code réussie.");
                }
            }
            else
            {
                $this->addFlash('warning', "Le mail n'a pas été envoyé, ce user possède déja un code de validation.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur envoi du mail nouveau code. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);

        }
        $entityManager->flush();

        $users = $userRepository->findAll();
        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/mailContact", name="admin_mailContactUser")
     */
    public function mailContactUser(int $id, UserRepository $userRepository, LogService $logService, MailerService $mailerService, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if ($_POST && $_POST['message'] != "")
        {
            try {

                if ($user)
                {
                    //envoi mail avec le nouveau code de validation.
                    $content = $this->renderView(
                        'email/emailContactUser.html.twig',[
                        'userPseudo' => $user->getPseudo(),
                        'message' => $_POST['message'],
                    ]);
                    $mailerService->sendMailContactUser($user->getEmail(), $content);
                    $log = $logService->newLogMailContactUser($user->getEmail());
                    $entityManager->persist($log);
                    $entityManager->flush();

                    $this->addFlash('success', "Envoie du mail de contact user réussie.");
                    $users = $userRepository->findAll();
                    return $this->render('admin/adminListUsers.html.twig', [
                        'users' => $users,
                    ]);
                }
            }
            catch (\Exception $e)
            {
                $this->addFlash('warning', "Erreur envoie du mail de contact user. ");
                $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                $entityManager->persist($log);
                $entityManager->flush();
            }

        }
        else if($_POST && $_POST['message'] == "")
        {
            $this->addFlash('warning', "Veuillez saisir un message à envoyer par mail au user. ");
        }
        else
        {
            $this->addFlash('info', "Veuillez saisir un message à envoyer par mail au user. ");
        }

        $users = $userRepository->findAll();
        return $this->render('admin/adminContactUser.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/mailResendCodeValidation", name="admin_mailResendCodeValidation")
     */
    public function mailResendCodeValidation(int $id, UserRepository $userRepository, LogService $logService, MailerService $mailerService, EntityManagerInterface $entityManager): Response
    {
        try {
            $user = $userRepository->find($id);
            if ($user && $user->getCodeValidation() != "" && strlen($user->getCodeValidation()) == 6)
            {
                //envoi mail avec le nouveau code de validation.
                $content = $this->renderView(
                    'email/emailResendCodeValidation.html.twig',[
                    'userPseudo' => $user->getPseudo(),
                    'codeValidation' => $user->getCodeValidation(),
                ]);
                $mailerService->sendMailCodeActivation($user->getEmail(), $content);
                $log = $logService->newLogMailValidation($user->getEmail());
                $entityManager->persist($log);
                $this->addFlash('success', "Envoi par mail du code de Validation réussie.");
            }
            else
            {
                $this->addFlash('warning', "Erreur, le user n'a pas de code de validation.");
            }
        }
        catch (\Exception $e)
        {
            $this->addFlash('warning', "Erreur d'envoie par mail du code de Validation. ");
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);

        }
        $entityManager->flush();

        $users = $userRepository->findAll();
        return $this->render('admin/adminListUsers.html.twig', [
            'users' => $users,
        ]);
    }

}
