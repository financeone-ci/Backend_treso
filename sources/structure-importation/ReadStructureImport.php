<?php

// Lecture des sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


// Lecture d'un site
if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = secure($_GET['id']);
    $sql = "SELECT idstructure_fichier as id, code_structure_fichier as code, desc_structure_fichier as description, pos_taxe_engagement as pos_taxe, pos_num_engagement as pos_num,
    pos_beneficiaire as pos_benef, pos_ref_beneficiaire as pos_ref_benef, pos_montant_engagement as pos_montant, pos_num_bon_commande as pos_num_bon,
    pos_motif_engagement as pos_motif, pos_date_echeance as pos_echeance, pos_code_budget as pos_budget, pos_date_engagement as pos_date, pos_ref_marche as pos_marche, pos_retenue_engagement as pos_retenue
FROM structure_fichier
ORDER BY code_structure_fichier 
    WHERE idstructure_fichier = '$id'";
// Lecture de tous les sites
}else{
    $sql = "SELECT idstructure_fichier as id, code_structure_fichier as code, desc_structure_fichier as description, pos_taxe_engagement as pos_taxe, pos_num_engagement as pos_num,
    pos_beneficiaire as pos_benef, pos_ref_beneficiaire as pos_ref_benef, pos_montant_engagement as pos_montant, pos_num_bon_commande as pos_num_bon,
    pos_motif_engagement as pos_motif, pos_date_echeance as pos_echeance, pos_code_budget as pos_budget, pos_date_engagement as pos_date, pos_ref_marche as pos_marche, pos_retenue_engagement as pos_retenue
FROM structure_fichier ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql,"read");
echo $reponse;