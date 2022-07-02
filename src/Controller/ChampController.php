<?php

namespace App\Controller;

use App\Repository\ChampsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Security(" (user.getActive() == true && user.getValidated() == true) || is_granted('ROLE_ADMIN') ")
 */
class ChampController extends AbstractController
{
    /**
     * @Route("/champ/update/{id}/{newValeur}/{page}", name="champ_update")
     */
    public function updateChamp(int $id, string $newValeur, string $page, ChampsRepository $champsRepository): Response
    {
        //récupération du champ et de la fiche perso
        $champ = $champsRepository->find($id);
        $id = $champ->getFichePerso()->getId();

        //modification de la valeur du champ
        try {
            if($champ->getValeurTexte() != null){
                $champ->setValeurTexte($newValeur);
            }
            else{
                //$champ->setValeurArea($newValeur);
                //$champ->setValeurArea(preg_replace('(\$)', '/\n/g', $newValeur));
                $champ->setValeurArea(str_replace('¤', PHP_EOL, $newValeur));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Modification du champ réussie !');
        }catch(\Exception $e){
            $this->addFlash('warning', 'Erreur modification du champ : '.$e->getMessage());
        }

        //redirection vers la page de provenance: listeDetails ou update
        if ($page == "update"){
            return $this->redirectToRoute('fiche_update', [
                'id' => $id
            ]);
        }
        else{
            return $this->redirectToRoute('fiche_detail', [
                'id' => $id
            ]);
        }
    }

    /**
     * @Route("/champ/delete/{id}", name="champ_delete")
     */
    public function deleteChamp(int $id, ChampsRepository $champsRepository): Response
    {
        $champ = $champsRepository->find($id);
        $idFiche = $champ->getFichePerso()->getId();
        $fiche = $champ->getFichePerso();

        try {
            //maj sort de chaque champ après celui supprimé
            $champs = $champsRepository->findBy(['fichePerso' => $idFiche, 'typeInfo' => $champ->getTypeInfo()],['sort' => 'ASC']);
            for ($i = ($champ->getSort()); $i < (count($champs)-1); $i++ ) {
                $champs[$i+1]->setSort($i);
            }

            //gestion de l'ordre des champs
            $info = $champ->getTypeInfo()->getId();
            $champsParInfo = $champsRepository->findBy(['fichePerso' => $idFiche, 'typeInfo' => $info]);
            $countChamps = count($champsParInfo);

            //mise a jour du nb de champs de ce type d'info
            $erreurNbChamps=false;
            switch ($info){
                case 1 : $fiche->setNbChamps1($countChamps-1);break;
                case 2 : $fiche->setNbChamps2($countChamps-1);break;
                case 3 : $fiche->setNbChamps3($countChamps-1);break;
                case 4 : $fiche->setNbChamps4($countChamps-1);break;
                case 5 : $fiche->setNbChamps5($countChamps-1);break;
                case 6 : $fiche->setNbChamps6($countChamps-1);break;
                case 7 : $fiche->setNbChamps7($countChamps-1);break;
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($champ);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression du champ réussie !');
        }catch(\Exception $e){
            $this->addFlash('warning', 'Erreur suppression du champ !');
        }

         return $this->redirectToRoute('fiche_update', [
            'id' => $idFiche
         ]);
    }


    /**
     * @Route("/champ/previous/{id}", name="champ_previous")
     */
    public function buttonPrevious(int $id, ChampsRepository $champsRepository)
    {
        $champ = $champsRepository->find($id);
        $idFiche = $champ->getFichePerso()->getId();
        $champs = $champsRepository->findBy(['fichePerso' => $idFiche, 'typeInfo' => $champ->getTypeInfo()],['sort' => 'ASC']);
        $positionList = $champ->getSort();

        //échange de position
        if($positionList != 0){
            $champs[$positionList]->setSort($positionList-1);
            $champs[$positionList-1]->setSort($positionList);
        }

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', "Changement de l'ordre des champs effectué");
        }catch(\Exception $e){
            $this->addFlash('warning', "Erreur de changement de l'ordre des champs !");
        }

        return $this->redirectToRoute('fiche_update', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/champ/next/{id}", name="champ_next")
     */
    public function buttonNext(int $id, ChampsRepository $champsRepository)
    {
        $champ = $champsRepository->find($id);
        $idFiche = $champ->getFichePerso()->getId();
        $champs = $champsRepository->findBy(['fichePerso' => $idFiche, 'typeInfo' => $champ->getTypeInfo()],['sort' => 'ASC']);
        $positionList = $champ->getSort();

        //échange de position
        if($positionList != (count($champs)-1)){
            $champs[$positionList]->setSort($positionList+1);
            $champs[$positionList+1]->setSort($positionList);
        }

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', "Changement de l'ordre des champs effectué");
        }catch(\Exception $e){
            $this->addFlash('warning', "Erreur de changement de l'ordre des champs !");
        }

        return $this->redirectToRoute('fiche_update', [
            'id' => $id
        ]);
    }

}
