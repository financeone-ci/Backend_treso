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
    $sql = "SELECT `ID_TYPE_TIERS` AS id, `LIBELLE_TYPE_TIERS`, `CODE_TYPE_TIERS`, `ID_SOCIETE` FROM `type_tiers` WHERE  ID_TYPE_TIERS = '$id'";
// Lecture de tous les sites
}else{
    $sql = "SELECT `ID_TYPE_TIERS` AS id, `LIBELLE_TYPE_TIERS`, `CODE_TYPE_TIERS`, `ID_SOCIETE` FROM `type_tiers` ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql);
echo $reponse;