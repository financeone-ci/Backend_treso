<?php

// Lecture des sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';


    $sql = "SELECT securite_id as id, securite_taille, securite_majuscule, securite_carc_speciaux, securite_chiffres, securite_duree_pwd, valider,  autoriser, approuver FROM securite ";  

// reponse de l'API
$reponse = apiCreator($DB, $sql,"read",[], false);
echo $reponse;