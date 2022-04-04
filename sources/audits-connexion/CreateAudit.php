<?php
// Création de categorie**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$header = apache_request_headers(); // autorisation 
$obj = json_decode(file_get_contents('php://input')); 
if (isset($obj->values)){
    $obj = $obj->values;
}
if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true){
    // Récupération de la société
    $jeton = $header['Authorization'];
    $payload = tokenData($jeton);
    $soci = $payload->user_societe;

echo AuditConnexion($DB, $payload->user_id, $payload->user_login, "out", 1, 'Déconnexion manuelle', $soci);
}else{
    echo 
    json_encode([
        "reponse" => "error",
        "message" => "Accès refusé",
                ], 
        JSON_UNESCAPED_UNICODE
    );
}