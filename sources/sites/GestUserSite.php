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

    if(isset($_GET['site']) && !empty($_GET['site']) && isset($_GET['us']) && !empty($_GET['us']))
    {
        $site = secure($_GET['site']);
        $user = secure($_GET['us']);
        $action = secure($_GET['act']);

        switch ($action) {
            case 0:
                # Cas d'ajout d'utilisateur au site
                $t = array(
                    'user' => $user,
                    'site' => $site,
                );
                $req = $DB->prepare("INSERT INTO site_user(ID_USER, ID_SITE) values(:user, :site)");
                $req->execute($t);
                break;
            case 1:
                # Cas de suppression d'utilisateur au site
                $t = array(
                    'user' => $user,
                    'site' => $site,
                );
                $req = $DB->prepare("DELETE FROM site_user WHERE ID_USER = :user AND ID_SITE = :site");
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