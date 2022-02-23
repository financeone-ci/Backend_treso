<?php
// Mise à jour devise**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
    
);
$sql = "UPDATE `type_tiers` SET `LIBELLE_TYPE_TIERS` = :tlibelle, `CODE_TYPE_TIERS` = :tcode WHERE `type_tiers`.`ID_TYPE_TIERS` = :tid";
$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification de type tiers ", $response);

echo $response;