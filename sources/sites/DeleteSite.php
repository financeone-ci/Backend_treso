<?php
// suppression de site**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array();
$req =  "DELETE FROM `sites` WHERE `sites`.`ID_SITE` = :tid" ;
$response = apiCreator($DB, $req, "delete", $t, false);

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
        AuditSystem($DB, $issue->payload->user_login, "Suppression site", "Suppression de nouveau site", "échec", $issue->payload->item_id, $data, $issue->payload->user_id, $issue->payload->user_societe);
    }
    if($issue->reponse === "success")
    {
        $implode = "";
        foreach ($data as $key => $value) {
            # code...
            $implode .= $key.': '.$value.' / ';
        }
        $data = $implode;
        AuditSystem($DB, $issue->payload->user_login, "Suppression site", "Suppression de nouveau site", "succès", $issue->payload->item_id, $data, $issue->payload->user_id, $issue->payload->user_societe);
    }
} 

echo $response;