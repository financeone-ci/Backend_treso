<?php

// Envoi de password par mail**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/sendMail.php';

// Récupération des données postées dans $obj
$obj = json_decode(file_get_contents('php://input'));
$obj = $obj->values;

$infoHttp = array();

if(isset($obj) && !empty($obj))
{
    $mail = secure($obj->mail);
    $mailData = [];
    // controle du mail
    $sql = "SELECT user_email, user_pwd FROM user WHERE user_email = '$mail'";
    $req = $DB->query($sql);
    $row = $req->rowCount();
    if( $row > 0){
        // E-mail trouvé
        $d = $req->fetch();
        $data = $d['user_pwd'];
        $mailData = [
            'sujet' => "Mot de passe oublié",
            'texte' => "Votre mot de passe est ".$data.". Pour des raisons de sécurité, veuillez supprimer ce mail."
        ];

        if(envoiMail($mail, $mailData) === true)
        {
            // e-mail envoyé
            $infoHttp = [
                "reponse" => "success",
                "message" => "Mot de passe envoyé",
            ]; 
        }else{
            // email non envoyé
            $infoHttp = [
                "reponse" => "error",
                "message" => "Envoi impossible",
            ]; 
        }
    }else{
        // E-mail non trouvé
        $infoHttp = [
            "reponse" => "error",
            "message" => "Utilisateur introuvable",
        ]; 
    }
}else{
    $infoHttp = [
            "reponse" => "error",
            "message" => "Paramètres incorrects",
        ]; 
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);