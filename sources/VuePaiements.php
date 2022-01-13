<?php

// Récupérer les informations d'un ou plusieur paiements **************************

// header("Access-Control-Allow-Credentials: true");
// header("Access-Control-Max-Age: 86400");

require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
require_once '../fonctions/getToken.php';
require_once '../fonctions/getFiles.php';

$infoHttp = array();

// Récupération des entêtes HTTP
$header = apache_request_headers();

 if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)

{
    if(isset($_GET['id']) && !empty($_GET['id'])){
        // Cas d'un paiement particulier
        $id = secure(($_GET['id']));
        $sql = "SELECT BENEF_REMISE, REF_REMISE FROM paiement WHERE IDPAIEMENT = '$id'";
        $req = $DB->query($sql);
        $DBdata = $req->fetch();
        $DBdata["file"] = getFiles('../uploads/paiements/retraits/'.$id,$id);
        $infoHttp = [
            "reponse" => "success",
            "infos" => $DBdata,
           
        ];
    }else{
        // Cas de plusieurs paiements
        $infoHttp = [
            "reponse" => "error",
            "infos" => "probleme",
        ];
    }
}else{
    $infoHttp = [
            "reponse" => "error",
            "message" => "Paramètres incorrects",
        ];
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
