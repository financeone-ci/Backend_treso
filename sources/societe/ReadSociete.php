<?php
// Lecture des sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API
require_once '../../fonctions/getToken.php';

// Lecture d'une société
if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = secure($_GET['id']);
    $sql = "SELECT `ID_SOCIETE` AS id,`CODE_SOCIETE`,`LIBELLE_SOCIETE`,`COMPLEMENT_SOCIETE`,`ADRESSE_SOCIETE`,`TEL_SOCIETE`,`FAX_SOCIETE`,`EMAIL_SOCIETE`,`SIEGE` FROM `societe`  
        WHERE ID_SOCIETE = '$id'";
// Lecture de toutes les sociétés
}else{
    $sql = "SELECT `ID_SOCIETE` AS id,`CODE_SOCIETE`,`LIBELLE_SOCIETE`,`COMPLEMENT_SOCIETE`,`ADRESSE_SOCIETE`,`TEL_SOCIETE`,`FAX_SOCIETE`,`EMAIL_SOCIETE`,`SIEGE` FROM `societe`"; 
}
// reponse de l'API
$reponse = apiCreator($DB, $sql, 'read', [], false);
echo $reponse;