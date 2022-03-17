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
    $sql = "SELECT `user_id` AS id, `user_nom`, `user_prenom`, `user_expiration`, `user_login`,  `user_email`, `user_tel`, `user_role`, `user_cnx`, `user_decnx`, `user_actif`, user.`profil_id`, profil_libelle AS profil FROM `user` JOIN profil ON profil.profil_id = user.profil_id  WHERE user_id = '$id'";
// Lecture de tous les users
}else{
    $sql = "SELECT `user_id` AS id, `user_nom`, `user_prenom`, `user_expiration`, `user_login`,  `user_email`, `user_tel`, `user_role`, `user_cnx`, `user_decnx`, `user_actif`, user.`profil_id`, profil_libelle AS profil FROM `user` JOIN profil ON profil.profil_id = user.profil_id ";  
}
// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[],false);
echo $reponse;