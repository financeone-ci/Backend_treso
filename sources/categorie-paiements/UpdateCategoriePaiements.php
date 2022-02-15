<?php
// Mise à jour categorie**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
    
);
$sql = "UPDATE `categorie_paiement` SET `CODE_CATEGORIE_PAIEMENT` = :tcode, `LIB_CATEGORIE_PAIEMENT` = :tlibelle WHERE `categorie_paiement`.`ID_CATEGORIE_PAIEMENT` = :tid;";
$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification de categorie ", $response);


echo $response;