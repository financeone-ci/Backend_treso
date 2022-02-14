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
    $sql = "SELECT `IDDEVISE` As id,`CODE_DEVISE`,`LIBELLE_DEVISE`,`TAUX_DEVISE`,`DEVISE_DE_BASE`,`LIBELLE_CENTIMES`,`SIGLE_DEVISE` FROM `devise`  
    WHERE IDDEVISE = '$id'";
// Lecture de tous les sites
}else{
    $sql = "SELECT `IDDEVISE` As id,`CODE_DEVISE`,`LIBELLE_DEVISE`,`TAUX_DEVISE`,`DEVISE_DE_BASE`,`LIBELLE_CENTIMES`,`SIGLE_DEVISE` FROM `devise` ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[], false);
echo $reponse;