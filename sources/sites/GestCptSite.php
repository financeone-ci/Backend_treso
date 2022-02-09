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

    if(isset($_GET['site']) && !empty($_GET['site']) && isset($_GET['cpte']) && !empty($_GET['cpte']))
    {
        $site = secure($_GET['site']);
        $compte = secure($_GET['cpte']);
        $action = secure($_GET['act']);

        switch ($action) {
            case 0:
                # Cas d'ajout d'utilisateur au site
                $t = array(
                    'compte' => $compte,
                    'site' => $site,
                );
                $req = $DB->prepare("INSERT INTO site_compte(ID_COMPTE, ID_SITE) values(:compte, :site)");
                $req->execute($t);
                
                //Audits
                // AuditSystem();

                break;
            case 1:
                # Cas de suppression d'utilisateur au site
                $t = array(
                    'compte' => $compte,
                    'site' => $site,
                );
                $req = $DB->prepare("DELETE FROM site_compte WHERE ID_COMPTE = :compte AND ID_SITE = :site");

                //Audits
                // AuditSystem();

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