<?php
// Mise à jour devise**************************

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

$sql = "UPDATE `structure_fichier` SET `code_structure_fichier` = :tcode, `desc_structure_fichier` = :tdescription, `pos_taxe_engagement` = :tpos_taxe, `pos_num_engagement` = :tpos_num, `pos_beneficiaire` = :tpos_benef, `pos_ref_beneficiaire` = :tpos_ref_benef, `pos_montant_engagement` = :tpos_montant, `pos_num_bon_commande` = :tpos_num_bon, `pos_motif_engagement` = :tpos_motif, `pos_date_echeance` = :tpos_echeance, `pos_code_budget` = :tpos_budget, `pos_date_engagement` = :tpos_date, `pos_ref_marche` = :tpos_marche, `pos_retenue_engagement` = :tpos_retenue WHERE `structure_fichier`.`idstructure_fichier` =  :tid;";
$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification de devise ", $response);


echo $response;