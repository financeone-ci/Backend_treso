<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tcode' => 'code',
    'tbeneficiaire' => 'beneficiaire',
    'ttel' => 'contact',
    'tadresse' => 'adresse',
    'treference' => 'reference',
    'tcivilite' => 'civilite',
    'tnom' => 'nom',
    'tfonction' => 'fonction',
  
);
$req =  "INSERT INTO `tiers` ( `CODE_TIERS`, `TEL_TIERS`, `ADRESSE_TIERS`, `BENEFICIAIRE_TIERS`, `REF_TIERS`,  `CIV_REPRESENTANT_TIERS`, `NOM_REPRESENTANT_TIERS`, `FONCTION_REPRESENTANT_TIERS`, `ID_SOCIETE`) VALUES ( :tcode, :ttel, :tadresse, :tbeneficiaire, :treference,  :tcivilite, :tnom, :tfonction, :societe);" ;
$response = apiCreator($DB, $req, "create", $t);
// Audits
AuditSystem($DB, "Création", "Création d'un nouveau type tiers",  $response);

echo $response;
