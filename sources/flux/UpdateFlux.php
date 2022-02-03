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
$reponse = apiCreator($DB, $sql, "update", $t);

echo $reponse;