<?php
// Mise à jour profil**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreatorDouble.php'; // créateur d'API


$t = array(
    'tdescription' => 'description',
    'tlibelle' => 'libelle',
    
);
$sql = "UPDATE `profil` SET `profil_libelle` = :tlibelle, `Profil_description` = :tdescription WHERE `profil`.`profil_id` = :tid";
$response = apiCreatorDouble($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification de profil", $response);

echo $response;