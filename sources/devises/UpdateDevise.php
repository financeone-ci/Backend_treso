<?php
// Mise à jour devise**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
    'ttaux' => 'taux',
    'tbase' => 'base_devise',
);
$sql = "UPDATE `devise` SET `CODE_DEVISE` = :tcode, `LIBELLE_DEVISE` = :tlibelle, `TAUX_DEVISE` = :ttaux, `DEVISE_DE_BASE` = :tbase  WHERE `devise`.`IDDEVISE` = :tid;";
$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification de devise ", $response);


echo $response;