<?php
// Mise à jour de site**************************
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
$sql = "UPDATE sites 
        SET CODE_SITE = :tcode, DESCRIPTION_SITE = :tdesc, REPRESENTANT_SITE = :trepre, LOCALISATION_SITE = :tlocal
        WHERE ID_SITE = :tid";
$response = apiCreator($DB, $sql, "update", $t, false);

// Audits
AuditSystem($DB, "Modification", "Modification de site", $response);

echo $response;