<?php

// Lecture des sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
$header = apache_request_headers();

if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)
{
    // Lecture d'un site
    if(isset($_GET['id']) && !empty($_GET['id']))
    {
        $id = secure($_GET['id']);
        $sql = "SELECT ID_SITE as id, CODE_SITE, DESCRIPTION_SITE, REPRESENTANT_SITE, LOCALISATION_SITE
        FROM sites 
        WHERE ID_SITE = '$id'";
    // Lecture de tous les sites
    }else{
        $sql = "SELECT ID_SITE as id, CODE_SITE, DESCRIPTION_SITE, REPRESENTANT_SITE, LOCALISATION_SITE  
        FROM sites";
    }
    $req = $DB->query($sql);
    $data = $req->fetchAll(PDO::FETCH_OBJ);
    $req->closeCursor();
    $infoHttp = [
        "reponse" => "success",
        "infos" => $data,
    ]; 
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Accès refusé.",
    ]; 
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);