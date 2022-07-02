<?php

namespace App\Controller;

use App\Entity\Champs;
use App\Entity\FichePerso;
use App\Entity\Ressource;
use App\Form\ChampFormType;
use App\Form\FicheFormType;
use App\Form\RessourceFormType;
use App\Repository\ChampsRepository;
use App\Repository\FichePersoRepository;
use App\Repository\RessourceRepository;
use App\Repository\TypeInfoRepository;
use App\Repository\UserRepository;
use App\Service\SecurityService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security(" (user.getActive() == true && user.getValidated() == true) || is_granted('ROLE_ADMIN') ")
 */
class FicheController extends AbstractController
{
    private $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }


    /**
     * @Route("/profil/{id}/fiche/list", name="fiche_list")
     */
    public function listFiche(int $id, UserRepository $userRepository){

        $user = $userRepository->find($id);
        //récupération de l'instance du user pour contrôle sécurité
        if ($this->getUser() == $user || $this->isGranted("ROLE_ADMIN")){
            $fiches = $user->getFichePersos();

            return $this->render('fiche/list.html.twig', [
                'user'=>$user,
                'fiches'=>$fiches
            ]);
        }
        else{
            //identification mauvais user + logMenace + alert + checkMenace + redirection
            $realUser = $this->securityService->findRealUser();

            return $this->redirectToRoute('fiche_list',[
            'id' => $realUser->getId(),
            ]);
        }
    }


    /**
     * @Route("/fiche/{id}/detail", name="fiche_detail")
     */
    public function ficheDetail(int $id, FichePersoRepository $fichePersoRepository, TypeInfoRepository $infoRepository, ChampsRepository $champRepository, RessourceRepository $ressourceRepository, UserRepository $userRepository, Request $request): Response
    {
        //récupération de l'instance de la fichePerso
        $fiche = $fichePersoRepository->find($id);
        if(!$fiche){
            $this->addFlash('warning', "Cette fiche n'existe pas" );
            return $this->redirectToRoute('main_home');
        }

        //récupération de l'instance du user pour contrôle sécurité
        $user = $fiche->getUser();
        if ($this->getUser() == $user || $this->isGranted("ROLE_ADMIN")){

            //recupération liste types info
            $listeInfo = $infoRepository->findAll();

            //récupération des champs et ressources associés à la fiche de perso triés par ordre (sort)
            $champs = $champRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);
            $ressources = $ressourceRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);

            //récupération valeur bouton
            $boutonFiche = $request->get("boutonFiche");

            return $this->render('fiche/ficheDetail.html.twig', [
                'fiche' => $fiche,
                'listeInfo' => $listeInfo,
                'champs' => $champs,
                'ressources' => $ressources,
                'boutonFiche' => $boutonFiche
            ]);
        }
        else{
            //identification mauvais user + logMenace + alert + checkMenace + redirection
            $realUser = $this->securityService->findRealUser();

            return $this->redirectToRoute('fiche_list',[
                'id' => $realUser->getId(),
            ]);
        }
    }

    /**
     * @Route("/profil/{id}/fiche/create", name="fiche_create")
     *
     */
    public function ficheCreate(int $id, UserRepository $userRepository, ChampsRepository $champsRepository, RessourceRepository $ressourceRepository, Request $request): Response
    {
        //récupération de l'instance du user
        $user = $userRepository->find($id);

        //check si admin ou bon user
        if ($this->getUser() == $user || $this->isGranted("ROLE_ADMIN"))
        {
            $fiche = new FichePerso();
            $form = $this->createForm(FicheFormType::class, $fiche);
            $form->remove('user');
            $fiche->setUser($user);

            $form->remove('nbChamps1');
            $form->remove('nbChamps2');
            $form->remove('nbChamps3');
            $form->remove('nbChamps4');
            $form->remove('nbChamps5');
            $form->remove('nbChamps6');
            $form->remove('nbChamps7');

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                try{
                    //gestion image
                    $image = $form->get('image')->getData();
                    if ($image){
                        //genere un nouveau nom de fichier
                        $fichier = md5(uniqid()) . '_Fiche.' . $image->guessExtension();

                        //copie de la photo dans le dossier uploads
                        $image->move($this->getParameter('images_directory'), $fichier);

                        //envoie du nom de fichier dans la BDD
                        $fiche->setImage($fichier);
                    }
                    else{
                        $fiche->setImage("icon_d20_mini.png");
                    }

                    //initialisation du nb de champs à 0 pour chaque type d'info
                    $fiche->setNbChamps1(0);
                    $fiche->setNbChamps2(0);
                    $fiche->setNbChamps3(0);
                    $fiche->setNbChamps4(0);
                    $fiche->setNbChamps5(0);
                    $fiche->setNbChamps6(0);
                    $fiche->setNbChamps7(0);

                    //initialisation du nb de ressource à 0
                    $fiche->setNbRessource(0);

                    $entityManager->persist($fiche);
                    $entityManager->flush();
                    $this->addFlash('success', 'Création de la fiche réussie !');

                    $champs = $champsRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);
                    $ressources = $ressourceRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);


                    return $this->render('fiche/ficheDetail.html.twig',[
                        'user' => $user,
                        'fiche' => $fiche,
                        'champs' => $champs,
                        'ressources' => $ressources,
                    ]);
                }
                catch(\Exception $e){
                    $this->addFlash('warning', 'Erreur création de la fiche ! '.$e->getMessage());
                }
            }
            return $this->render('fiche/create.html.twig', [
                'user' => $user,
                'ficheForm' => $form->createView()
            ]);
        }
        else{
            //identification mauvais user + logMenace + alert + checkMenace + redirection
            $realUser = $this->securityService->findRealUser();

            return $this->redirectToRoute('fiche_create',[
                'id' => $realUser->getId(),
            ]);
        }
    }

    /**
     * @Route("/fiche/{id}/updateInfo", name="fiche_updateInfo")
     */
    public function ficheUpdateInfo(int $id, FichePersoRepository $fichePersoRepository, ChampsRepository $champsRepository, RessourceRepository $ressourceRepository, Request $request): Response
    {
        //récupération de l'instance de la fichePerso
        $fiche = $fichePersoRepository->find($id);
        //récupération de l'instance du user
        $user = $fiche->getUser();

        //gestion erreur
        if (!$fiche){
            throw $this->createNotFoundException('Pas de fiche trouvé');
        }

        $form = $this->createForm(FicheFormType::class, $fiche);
        $fiche->setUser($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            try{
                //gestion image
                $image = $form->get('image')->getData();
                if ($image){
                    //suppression de l'ancienne image
                    $oldImage = $fiche->getImage();
                    if ($oldImage && $oldImage != "icon_d20_mini.png") {
                        try {
                            //suppression du fichier de l'ancienne photo
                            unlink($this->getParameter('images_directory') . '/' . $oldImage);
                        } catch(\Exception $e){
                            $this->addFlash('warning', 'Erreur suppression de l\'image ! ' . $e->getMessage());
                        }
                    }

                    //genere un nouveau nom de fichier
                    $fichier = md5(uniqid()) . '_Fiche.' . $image->guessExtension();

                    //copie de la photo dans le dossier uploads
                    $image->move($this->getParameter('images_directory'), $fichier);

                    //envoie du nom de fichier dans la BDD
                    $fiche->setImage($fichier);
                }

                $entityManager->persist($fiche);
                $entityManager->flush();
                $this->addFlash('success', 'modification de la fiche réussie !');

                $champs = $champsRepository->findBy(['fichePerso' => $fiche->getId()],['sort' => 'ASC']);
                $ressources = $ressourceRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);

                return $this->render('fiche/ficheDetail.html.twig',[
                    'user' => $user,
                    'fiche' => $fiche,
                    'champs' => $champs,
                    'ressources' => $ressources,
                ]);
            }
            catch(\Exception $e){
                $this->addFlash('warning', 'Erreur modification de la fiche ! '.$e->getMessage());
            }
        }

        return $this->render('fiche/updateInfo.html.twig', [
            'user' => $user,
            'fiche' => $fiche,
            'ficheForm' => $form->createView()
        ]);;
    }

    /**
     * @Route("/fiche/{id}/update", name="fiche_update")
     */
    public function ficheUpdate(int $id,FichePersoRepository $fichePersoRepository,TypeInfoRepository $infoRepository, ChampsRepository $champsRepository, RessourceRepository $ressourceRepository, Request $request): Response
    {
        //récupération de l'instance de la fichePerso
        $fiche = $fichePersoRepository->find($id);

        //recupération liste types info
        $listeInfo = $infoRepository->findAll();

        //récupération valeur bouton
        $boutonFiche = $request->get("boutonFiche");


        $champ = new Champs();
        $form = $this->createForm(ChampFormType::class, $champ);
        $form->remove('fichePerso');
        $form->remove('sort');
        $champ->setFichePerso($fiche);
        $form->handleRequest($request);


        $ressource = new Ressource();
        $formRessource = $this->createForm(RessourceFormType::class, $ressource);
        $formRessource->remove('fichePerso');
        $formRessource->remove('sort');
        $formRessource->remove('valeurGlissante');
        $ressource->setFichePerso($fiche);
        $formRessource->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        //formulaire champ
        if ($form->isSubmitted() && $form->isValid()) {
            try{

                //gestion champ vide
                if ($form->get('typeChamp')->getData() == "text" && empty($form->get('valeurTexte')->getData())){
                    $champ->setValeurTexte("texte vide");
                }
                else if ($form->get('typeChamp')->getData() == "textArea" && empty($form->get('valeurArea')->getData())){
                    $champ->setValeurArea("zone de texte vide");
                }

                //gestion de l'ordre des champs
                $info = $form->get('typeInfo')->getData()->getid();
                $champsParInfo = $champsRepository->findBy(['fichePerso' => $id, 'typeInfo' => $info]);
                $countChamps = count($champsParInfo);
                $champ->setSort($countChamps);

                //mise a jour du nb de champs de ce type d'info
                $erreurNbChamps=false;
                switch ($info){
                    case 1 : $fiche->setNbChamps1($countChamps+1);break;
                    case 2 : $fiche->setNbChamps2($countChamps+1);break;
                    case 3 : $fiche->setNbChamps3($countChamps+1);break;
                    case 4 : $fiche->setNbChamps4($countChamps+1);break;
                    case 5 : $fiche->setNbChamps5($countChamps+1);break;
                    case 6 : $fiche->setNbChamps6($countChamps+1);break;
                    case 7 : $fiche->setNbChamps7($countChamps+1);break;
                }

                $entityManager->persist($champ);
                $entityManager->flush();
                $this->addFlash('success', 'Modification de la fiche réussie !');

            }catch(\Exception $e){
                $this->addFlash('warning', 'Erreur modification de la fiche !');
            }

            $champs = $champsRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);
            $ressources = $ressourceRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);

            if ($boutonFiche == 'update'){
                return $this->render('fiche/update.html.twig', [
                    'fiche' => $fiche,
                    'listeInfo' => $listeInfo,
                    'champs' => $champs,
                    'ressources' => $ressources,
                    'boutonFiche' => $boutonFiche,
                    'champForm' => $form->createView(),
                    'ressourceForm' => $formRessource->createView(),
                ]);
            }
            else{
                return $this->render('fiche/ficheDetail.html.twig', [
                    'fiche' => $fiche,
                    'listeInfo' => $listeInfo,
                    'champs' => $champs,
                    'ressources' => $ressources,
                    'boutonFiche' => $boutonFiche,
                    'champForm' => $form->createView(),
                    'ressourceForm' => $formRessource->createView(),
                ]);
            }

        }


        //formulaire ressource
        if ($formRessource->isSubmitted() && $formRessource->isValid()) {
            try{
                //gestion de l'ordre des ressources
                $listRessources = $ressourceRepository->findBy(['fichePerso' => $id]);
                $countRessources = count($listRessources);
                if ($countRessources < 3){
                    $ressource->setSort($countRessources);
                    $fiche->setNbRessource($countRessources+1);

                    $valeurMax = $formRessource->get('rangeMax')->getData();
                    $ressource->setValeurGlissante($valeurMax);

                    $entityManager->persist($ressource);
                    $entityManager->flush();
                    $this->addFlash('success', 'Modification de la fiche réussie !');
                }
                else{
                    $this->addFlash('warning', 'Erreur: 3 ressources max!');
                }
            }catch(\Exception $e){
                $this->addFlash('warning', 'Erreur modification de la fiche !');
            }

            $champs = $champsRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);
            $ressources = $ressourceRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);

            if ($boutonFiche == 'update'){
                return $this->render('fiche/update.html.twig', [
                    'fiche' => $fiche,
                    'listeInfo' => $listeInfo,
                    'champs' => $champs,
                    'ressources' => $ressources,
                    'boutonFiche' => $boutonFiche,
                    'champForm' => $form->createView(),
                    'ressourceForm' => $formRessource->createView(),
                ]);
            }
            else{
                return $this->render('fiche/ficheDetail.html.twig', [
                    'fiche' => $fiche,
                    'listeInfo' => $listeInfo,
                    'champs' => $champs,
                    'ressources' => $ressources,
                    'boutonFiche' => $boutonFiche,
                    'champForm' => $form->createView(),
                    'ressourceForm' => $formRessource->createView(),
                ]);
            }

        }

        $champs = $champsRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);
        $ressources = $ressourceRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);

        return $this->render('fiche/update.html.twig', [
            'fiche' => $fiche,
            'listeInfo' => $listeInfo,
            'champs' => $champs,
            'ressources' => $ressources,
            'boutonFiche' => $boutonFiche,
            'champForm' => $form->createView(),
            'ressourceForm' => $formRessource->createView(),
        ]);

    }

    /**
     * @Route("/fiche/{id}/delete", name="fiche_delete")
     */
    public function ficheDelete(int $id,FichePersoRepository $fichePersoRepository, ChampsRepository $champsRepository, Request $request): Response
    {
        $fiche = $fichePersoRepository->find($id);
        $id = $fiche->getUser()->getId();

        try {
            $entityManager = $this->getDoctrine()->getManager();

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
            if ($image && $image != "icon_d20_mini.png") {
                //suppression du fichier de l'ancienne photo
                unlink($this->getParameter('images_directory') . '/' . $image);
            }

            $entityManager->remove($fiche);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression de la fiche réussie !');
        }catch(\Exception $e){
            $this->addFlash('warning', 'Erreur suppression de la fiche !'. $e->getMessage());
        }

        return $this->redirectToRoute('fiche_list', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/fichetest/{id}/", name="fiche_test")
     */
    public function ficheTest(int $id, FichePersoRepository $fichePersoRepository, TypeInfoRepository $infoRepository, ChampsRepository $champRepository, RessourceRepository $ressourceRepository, Request $request): Response
    {
        //récupération de l'instance de la fichePerso
        $fiche = $fichePersoRepository->find($id);

        //recupération liste types info
        $listeInfo = $infoRepository->findAll();

        //récupération des champs et ressources associés à la fiche de perso triés par ordre (sort)
        $champs = $champRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);
        $ressources = $ressourceRepository->findBy(['fichePerso' => $id],['sort' => 'ASC']);

        //récupération valeur bouton
        $boutonFiche = $request->get("boutonFiche");

        return $this->render('fiche/ficheDetailSkinMed.html.twig', [
            'fiche' => $fiche,
            'listeInfo' => $listeInfo,
            'champs' => $champs,
            'ressources' => $ressources,
            'boutonFiche' => $boutonFiche
        ]);
    }

}
