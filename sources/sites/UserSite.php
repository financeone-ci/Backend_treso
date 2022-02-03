<?php
// Affiche user site**************************
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

    if(isset($_GET['site']) && !empty($_GET['site']))
    {
        $site = secure($_GET['site']);
        // récupérer la liste des utilisateur
        $sql1 = "SELECT user_id as id, user_nom, user_prenom 
                    FROM user 
                         JOIN profil ON user.profil_id = profil.profil_id
                    WHERE profil.id_societe = '$societe'";
        $req1 = $DB->query($sql1);
        $d1 = $req1->fetchAll(PDO::FETCH_OBJ);
        foreach ($d1 as $key) {
            $user = $key->id;
            // vérifier si l'utilisateur fait parti du site
            $sql2 = "SELECT ID_SITE_USER 
                     FROM site_user 
                     WHERE ID_USER = '$user' AND ID_SITE = '$site'";
            $req2 = $DB->query($sql2);
            $d2 = $req2->fetch();
            if(!empty($d2['ID_SITE_USER'])){
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