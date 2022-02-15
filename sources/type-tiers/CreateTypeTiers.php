<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
  
);
$req =  "INSERT INTO `type_tiers` ( `LIBELLE_TYPE_TIERS`, `CODE_TYPE_TIERS`, `ID_SOCIETE`) VALUES (:tlibelle, :tcode, :societe)" ;
$response = apiCreator($DB, $req, "create", $t);
// Audits
AuditSystem($DB, "Création", "Création d'un nouveau type tiers",  $response);

echo $response;
