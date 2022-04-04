<?php
// suppression de banque**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreatorDouble.php'; // créateur d'API
require_once '../../fonctions/urlCatch.php'; // interroger une API
$idDelete = secure($_GET['id']);
$t = array();
$req =  "DELETE FROM `banque` WHERE IDBANQUE = :tid" ;
$req2 =  "DELETE FROM `dimcheque` WHERE `IDBANQUE` = $idDelete" ;
$reponse = apiCreatorDouble($DB,$req, $req2, "", "delete",$t,false);
// Audits
AuditSystem($DB, "Suppression", "Suppression de banque ", $reponse);    
  
echo $reponse;