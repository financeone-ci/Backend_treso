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

    if(isset($_GET['site']) && !empty($_GET['site']))
    {
        $site = secure($_GET['site']);
        // récupérer la liste des utilisateur
        $sql1 = "SELECT ID_COMPTE as id, CODE_COMPTE, LIBELLE_COMPTE 
                FROM compte 
                WHERE compte.ID_SOCIETE = '$societe'";
        $req1 = $DB->query($sql1);
        $d1 = $req1->fetchAll(PDO::FETCH_OBJ);
        foreach ($d1 as $key) {
            $cpte = $key->id;
            // vérifier si l'utilisateur fait parti du site
            $sql2 = "SELECT ID_SITE_COMPTE 
                     FROM site_compte 
                     WHERE ID_COMPTE = '$cpte' AND ID_SITE = '$site'";
            $req2 = $DB->query($sql2);
            $d2 = $req2->fetch();
            if(!empty($d2['ID_SITE_COMPTE'])){
                $key->site = 1;
            }else{
                $key->site = 0;
            }
        }
        $req2->closeCursor();
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