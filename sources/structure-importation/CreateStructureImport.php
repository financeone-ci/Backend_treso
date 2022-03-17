<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
    'tcode' => 'code',
    'tdescription' => 'description',
    'tpos_taxe' => 'pos_taxe',
    'tpos_benef' => 'pos_benef',
    'tpos_ref_benef' => 'pos_ref_benef',
    'tpos_montant' => 'pos_montant',
    'tpos_num' => 'pos_num',
    'tpos_motif' => 'pos_motif',
    'tpos_echeance' => 'pos_echeance',
    'tpos_budget' => 'pos_budget',
    'tpos_num_bon' => 'pos_num_bon',
    'tpos_date' => 'pos_date',
    'tpos_marche' => 'pos_marche' ,
    'tpos_retenue' => 'pos_retenue',
);

$req =  "INSERT INTO structure_fichier (code_structure_fichier, desc_structure_fichier, pos_taxe_engagement, pos_beneficiaire, pos_ref_beneficiaire, pos_montant_engagement, pos_num_engagement, pos_motif_engagement, 
pos_date_echeance, pos_code_budget, pos_num_bon_commande, pos_date_engagement, pos_ref_marche, pos_retenue_engagement, `ID_SOCIETE`)
VALUES (:tcode, :tdescription, :tpos_taxe, :tpos_benef, :tpos_ref_benef, :tpos_montant, :tpos_num, :tpos_motif, :tpos_echeance, :tpos_budget, :tpos_num_bon, :tpos_date, :tpos_marche, :tpos_retenue, :societe)" ;
$response = apiCreator($DB, $req, "create", $t);
// Audits
AuditSystem($DB, "Création", "Création de nouvelle structure",  $response);

echo $response;