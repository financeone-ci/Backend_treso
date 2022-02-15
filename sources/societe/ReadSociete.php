<?php

// Lecture des sociétés**************************
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
        $sql = "SELECT `ID_SOCIETE` AS id,`CODE_SOCIETE`,`LIBELLE_SOCIETE`,`COMPLEMENT_SOCIETE`,`ADRESSE_SOCIETE`,`TEL_SOCIETE`,`FAX_SOCIETE`,`EMAIL_SOCIETE`,`SIEGE` FROM `societe`  
         WHERE ID_SOCIETE = '$id'";
    // Lecture de tous les sites
    }else{
        $sql = "SELECT `ID_SOCIETE` AS id,`CODE_SOCIETE`,`LIBELLE_SOCIETE`,`COMPLEMENT_SOCIETE`,`ADRESSE_SOCIETE`,`TEL_SOCIETE`,`FAX_SOCIETE`,`EMAIL_SOCIETE`,`SIEGE` FROM `societe`";
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