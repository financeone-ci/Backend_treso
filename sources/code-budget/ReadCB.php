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
    $sql = "SELECT `ID_CB` AS id, `CODE_CB`, `LIB_CB`, `type_budget`.`ID_TYPE_BUDGET`, `CODE_TYPE_BUDGET` FROM `code_budgetaire` JOIN type_budget ON `type_budget`.`ID_TYPE_BUDGET` = `code_budgetaire`.`ID_TYPE_BUDGET`  WHERE ID_CB = '$id'";
// Lecture de tous les users
}else{
    $sql = "SELECT `ID_CB` AS id, `CODE_CB`, `LIB_CB`, `type_budget`.`ID_TYPE_BUDGET`, `CODE_TYPE_BUDGET` FROM `code_budgetaire` JOIN type_budget ON `type_budget`.`ID_TYPE_BUDGET` = `code_budgetaire`.`ID_TYPE_BUDGET`  ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql,"read");
echo $reponse;