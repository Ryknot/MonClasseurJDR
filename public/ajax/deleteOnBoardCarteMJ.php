<?php


if (isset($_POST['carteId']))
{
    $carteId = $_POST['carteId'];

    /* connection à la base de donnée mySQL (adresse hôte, nom bdd , identifiant , mdp , aide affichage erreur) */
    $bdd = new PDO('mysql:host=localhost;dbname=projetperso','root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));

    /* appel des information ( selection des champs, du tableau 'identifiants' rangés par ordre des 'noms') */
    $requete = $bdd->prepare('SELECT qty_on_board FROM carte_mj WHERE id = ?');
    $requete->execute(array($carteId));
    $data = $requete->fetch();
    $qty = $data['qty_on_board'];

    if($qty == 1){
        /* appel des informations ( selection des champs, du tableau 'carteMJ') */
        $requete = $bdd->prepare('UPDATE carte_mj SET on_board = ?, qty_on_board = ?  WHERE id = ?');
        $requete->execute(array(false, 0, $carteId));

        echo "ajax dernière carte ok";

    }else{
        $qty = $qty - 1;
        /* appel des informations ( selection du champ qtyOnBoard du tableau 'carteMJ') */
        $requete = $bdd->prepare('UPDATE carte_mj SET qty_on_board = ?  WHERE id = ?');
        $requete->execute(array($qty, $carteId));

        echo "ajax autre carte ok";
    }

}