<?php
// suppression de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array();
$req =  "DELETE FROM `droits` WHERE `droits`.`profil_id` = :tid" ; /* id du profil  */
$reponse = apiCreator($DB, $req, "delete", $t,false);
 
echo $reponse;