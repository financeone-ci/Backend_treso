<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
require_once '../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();

// Récupérartion du jeton
if(isset($_GET['jeton']) && !empty($_GET['jeton'])){
    $jeton = secure($_GET['jeton']);
    $decoded = JWT::decode($jeton, $key, array('HS256'));
    $user = $decoded->user_nom . ' ' . $decoded->user_prenom;
}else{
    $user = "utilisateur introuvable";
}

if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = $_GET['id'];
    try{
        $t1 = array(
            'tuser' => $user,
        );
        $req = $DB->prepare("UPDATE paiement SET ID_STATUT_PAIEMENT = 5, USER_TIRAGE = :tuser, DATE_IMPRESSION = SYSDATE() WHERE IDPAIEMENT IN ($id)");
        $req->execute($t1);

         $infoHttp = [
            "reponse" => "success",
            "message" => "Impression lancée avec succès",
         ]; 
    }catch(PDOException $e){
        $infoHttp = [
            "reponse" => "error",
            "message" => "Impossible d'imprimer le(s) chèque(s)",
        ]; 
    }
}else{
    $infoHttp = [
            "reponse" => "error",
            "message" => "Impossible d'imprimer le(s) chèque(s)",
        ]; 
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);