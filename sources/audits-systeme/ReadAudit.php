<?php

// Lecture des audit**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


    $sql = "SELECT `audit_sys_id` AS id, `audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_item_id`, `audit_sys_nouvelleValeur`, `audit_sys_date`, `audit_sys_userid` FROM `audit_sys`   ";  

// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[],false);
echo $reponse;