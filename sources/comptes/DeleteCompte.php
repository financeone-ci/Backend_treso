<?php
// suppression de société**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array();

$req =  "DELETE FROM `compte` WHERE `ID_COMPTE` = :tid" ;

$reponse = apiCreator($DB, $req, "delete", $t, false);
// Audits
AuditSystem($DB, "Suppression", "Suppression d'un compte", $reponse);
        
echo $reponse;