<?php
// suppression de categorie**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array();
$req =  "DELETE FROM `categorie_paiement` WHERE `ID_CATEGORIE_PAIEMENT` = :tid" ;
$reponse = apiCreator($DB, $req, "delete", $t,false);
// Audits
AuditSystem($DB, "Suppression", "Suppression de catégorie paiement ", $reponse);
        

echo $reponse;