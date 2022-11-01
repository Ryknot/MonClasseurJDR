<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GlobalService extends AbstractController
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkFileExistImageFiche($user)
    {
        $fiches = $user->getFichePersos();

        foreach($fiches as $fiche)
        {
            $image = $fiche->getImage();
            if ($image && $image != "icon_d20_mini.png" && !(file_exists($this->getParameter('images_directory') . '/' . $image)))
            {
                $fiche->setImage("icon_d20_mini.png");
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }
            
    }

    public function checkFileExistImageCarte($user)
    {
        $cartes = $user->getCartesMJ();

        foreach($cartes as $carte)
        {
            $image = $carte->getImage();
            if ($image && $image != "icon_d20_mini.png" && !(file_exists($this->getParameter('images_directory') . '/' . $image)))
            {
                $carte->setImage("icon_d20_mini.png");
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }
            
    }

}