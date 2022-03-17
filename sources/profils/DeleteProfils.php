<?php
// suppression de profil**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreatorDouble.php'; // créateur d'API
require_once '../../fonctions/urlCatch.php'; // interroger une API
$idDelete = secure($_GET['id']);
$t = array();
$req =  "DELETE FROM `profil` WHERE `profil`.`profil_id` = :tid" ;
$req2 =  "DELETE FROM `droits` WHERE `profil_id` = $idDelete" ;
$reponse = apiCreatorDouble($DB,$req, $req2, "", "delete",$t,false);
// Audits
AuditSystem($DB, "Suppression", "Suppression de profil ", $reponse);    
  
echo $reponse;