<?php
// Mise à jour devise**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
    'tcode' => 'CODE_BANQUE',
    'tlibelle' => 'LIBELLE_BANQUE',
    'tdirecteur' => 'DG',
    'tadresse' => 'ADRESSE_BANQUE',
    'tcontact' => 'CONTACT_BANQUE',
    'tgestionnaire' => 'GESTIONNAIRE',
    'tadresse_web' => 'ADRESSE_WEB_BANQUE',
);

$sql = "UPDATE `banque` SET `CODE_BANQUE`= :tcode, `LIBELLE_BANQUE`= :tlibelle, `DG`= :tdirecteur, `GESTIONNAIRE`= :tgestionnaire, `ADRESSE_BANQUE`=:tadresse, `ADRESSE_WEB_BANQUE`=:tadresse_web, `CONTACT_BANQUE`= :tcontact  WHERE `IDBANQUE` =  :tid;";
$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification de banque ", $response);


echo $response;