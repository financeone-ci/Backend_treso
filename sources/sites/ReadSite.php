<?php

// Lecture des sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';

// Lecture d'une société
if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = secure($_GET['id']);
    $sql = "SELECT ID_SITE as id, CODE_SITE, DESCRIPTION_SITE, REPRESENTANT_SITE, LOCALISATION_SITE
            FROM sites 
            WHERE ID_SITE = '$id'";
// Lecture de toutes les sociétés
}else{
    $sql = "SELECT ID_SITE as id, CODE_SITE, DESCRIPTION_SITE, REPRESENTANT_SITE, LOCALISATION_SITE  
        FROM sites"; 
}
// reponse de l'API
$reponse = apiCreator($DB, $sql);

echo $reponse;