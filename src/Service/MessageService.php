<?php

namespace App\Service;


use App\Entity\Log;
use App\Entity\Message;

class MessageService extends GlobalService

{
    public function checkLogsLenght($messages, $MESSAGES_MAX_COUNT, $MARGE_MESSAGES_MAX){
        $deleteCount = 0;
        for ($i = 0; $i < (count($messages)-$MESSAGES_MAX_COUNT+$MARGE_MESSAGES_MAX); $i++)
        {
            $deleteCount++;
            $this->entityManager->remove($messages[$i]);
        }
        $log = new Log();
        $log->setType("message");
        $log->setDate(new \DateTime());
        $log->setMessage("Nettoyage auto: " . $deleteCount . " messages supprimés");
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }


    public function newMessageDeleteUser($email, $admin){
        $message = new Message();

        $message->setType("support");
        $message->setDate(new \DateTime());
        $message->setContent("Suppression du compte " . $email);
        $message->setUser($admin);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

}