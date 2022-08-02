<?php

namespace App\Controller;

use App\Entity\CarteMJ;
use App\Form\CarteMJFormType;
use App\Repository\CarteMJRepository;
use App\Repository\UserRepository;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security(" (user.getActive() == true && user.getValidated() == true) || is_granted('ROLE_ADMIN') ")
 */
class CarteMJController extends AbstractController
{
    /**
     * @Route("/interfacemj/{id}/CarteMJ/listAll", name="interfaceMJ_listAllCartesMJ")
     */
    public function listAllCartesMJ(int $id, UserRepository $userRepository, CarteMJRepository $carteMJRepository)
    {
        $user = $userRepository->find($id);
        $cartesMJ = $carteMJRepository->findBy(['user' => $user],['nom' => 'ASC']);

        return $this->render('carte_mj/listAllCartesMJ.html.twig', [
            'user' => $user,
            'cartesMJ' => $cartesMJ,
        ]);
    }


    /**
     * @Route("/interfacemj/{id}/CarteMJ/create", name="interfaceMJ_createCarteMJ")
     */
    public function createCarteMJ(int $id, UserRepository $userRepository, LogService $logService, Request $request): Response
    {
        $user = $userRepository->find($id);

        //récupération valeur bouton
        $boutonFiche = $request->get("boutonFiche");

        $carteMJ = new CarteMJ();
        $form = $this->createForm(CarteMJFormType::class, $carteMJ);
        $form->remove('user');
        $carteMJ->setUser($user);

        //en attente gestion filtre
        $form->remove('filtre');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                //gestion image
                $image = $form->get('image')->getData();

                if ($image) {
                    //genere un nouveau nom de fichier
                    $fichier = md5(uniqid()) . '_CarteMJ.' . $image->guessExtension();

                    //copie de la photo dans le dossier uploads
                    $image->move($this->getParameter('images_directory')."/", $fichier);

                    //envoie du nom de fichier dans la BDD
                    $carteMJ->setImage($fichier);
                }
                else{
                    $carteMJ->setImage("icon_d20_mini.png");
                }


                //gestion pv min 1 max 1000, valeur par défaut 1
                $pv = $form->get('PV')->getData();
                if($pv < 1 || $pv>1000 || $pv === null){
                    $carteMJ->setPV(1);
                }

                $entityManager->persist($carteMJ);
                $entityManager->flush();
                $this->addFlash('success', 'Création de la carte réussie !');


                if ($boutonFiche == 'valider') {
                    return $this->render('interface_mj/interfaceMJ.html.twig', [
                        'user' => $user,
                        'carteMJ' => $carteMJ,
                    ]);
                }
                return $this->redirectToRoute('interfaceMJ_updateCarteMJ', [
                    'carteId' => $carteMJ->getId(),
                ]);

            } catch
                (\Exception $e){
                $this->addFlash('warning', 'Erreur création de la carte !');
                $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                $entityManager->persist($log);
                $entityManager->flush();
            }
        }
        if ($form->isSubmitted() && !($form->isValid())) {
            $this->addFlash('warning', 'Erreur pendant la création de la carte ! ');
        }

        return $this->render('carte_mj/createCarteMJ.html.twig', [
            'user'=>$user,
            'carteMJForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/CarteMJ/{carteId}/update", name="interfaceMJ_updateCarteMJ")
     */
    public function updateCarteMJ(int $carteId, CarteMJRepository $carteMJRepository, LogService $logService, Request $request): Response
    {
        $carteMJ = $carteMJRepository->find($carteId);
        if($carteMJ == null)
        {
            return $this->redirectToRoute('interfaceMJ_listAllCartesMJ', [
                'user' => $this->getUser(),
            ]);
        }
        $user = $carteMJ->getUser();

        $form = $this->createForm(CarteMJFormType::class, $carteMJ);
        $form->remove('user');
        $carteMJ->setUser($user);

        //en attente gestion filtre
        $form->remove('filtre');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                //gestion image
                $image = $form->get('image')->getData();

                if ($image) {
                    $oldImage = $carteMJ->getImage();
                    if ($oldImage != $image)
                    {
                        if($oldImage && $oldImage != "icon_d20_mini.png" && file_exists($this->getParameter('images_directory') . '/' . $oldImage)){
                            try {
                                //suppression du fichier de l'ancienne photo
                                unlink($this->getParameter('images_directory') . '/' . $oldImage);
                            } catch(\Exception $e){
                                $this->addFlash('warning', 'Erreur suppression ancienne image !');
                                $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                                $entityManager->persist($log);
                                $entityManager->flush();
                            }
                        }

                        //genere un nouveau nom de fichier
                        $fichier = md5(uniqid()) . '_CarteMJ.' . $image->guessExtension();

                        //copie de la photo dans le dossier uploads
                        $image->move($this->getParameter('images_directory')."/", $fichier);

                        //envoie du nom de fichier dans la BDD
                        $carteMJ->setImage($fichier);
                    }
                }

                //gestion pv min 1 max 1000, valeur par défaut 1
                $pv = $form->get('PV')->getData();
                if($pv < 1 || $pv>1000 || $pv === null){
                    $carteMJ->setPV(1);
                }

                $entityManager->persist($carteMJ);
                $entityManager->flush();
                $this->addFlash('success', 'Modification de la carte réussie !');

            } catch(\Exception $e){
                $this->addFlash('warning', 'Erreur modification de la carte !');
                $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                $entityManager->persist($log);
                $entityManager->flush();
            }
        }
        if ($form->isSubmitted() && !($form->isValid())) {
            $this->addFlash('warning', 'Erreur pendant la modification de la carte ! ');
        }

        return $this->render('carte_mj/updateCarteMJ.html.twig', [
            'user'=>$user,
            'carteMJ' => $carteMJ,
            'carteMJForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/CarteMJ/{carteId}/delete", name="interfaceMJ_deleteCarteMJ")
     */
    public function deleteCarteMJ( int $carteId, CarteMJRepository $carteMJRepository, EntityManagerInterface $entityManager, LogService $logService): Response
    {
        $carteMJ = $carteMJRepository->find($carteId);
        $user = $carteMJ->getUser();

        try {
            $image = $carteMJ->getImage();
            if($image && $image != "icon_d20_mini.png" && file_exists($this->getParameter('images_directory') . '/' . $image)){
                try {
                    //suppression du fichier de l'image'
                    unlink($this->getParameter('images_directory') . '/' . $image);
                } catch(\Exception $e){
                    $this->addFlash('warning', 'Erreur suppression de l\'image ! Image introuvable');
                    $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
                    $entityManager->persist($log);
                    $entityManager->flush();
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($carteMJ);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression de la carte réussie !');

        } catch(\Exception $e){
            $this->addFlash('warning', 'Erreur suppression de la carte !');
            $log = $logService->newLogError($e->getMessage(). "|| ".$e->getFile(). "||" .$e->getLine());
            $entityManager->persist($log);
            $entityManager->flush();
        }

        //rechargement de la liste de carteMJ
        $cartesMJ = $carteMJRepository->findBy(['user' => $user],['nom' => 'ASC']);

        return $this->render('carte_mj/listAllCartesMJ.html.twig', [
            'user' => $user,
            'cartesMJ' => $cartesMJ,
        ]);
    }

    /**
     * @Route("/CarteMJ/{carteId}/addOnBoard", name="interfaceMJ_addOnBoard")
     */
    public function addOnBoardCarteMJ( int $carteId, CarteMJRepository $carteMJRepository, EntityManagerInterface $entityManager): Response
    {
        $carteMJ = $carteMJRepository->find($carteId);
        $user = $carteMJ->getUser();

        if($carteMJ->getQtyOnBoard() == 0)
        {
            $carteMJ->setOnBoard(true);
            $carteMJ->setQtyOnBoard(1);
        }else
        {
            $qty = $carteMJ->getQtyOnBoard() + 1;
            $carteMJ->setQtyOnBoard($qty);
        }
        $entityManager->flush();

        return $this->redirectToRoute('interfaceMJ', [
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/CarteMJ/{carteId}/deleteOnBoard", name="interfaceMJ_deleteOnBoard")
     */
    public function deleteOnBoardCarteMJ( int $carteId, CarteMJRepository $carteMJRepository, EntityManagerInterface $entityManager): Response
    {
        $carteMJ = $carteMJRepository->find($carteId);
        $user = $carteMJ->getUser();

        if($carteMJ->getQtyOnBoard() == 1)
        {
            $carteMJ->setOnBoard(false);
            $carteMJ->setQtyOnBoard(0);
        }else
        {
            $qty = $carteMJ->getQtyOnBoard() - 1;
            $carteMJ->setQtyOnBoard($qty);
        }
        $entityManager->flush();

        return $this->redirectToRoute('interfaceMJ', [
            'user'=>$user,
        ]);
    }
}
