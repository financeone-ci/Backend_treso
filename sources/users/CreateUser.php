<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$pwd = sha1("adminf1");
//$pwd = password_hash("adminf1", PASSWORD_DEFAULT);
$t = array(
    'tnom' => 'nom',
    'tprenom' => 'prenom',
    'tlogin' => 'login',
  //  'tpwd' => $pwd,
    'temail' => 'email',
    'ttel' => 'tel',
    'trole' => 'role',
    'tprofil' => 'profil',
);
$req =  "INSERT INTO `user` (`user_nom`, `user_prenom`, `user_login`, `user_pwd`, `user_email`, `user_tel`, `user_role`, `profil_id`) VALUES (:tnom, :tprenom, :tlogin, '$pwd', :temail, :ttel, :trole,  :tprofil)" ;
$response = apiCreator($DB, $req, "create", $t, false);
// Audits
AuditSystem($DB, "Création", "Création de user",  $response);

echo $response;