<?php


if (isset($_POST['id']) && isset($_POST['newValeur']) && isset($_POST['typeChamp']))
{
    $typeChamp = $_POST['typeChamp'];

    /* connection à la base de donnée mySQL (adresse hôte, nom bdd , identifiant , mdp , aide affichage erreur) */
    $bdd = new PDO('mysql:host=localhost;dbname=projetperso','root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));

    if ($typeChamp == "texte"){
        $requete = $bdd->prepare('UPDATE champs SET valeur_texte = ?  WHERE id = ?');
    }
    else if ($typeChamp == "area"){
        $requete = $bdd->prepare('UPDATE champs SET valeur_area = ?  WHERE id = ?');
    }

    $requete->execute(array($_POST['newValeur'], $_POST['id']));

    echo "ok";
}