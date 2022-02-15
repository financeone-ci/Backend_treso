<?php
// Création de site**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

// modifier dans la BDD
$t = array(
    'tcode' => 'code',
    'tlibelle' => 'libelle',
    'tcomplement' => 'complement',
    'tadresse' => 'adresse',
    'tcontact' => 'contact',
    'tfax' => 'fax',
    'temail' => 'email',
    'tsiege' => 'siege',
);

$req = $DB->prepare("INSERT INTO `societe` (`CODE_SOCIETE`, `LIBELLE_SOCIETE`, `COMPLEMENT_SOCIETE`, `ADRESSE_SOCIETE`, `TEL_SOCIETE`, `FAX_SOCIETE`, `EMAIL_SOCIETE`, `SIEGE`) VALUES (:tcode, :tlibelle, :tcomplement, :tadresse, :tcontact, :tfax, :temail, :tsiege);");

$response = apiCreator($DB, $req, "create", $t);

// Audits
AuditSystem($DB,  "Création", "Création de nouvelle société", $response);

echo $response;