<?php
// suppression de site**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array();
$req =  "DELETE FROM `sites` WHERE `sites`.`ID_SITE` = :tid" ;
$response = apiCreator($DB, $req, "delete", $t, false);

// Audits
AuditSystem($DB, "Suppression site", "Suppression de site", $response);
echo $response;