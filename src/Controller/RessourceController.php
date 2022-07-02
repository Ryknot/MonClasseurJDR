<?php

namespace App\Controller;

use App\Repository\RessourceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security(" (user.getActive() == true && user.getValidated() == true) || is_granted('ROLE_ADMIN') ")
 */
class RessourceController extends AbstractController
{
    //*********  INFO *********

    //modification de la valeur glissante en AJAX

    //*************************

    /**
     * @Route("/ressource/updateRangeMax/{id}/{newRangeMax}/{page}", name="ressource_updateRangeMax")
     */
    public function UpdateRangeMaxRessource(int $id, int $newRangeMax, string $page, RessourceRepository $ressourceRepository): Response
    {
        $ressource = $ressourceRepository->find($id);
        $idFiche = $ressource->getFichePerso()->getId();
        $valeurGlissante = $ressource->getValeurGlissante();

        try {
            $ressource->setRangeMax($newRangeMax);

            //correction de la valeur glissante si supérieur à la range max
            if ($valeurGlissante > $newRangeMax){
                $ressource->setValeurGlissante($newRangeMax);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ressource);
            $entityManager->flush();
            $this->addFlash('success', 'Modification de la ressource réussie !');
        }catch(\Exception $e){
            $this->addFlash('warning', 'Erreur modification de la ressource !');
        }

        //redirection vers la page de provenance: listeDetails ou update
        if ($page == "update"){
            return $this->redirectToRoute('fiche_update', [
                'id' => $idFiche
            ]);
        }
        else{
            return $this->redirectToRoute('fiche_detail', [
                'id' => $idFiche
            ]);
        }
    }


    /**
     * @Route("/ressource/delete/{id}", name="ressource_delete")
     */
    public function deleteRessource(int $id, RessourceRepository $ressourceRepository): Response
    {
        $ressource = $ressourceRepository->find($id);
        $idFiche = $ressource->getFichePerso()->getId();
        $fiche = $ressource->getFichePerso();

        try {
            //maj sort de chaque ressource après celui supprimé
            $ressources = $ressourceRepository->findBy(['fichePerso' => $idFiche],['sort' => 'ASC']);
            for ($i = ($ressource->getSort()); $i < (count($ressources)-1); $i++ ) {
                $ressources[$i+1]->setSort($i);
            }

            $fiche->setNbRessource(count($ressources)-1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ressource);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression de la ressource réussie !');
        }catch(\Exception $e){
            $this->addFlash('warning', 'Erreur suppression de la ressource !');
        }

        return $this->redirectToRoute('fiche_update', [
            'id' => $idFiche,
        ]);
    }




}
