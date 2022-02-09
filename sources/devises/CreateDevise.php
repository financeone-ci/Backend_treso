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
$response = apiCreator($DB, $req, "create", $t);

// Audits
$issue = json_decode($response);
if($issue->data)
{
    $data = $issue->data;
    if($issue->reponse === "error")
    {
        // $data = implode(" :: ", $data);
        $implode = "";
        foreach ($data as $key => $value) {
            # code...
            $implode .= $key.': '.$value.' ';
        }
        $data = $implode;
        AuditSystem($DB, $issue->payload->user_login, "Création site", "Création de nouveau site", "échec", $issue->payload->item_id, $data, $issue->payload->user_id, $issue->payload->user_societe);
    }
    if($issue->reponse === "success")
    {
        $implode = "";
        foreach ($data as $key => $value) {
            # code...
            $implode .= $key.': '.$value.' / ';
        }
        $data = $implode;
        AuditSystem($DB, $issue->payload->user_login, "Création site", "Création de nouveau site", "succès", $issue->payload->item_id, $data, $issue->payload->user_id, $issue->payload->user_societe);
    }
} 

echo $response;
