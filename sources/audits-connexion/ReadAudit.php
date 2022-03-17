<?php

// Lecture des audit**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


// Lecture d'un audit
if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = secure($_GET['id']);
    $sql = "SELECT `audit_cnx_id` AS id, `audit_cnx_userid`, `audit_cnx_usernom`, `audit_cnx_ip`, `audit_cnx_machine`, `audit_cnx_action`, `audit_cnx_issue`, `audit_cnx_description`, `audit_cnx_date` FROM `audit_cnx` FROM `audit_cnx`  
    WHERE audit_cnx_id = '$id'";
// Lecture de tous les audits
}else{
    $sql = "SELECT `audit_cnx_id` AS id, `audit_cnx_userid`, `audit_cnx_usernom`, `audit_cnx_ip`, `audit_cnx_machine`, `audit_cnx_action`, `audit_cnx_issue`, `audit_cnx_description`, `audit_cnx_date` FROM `audit_cnx`  ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[],false);
echo $reponse;