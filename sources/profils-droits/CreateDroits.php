<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

// recuperation des sous-menus
$reqSM = $DB->query("SELECT * FROM `e_smenu`");
$sousM = $reqSM->fetchAll(PDO::FETCH_OBJ);
$reqSM->closeCursor();

if(!empty($sousM)){
    foreach ($sousM as $key => $value) {
        $t = array(
            ':tsmenu' => $value->e_smenu_id,
            ':tprofil' => 'profil',  
            );
        $req =  "INSERT INTO `droits` ( `droits_lecture`, `droits_creer`, `droits_modifier`, `droits_supprimer`, `e_smenu_id`, `profil_id`) VALUES (  '0', '0', '0', '0', :tsmenu, :tprofil)" ;
        $response = apiCreator($DB, $req, "create", $t);

    }
}

/*
echo $response;
*/
