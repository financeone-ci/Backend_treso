<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
    'tbudget' => 'budget',

);
$req =  "INSERT INTO `code_budgetaire` (`CODE_CB`, `LIB_CB`, `ID_TYPE_BUDGET`, `ID_SOCIETE`) VALUES ( :tcode, :tlibelle, :tbudget, :societe)" ;
$response = apiCreator($DB, $req, "create", $t, true);
// Audits
AuditSystem($DB, "Création", "Création de code budgétaire",  $response);

echo $response;