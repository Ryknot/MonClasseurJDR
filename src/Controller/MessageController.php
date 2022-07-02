<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageFormType;
use App\Repository\UserRepository;
use App\Service\LogService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security(" is_granted('ROLE_NOTVALIDATED') || is_granted('ROLE_USER') || is_granted('ROLE_ADMIN') ")
 */
class MessageController extends AbstractController
{
    private $securityService;
    private $logService;

    public function __construct(SecurityService $securityService, LogService $logService)
    {
        $this->securityService = $securityService;
        $this->logService = $logService;
    }

    /**
     * @Route("/profil/{id}/message/create", name="message_create")
     */
    public function createMessage(int $id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        //check si admin ou bon user
        if ($this->getUser() == $user || $this->isGranted("ROLE_ADMIN"))
        {
            $message = new Message();
            $message->setUser($user->getEmail());
            $message->setDate(new \DateTime());

            $form = $this->createForm(MessageFormType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                try{
                    $entityManager->persist($message);
                    $entityManager->flush();

                    $this->addFlash('success', 'Message envoyé ! Merci pour ce retour d\'information.');
                    $log = $this->logService->newLogMessage($user->getEmail(), $message->getType());
                    $entityManager->persist($log);
                    $entityManager->flush();

                    return $this->render('message/createMessage.html.twig', [
                        'user' => $user,
                        'messageForm' => $form->createView()
                    ]);
                }
                catch (\Exception $e)
                {
                    $this->addFlash('warning', 'Erreur création du message ! '.$e->getMessage());
                }
            }

            return $this->render('message/createMessage.html.twig', [
                'user' => $user,
                'messageForm' => $form->createView()
            ]);
        }
        else
        {
            //identification mauvais user + logMenace + alert + checkMenace + redirection
            $realUser = $this->securityService->findRealUser();

            return $this->redirectToRoute('message_create',[
                'id' => $realUser->getId(),
            ]);
        }
    }
}
