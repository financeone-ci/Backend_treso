<?php

// Lecture des droits**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


// Lecture d'un droit
if(isset($_GET['id']) && !empty($_GET['id'])) // id du profil ****************
{
    $id = secure($_GET['id']);
    $sql = "SELECT * FROM `droits` WHERE `profil_id` = '$id'";
// 
}else{
    return  json_encode(["reponse" => "error","message" => "url invalide","data" => []],); 
}
// reponse de l'API
$reponse = apiCreator($DB, $sql);
echo $reponse;