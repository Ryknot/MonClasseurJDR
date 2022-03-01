<?php


if (isset($_POST['id']) && isset($_POST['valeurGlissante']))
{
    /* connection à la base de donnée mySQL (adresse hôte, nom bdd , identifiant , mdp , aide affichage erreur) */
    $bdd = new PDO('mysql:host=localhost;dbname=projetperso','root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));

    /* appel des informations ( selection des champs, du tableau 'ressource') */
    $requete = $bdd->prepare('UPDATE ressource SET valeur_glissante = ?  WHERE id = ?');
    $requete->execute(array($_POST['valeurGlissante'], $_POST['id']));

    echo "ok";
}