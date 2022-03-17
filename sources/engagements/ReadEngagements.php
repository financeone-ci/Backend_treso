<?php

// Lecture des audit**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';

///// période de recherche
if(isset($_GET['debut'])){
    $dateDeb = secure($_GET['debut']);
}else $dateDeb = date('Y-m-d');
if(isset($_GET['fin'])){
    $dateFin = secure($_GET['fin']).' 23:59';
}else $dateFin = date('Y-m-d 23:59:59');

//$dateFin = secure($_GET['fin'].' 23:59');

    $sql = "SELECT `ID_ENGAGEMENT` AS id,`NUM_ENGAGEMENT`,`BENEFICIAIRE`,`REF_BENEFICIAIRE`,engagement.`MONTANT`, `NUM_BON`,`MOTIF`,`TYPE_IMPORT`,`USER_IMPORT`,`DATE_ECHEANCE`,`ID_STATUT_ENGAGEMENT`,`CODE_BUDGET`,`DATE_IMPORTATION`,`IDIMPORT`,`DATE_ENGAGEMENT`,`REF_MARCHE`,`RETENUE`,`TAXE` FROM `engagement`  LEFT JOIN `engagement_paiement` ON engagement.ID_ENGAGEMENT = engagement_paiement.IDENGAGEMENT  WHERE DATE_ENGAGEMENT BETWEEN '$dateDeb' AND '$dateFin'   ";  

// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[],true,true);
echo $reponse;