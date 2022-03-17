<?php
// suppression de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array();
$req =  "DELETE FROM `code_budgetaire` WHERE `code_budgetaire`.`ID_CB` = :tid" ;
$reponse = apiCreator($DB, $req, "delete", $t,false);
  // Audits
AuditSystem($DB, "Suppression", "Suppression de code budgétaire ", $reponse);    
  
echo $reponse;