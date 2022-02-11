<?php
// Création de site**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
    'tcode' => 'code',
    'tdesc' => 'description',
    'trepre' => 'representant',
    'tlocal' => 'local',
);
$req =  "INSERT INTO sites(CODE_SITE, DESCRIPTION_SITE, REPRESENTANT_SITE, LOCALISATION_SITE, ID_SOCIETE) VALUES(:tcode, :tdesc, :trepre, :tlocal, :societe)" ;
$response = apiCreator($DB, $req, "create", $t, true);

// Audits
AuditSystem($DB,  "Création site", "Création de nouveau site", $reponse); 
echo $response;