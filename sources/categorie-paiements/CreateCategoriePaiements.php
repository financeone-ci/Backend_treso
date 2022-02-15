<?php
// Création de categorie**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
);
$req =  "INSERT INTO `categorie_paiement` (`CODE_CATEGORIE_PAIEMENT`, `LIB_CATEGORIE_PAIEMENT`, `ID_SOCIETE`) VALUES ( :tcode, :tlibelle, :societe);" ;
$response = apiCreator($DB, $req, "create", $t, true);
// Audits
AuditSystem($DB, "Création", "Création de nouvelle catégorie paiement",  $response);

echo $response;
