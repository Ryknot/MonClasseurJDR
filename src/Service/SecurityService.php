<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class SecurityService extends GlobalService
{
    private $logService;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, LogService $logService, UserRepository $userRepository)
    {
        parent::__construct($entityManager);
        $this->logService = $logService;
        $this->userRepository = $userRepository;
    }

    public function findRealUser()
    {
        $realUser = $this->userRepository->find($this->getUser());
        $log = $this->logService->newLogMenace("tentative accès autre compte par " . $realUser->getEmail());
        $this->addFlash('danger', 'Vous avez été redirigé vers VOTRE compte utilisateur, un message a été envoyé à un administrateur. Faîtes attention la prochaine fois !!');
        $this->entityManager->persist($log);
        $this->entityManager->flush();
        return $realUser;
    }

}