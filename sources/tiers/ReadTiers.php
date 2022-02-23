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
    $sql = "SELECT `ID_TIERS` AS id, `CODE_TIERS`, `CODE_TIERS`, `TEL_TIERS`, `ADRESSE_TIERS`, `BENEFICIAIRE_TIERS`, `REF_TIERS`, `FOURNISSEUR_TIERS`, `CIV_REPRESENTANT_TIERS`, `NOM_REPRESENTANT_TIERS`, `FONCTION_REPRESENTANT_TIERS` FROM `tiers` WHERE  ID_TIERS = '$id'";
// Lecture de tous les sites
}else{
    $sql = "SELECT `ID_TIERS` AS id, `CODE_TIERS`, `CODE_TIERS`, `TEL_TIERS`, `ADRESSE_TIERS`, `BENEFICIAIRE_TIERS`, `REF_TIERS`, `FOURNISSEUR_TIERS`, `CIV_REPRESENTANT_TIERS`, `NOM_REPRESENTANT_TIERS`, `FONCTION_REPRESENTANT_TIERS` FROM `tiers`  ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql);
echo $reponse;