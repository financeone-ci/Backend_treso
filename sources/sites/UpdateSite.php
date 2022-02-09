<?php
// Mise à jour de site**************************
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
$sql = "UPDATE sites 
        SET CODE_SITE = :tcode, DESCRIPTION_SITE = :tdesc, REPRESENTANT_SITE = :trepre, LOCALISATION_SITE = :tlocal
        WHERE ID_SITE = :tid";
$response = apiCreator($DB, $sql, "update", $t, false);

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
        AuditSystem($DB, $issue->payload->user_login, "Mise à jour site", "Mise à jour de nouveau site", "échec", $issue->payload->item_id, $data, $issue->payload->user_id, $issue->payload->user_societe);
    }
    if($issue->reponse === "success")
    {
        $implode = "";
        foreach ($data as $key => $value) {
            # code...
            $implode .= $key.': '.$value.' / ';
        }
        $data = $implode;
        AuditSystem($DB, $issue->payload->user_login, "Mise à jour site", "Mise à jour de nouveau site", "succès", $issue->payload->item_id, $data, $issue->payload->user_id, $issue->payload->user_societe);
    }
} 

echo $response;