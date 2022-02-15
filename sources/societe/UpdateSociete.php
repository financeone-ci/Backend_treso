<?php
// Mise à jour de societe**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
        'tcode' => 'code',
        'tdescription' => 'description',
        'tcomplement' => 'complement',
        'tadresse' => 'adresse',
        'ttel' => 'tel',
        'tfax' => 'fax',
        'temail' => 'email',
        'tsiege' => 'siege',
        'tid' => 'id',
    );
$sql = "UPDATE `societe` 
        SET `CODE_SOCIETE` = :tcode, `LIBELLE_SOCIETE` = :tdescription, `COMPLEMENT_SOCIETE` = :tcomplement, `ADRESSE_SOCIETE` = :tadresse, `TEL_SOCIETE` = :ttel, `FAX_SOCIETE` = :tfax, `EMAIL_SOCIETE` = :temail, `SIEGE` = :tsiege 
        WHERE `societe`.`ID_SOCIETE` = :tid";
        
$response = apiCreator($DB, $sql, "update", $t, false);

// Audits
AuditSystem($DB, "Modification", "Modification de site", $response);

echo $response;