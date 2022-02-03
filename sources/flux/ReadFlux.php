<?php

// Lecture des flux**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


// Lecture d'un flux
if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = secure($_GET['id']);
    $sql = "SELECT `ID_FLUX` AS id, `CODE_FLUX`, `DESCRIPTION_FLUX`, `SENS_FLUX`, `INCR_SOLDE_FLUX`, `INCR_QUOTA_FLUX`, categorie_flux.`ID_CATEGORIE_FLUX`, `CODE_CATEGORIE_FLUX` FROM `flux` JOIN categorie_flux ON flux.ID_CATEGORIE_FLUX = categorie_flux.ID_CATEGORIE_FLUX 
    WHERE ID_FLUX = '$id'";
// Lecture de tous les flux
}else{
    $sql = "SELECT `ID_FLUX` AS id, `CODE_FLUX`, `DESCRIPTION_FLUX`, `SENS_FLUX`, `INCR_SOLDE_FLUX`, `INCR_QUOTA_FLUX`, categorie_flux.`ID_CATEGORIE_FLUX`, `CODE_CATEGORIE_FLUX` FROM `flux` JOIN categorie_flux ON flux.ID_CATEGORIE_FLUX = categorie_flux.ID_CATEGORIE_FLUX";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql);
echo $reponse;