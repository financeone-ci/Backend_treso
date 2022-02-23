<?php
// Affiche user site**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$infoHttp = array();
$header = apache_request_headers();

if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)
{
    // Récupération de la société
    $jeton = $header['Authorization'];
    $payload = tokenData($jeton);
    $societe = $payload->user_societe;

    if(isset($_GET['tiers']) && !empty($_GET['tiers']))
    {
        $tiers = secure($_GET['tiers']);
        // récupérer la liste des types tiers
        $sql1 = "SELECT 
        type_tiers.ID_TYPE_TIERS AS id, `LIBELLE_TYPE_TIERS`,`CODE_TYPE_TIERS`, tiers_type_tiers.ID_TIERS AS tiers
        FROM `type_tiers`
                LEFT JOIN tiers_type_tiers
                ON type_tiers.ID_TYPE_TIERS = tiers_type_tiers.ID_TYPE_TIERS
                WHERE `ID_SOCIETE` = $societe";
        $req1 = $DB->query($sql1);
        $d1 = $req1->fetchAll(PDO::FETCH_OBJ);
        
      
        $req1->closeCursor();

        $infoHttp = [
            "reponse" => "success",
            "infos" => $d1,
        ];
    }else{
        $infoHttp = [
            "reponse" => "error",
            "message" => "paramètres incorrects",
        ]; 
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Accès refusé",
    ]; 
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);