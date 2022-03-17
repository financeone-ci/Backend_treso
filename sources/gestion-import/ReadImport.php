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

    $sql = "SELECT IDIMPORT AS id, CHEMIN_IMPORT, DATE_IMPORT, TOTAL_IMPORT, TOTAL_REJET, TOTAL_ECRITURE 
    FROM import
    WHERE DATE_IMPORT BETWEEN '$dateDeb' AND '$dateFin'   ";  

// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[],true,true);
echo $reponse;