<?php

// Lecture des users**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


// Lecture d'un user
if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = secure($_GET['id']);
    $sql = "SELECT `ID_TYPE_BUDGET` AS id, `CODE_TYPE_BUDGET`, `LIBELLE_TYPE_BUDGET`, `SENS_TYPE_BUDGET` FROM `type_budget`   WHERE ID_TYPE_BUDGET = '$id'";
// Lecture de tous les users
}else{
    $sql = "SELECT `ID_TYPE_BUDGET` AS id, `CODE_TYPE_BUDGET`, `LIBELLE_TYPE_BUDGET`, `SENS_TYPE_BUDGET` FROM `type_budget`   ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[],false);
echo $reponse;