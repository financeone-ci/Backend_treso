<?php
// suppression de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array();
$req =  "DELETE FROM rejet WHERE IDIMPORT IN (:tid) " ;
if( apiCreator($DB, $req, "delete", $t,false)){
$req =  "DELETE FROM engagement WHERE IDIMPORT IN (:tid) " ;
apiCreator($DB, $req, "delete", $t,false);
$req =  "DELETE FROM import WHERE IDIMPORT IN (:tid)" ;
$reponse = apiCreator($DB, $req, "delete", $t,false);

// Audits
AuditSystem($DB, "Suppression", "Suppression d'importation ", $reponse);
 }
       
     
  
echo $reponse;