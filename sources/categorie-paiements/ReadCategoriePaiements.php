<?php

// Lecture des sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


// Lecture d'un site
if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = secure($_GET['id']);
    $sql = "SELECT `ID_CATEGORIE_PAIEMENT` AS id, `CODE_CATEGORIE_PAIEMENT`, `LIB_CATEGORIE_PAIEMENT`, `ID_SOCIETE` FROM `categorie_paiement`   
    WHERE ID_CATEGORIE_PAIEMENT = '$id'";
// Lecture de tous les sites
}else{
    $sql = "SELECT `ID_CATEGORIE_PAIEMENT` AS id, `CODE_CATEGORIE_PAIEMENT`, `LIB_CATEGORIE_PAIEMENT`, `ID_SOCIETE` FROM `categorie_paiement`";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql);
echo $reponse;