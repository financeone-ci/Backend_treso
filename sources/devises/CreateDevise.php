<?php
// Création de devise**************************
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
$req =  "INSERT INTO `devise` (`CODE_DEVISE`, `LIBELLE_DEVISE`, `TAUX_DEVISE`, `DEVISE_DE_BASE`) VALUES ( :tcode, :tlibelle, :ttaux, :tbase);" ;
$reponse = apiCreator($DB, $req, "create", $t);
echo $reponse;
