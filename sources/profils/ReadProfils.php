<?php

// Lecture des profils**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreatorDouble.php'; // créateur d'API
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/urlCatch.php'; // interroger une API


// Lecture de tous les profils

    $sql = "SELECT profil.`profil_id` AS id, `profil_libelle`, `Profil_description`, `id_societe` FROM `profil`  ";  
    $sql2 = "SELECT * FROM `droits` JOIN e_smenu ON droits.e_smenu_id = e_smenu.e_smenu_id ";  

// reponse de l'API
$reponse = apiCreatorDouble($DB, $sql, $sql2,  "profil_id");



echo $reponse;