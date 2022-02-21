<?php
// Création de categorie**************************
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
$req =  "INSERT INTO `compte` ( `CODE_COMPTE`, `SOLDE_INITIAL_COMPTE`, `COMPTE_COMPTABLE`, `RIB`, `LIBELLE_COMPTE`, `GESTIONNAIRE_COMPTE`, `CIV_GESTIONNAIRE_COMPTE`, `SERVICE_GESTIONNAIRE_COMPTE`, `TEL_GESTIONNAIRE_COMPTE`, `EMAIL_GESTIONNAIRE_COMPTE`, `IDBANQUE`, `COMPTE_FICHIER`, `ID_SOCIETE`, `ID_DEVISE`) VALUES ( :tcode, :tsolde_i, :tcomptable, :trib, :tlibelle, :tgestionnaire, :tcivilite, :tservice, :ttel, :temail, :tbanque, :tfichier, :tsociete, :tdevise)";

$response = apiCreator($DB, $req, "create", $t, true);

// Audits
AuditSystem($DB, "Création", "Création de nouveau compte",  $response);

echo $response;
