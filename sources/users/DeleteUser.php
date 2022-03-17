<?php
// suppression de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array();
$req =  "DELETE FROM `user` WHERE `user`.`user_id` = :tid" ;
if(apiCreator($DB," DELETE FROM `site_user` WHERE ID_USER = :tid", "delete", $t,false)){
  $reponse = apiCreator($DB, $req, "delete", $t,false);
    // Audits
    AuditSystem($DB, "Suppression", "Suppression d'utilisateur ", $reponse);  
}
    
  
echo $reponse;