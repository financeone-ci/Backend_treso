<?php
require_once 'jwt.class.php';
/**
 * Created by PhpStorm.
 * User: DIE
 * Date: 06/07/2021
 * Time: 14:59
 */
//function generer_jwt(){
//    // get the local secret key
//    $secret = '08101783738219be049b80b50a8a7d22ec9a2b02255bac14b6242ac58f738ed3';
//
//// Create the token header
//    $header = json_encode([
//        'typ' => 'JWT',
//        'alg' => 'HS256'
//    ]);
//
//// Create the token payload
//    $payload = json_encode([
//        'user_id' => $_POST['user_id'],
//        'nom' => $_POST['nom'],
//        'prenom' => $_POST['prenom'],
//        'date_crea' => $_POST['date_crea'],
//        'statut' => $_POST['statut'],
//        'exp' => time()+54000000
//    ]);
//
//
//// Encode Header
//    $base64UrlHeader = base64UrlEncode($header);
//
//// Encode Payload
//    $base64UrlPayload = base64UrlEncode($payload);
//
//// Create Signature Hash
//    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
//
//// Encode Signature to Base64Url String
//    $base64UrlSignature = base64UrlEncode($signature);
//
//// Create JWT
//    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
//
//    return $jwt ;
//}

//function getToken($key, $payload, $exp)
function getToken($key, $payload)
{
//si le ldap et la clé ne sont pas fournis, j'abandonne la génération du token
    if (empty($payload) || empty($key)) {
        return false;
    } else{
//je déclare le 'IssuedAt'
//    $iat = strtotime(date("Y-m-d H:i:s"));
//si le 'Expire' n'est pas précisé, je l'initialise au début de la journée suivante
//    if(is_null($exp) || empty($exp) || !is_int($exp))
//    {
//        $exp = new DateTime(date("Y-m-d 00:00:00"));
//        $exp = $exp->add(new DateInterval('P1D'))->getTimestamp();
//    }
////j'initialise le tableau qui contiendra le payload
//    $payload = array(
//        'user_id' => $_POST['user_id'],
//        'nom' => $_POST['nom'],
//        'prenom' => $_POST['prenom'],
//        'date_crea' => $_POST['date_crea'],
//        'statut' => $_POST['statut'],
//        'exp' => time()+54000000
//    );
//je retourne le token JWT
        return JWT::encode($payload, $key, "HS256");
}
}
?>
