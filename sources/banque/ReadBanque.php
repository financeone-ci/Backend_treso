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
    $sql = "SELECT `IDBANQUE` AS id, `CODE_BANQUE` , `LIBELLE_BANQUE`, `DG`, `GESTIONNAIRE`, `ADRESSE_BANQUE`, `ADRESSE_WEB_BANQUE`, `CONTACT_BANQUE` FROM `banque`  
    WHERE IDBANQUE = '$id'";
// Lecture de tous les sites
}else{
    $sql = "SELECT `IDBANQUE` AS id, `CODE_BANQUE` , `LIBELLE_BANQUE`, `DG`, `GESTIONNAIRE`, `ADRESSE_BANQUE`, `ADRESSE_WEB_BANQUE`, `CONTACT_BANQUE` FROM `banque`  ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql,"read", [], false);
echo $reponse;