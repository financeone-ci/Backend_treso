<?php
// Mise à jour devise**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tnom' => 'nom',
    'tprenom' => 'prenom',
    'tlogin' => 'login',
    'temail' => 'email',
    'ttel' => 'tel',
    'trole' => 'role',
    'tprofil' => 'profil',
);
$sql =  "UPDATE `user` SET `user_nom`=:tnom, `user_prenom`= :tprenom, `user_login`= :tlogin,  `user_email`= :temail, `user_tel`=:ttel, `user_role`= :trole, `profil_id`= :tprofil WHERE user_id = :tid" ;

$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification utilisateur ", $response);


echo $response;