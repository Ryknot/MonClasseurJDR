<?php

namespace App\Service;

use App\Entity\Log;
use App\Entity\User;

class LogService extends GlobalService

{
    public function checkLogsLenght($logs, $LOGS_MAX_COUNT, $MARGE_LOGS_MAX){
        $deleteCount = 0;
        for ($i = 0; $i < (count($logs)-$LOGS_MAX_COUNT+$MARGE_LOGS_MAX); $i++)
        {
            $deleteCount++;
            $this->entityManager->remove($logs[$i]);
        }
        $log = new Log();
        $log->setType("message");
        $log->setDate(new \DateTime());
        $log->setMessage("Nettoyage auto: " . $deleteCount . " logs supprimés");
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }


    public function newLogConnect($email){
        $log = new Log();

        $log->setType("connexion");
        $log->setDate(new \DateTime());
        if($email != null){
            $log->setMessage("Connexion de " . $email);
        }
        else
        {
            $log->setMessage("Nouvelle connexion !!");
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$email]);
        if ($user){
            $user->setDateConnection(new \DateTime());
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function newLogCreate($email){
        $log = new Log();

        $log->setType("creation");
        $log->setDate(new \DateTime());
        $log->setMessage("Création du compte " . $email);

        return $log;
    }

    public function newLogValidate($email){
        $log = new Log();

        $log->setType("validation");
        $log->setDate(new \DateTime());
        $log->setMessage("Validation du compte " . $email);

        return $log;
    }

    public function newLogMessage($email, $type){
        $log = new Log();

        $log->setType("message");
        $log->setDate(new \DateTime());
        $log->setMessage("Nouveau message " . $type . " de " . $email);

        return $log;
    }

    public function newLogError($error){
        $log = new Log();

        $log->setType("erreur");
        $log->setDate(new \DateTime());
        $log->setMessage($error);

        return $log;
    }

    public function newLogMenace($menace){
        $log = new Log();

        $log->setType("menace");
        $log->setDate(new \DateTime());
        $log->setMessage($menace);

        return $log;
    }

    public function newLogDeleteUser($email){
        $log = new Log();

        $log->setType("suppression");
        $log->setDate(new \DateTime());
        $log->setMessage("Suppression du compte " . $email);

        return $log;
    }

    public function newLogMailValidation($email){
        $log = new Log();

        $log->setType("validation");
        $log->setDate(new \DateTime());
        $log->setMessage("Envoi par mail d'un nouveau code de validation pour " . $email);

        return $log;
    }

    public function newLogMailContactUser($email){
        $log = new Log();

        $log->setType("message");
        $log->setDate(new \DateTime());
        $log->setMessage("Envoi d'un mail de contact pour " . $email);

        return $log;
    }

}