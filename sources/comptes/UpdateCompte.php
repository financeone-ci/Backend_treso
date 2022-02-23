<?php
// Mise à jour categorie**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
    'tcode' => 'code',
    'tsolde_i' => 'solde_i',
    'tcomptable' => 'comptable',
    'trib' => 'rib',
    'tlibelle' => 'libelle',
    'tgestionnaire' => 'gestionnaire',
    'tcivilite' => 'civilite',
    'tservice' => 'service',
    'ttel' => 'tel',
    'temail' => 'email',
    'tbanque' => 'banque',
    'tfichier' => 'fichier',
    'tsociete' => 'societe',
    'tdevise' => 'devise',
);
$sql = "UPDATE `compte` SET `CODE_COMPTE` = :tcode, `SOLDE_INITIAL_COMPTE` = :tsolde_i, `COMPTE_COMPTABLE` = :tcomptable, `RIB` = :trib, `LIBELLE_COMPTE` = :tlibelle, `GESTIONNAIRE_COMPTE` = :tgestionnaire, `CIV_GESTIONNAIRE_COMPTE` = :tcivilite, `SERVICE_GESTIONNAIRE_COMPTE` = :tservice, `TEL_GESTIONNAIRE_COMPTE` = :ttel, `EMAIL_GESTIONNAIRE_COMPTE` = :temail, `ID_SOCIETE` = :tsociete, IDBANQUE = :tbanque, `ID_DEVISE` = :tdevise WHERE `compte`.`ID_COMPTE` = :tid";

$response = apiCreator($DB, $sql, "update", $t, false);

// Audits
AuditSystem($DB, "Modification", "Modification de categorie ", $response);

echo $response;