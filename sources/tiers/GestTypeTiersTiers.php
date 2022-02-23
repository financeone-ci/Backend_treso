<?php
// Ajouter ou supprimer un utlisateur du site**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$infoHttp = array();
$header = apache_request_headers();

if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)
{
    $jeton = $header['Authorization'];
    $payload = tokenData($jeton);
    $societe = $payload->user_societe;

    if(isset($_GET['tiers']) && !empty($_GET['tiers']) && isset($_GET['type']) && !empty($_GET['type']))
    {
        $tiers = secure($_GET['tiers']);
        $type = secure($_GET['type']);
        $action = secure($_GET['act']);

        switch ($action) {
            case 0:
                # Cas d'ajout  
                $t = array(
                    'ttype' => $type,
                    'tiers' => $tiers,
                );
                $req = $DB->prepare("INSERT INTO tiers_type_tiers (ID_TYPE_TIERS, ID_TIERS) values (:ttype, :tiers)");
                $req->execute($t);
                break;
            case 1:
                # Cas de suppression  
                $t = array(
                    'ttype' => $type,
                    'tiers' => $tiers,
                );
                $req = $DB->prepare("DELETE FROM tiers_type_tiers WHERE ID_TYPE_TIERS = :ttype AND ID_TIERS = :tiers");
                $req->execute($t);
                break;
            default:
                # Autres cas
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Action inconnue",
                ]; 
                break;
        }
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