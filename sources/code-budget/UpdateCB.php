<?php
// Mise à jour devise**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
    'tbudget' => 'budget',

);
$sql =  "UPDATE `code_budgetaire` SET `CODE_CB` = :tcode, `LIB_CB` = :tlibelle, ID_TYPE_BUDGET = :tbudget WHERE `code_budgetaire`.`ID_CB` = :tid" ;

$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification code budgétaire ", $response);


echo $response;