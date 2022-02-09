<?php
// Création de site**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
    'tcode' => 'code',
    'tdesc' => 'description',
    'trepre' => 'representant',
    'tlocal' => 'local',
);
$req =  "INSERT INTO sites(CODE_SITE, DESCRIPTION_SITE, REPRESENTANT_SITE, LOCALISATION_SITE, ID_SOCIETE) VALUES(:tcode, :tdesc, :trepre, :tlocal, :societe)" ;
$response = apiCreator($DB, $req, "create", $t, true);

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