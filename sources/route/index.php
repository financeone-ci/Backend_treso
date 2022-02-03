<?php

// Lecture des sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$infoHttp = array();
$header = apache_request_headers();
/*
if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)
{
    */
    
      
        $sql = "SELECT * FROM `route` ";
    // Lecture de tous les sites
    
    $req = $DB->query($sql);
    $data = $req->fetchAll(PDO::FETCH_OBJ);
    $req->closeCursor();
    $infoHttp = [
        "reponse" => "success",
        "infos" => $data,
    ]; /*
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Accès refusé.",
    ]; 
}*/
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);