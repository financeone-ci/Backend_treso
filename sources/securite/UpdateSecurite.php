<?php
// Mise à jour devise**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

if(isset($_GET['champ']) && isset($_GET['val']))
{
    $champ = $_GET['champ'];
    $val = $_GET['val'];
    
    $t = array();

    $sql = "UPDATE `securite` SET  `$champ` = '$val' WHERE `securite`.`securite_id` = :tid";
    $response = apiCreator($DB, $sql, "update", $t, false);
    // Audits
    AuditSystem($DB, "Modification", "Modification de sécurité ", $response);

}
else{
    $t = array(
        'tsecurite_duree_pwd' => 'securite_duree_pwd',
        'tsecurite_taille' => 'securite_taille',
    );
    $sql = "UPDATE `securite` SET `securite_taille` = :tsecurite_taille, `securite_duree_pwd` = :tsecurite_duree_pwd WHERE `securite`.`securite_id` = :tid";
    $response = apiCreator($DB, $sql, "update", $t, false);
    // Audits
    AuditSystem($DB, "Modification", "Modification de sécurité ", $response);
}


echo $response;