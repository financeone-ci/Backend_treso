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
}
else {
    echo json_encode(["reponse" => "error","message" => "url invalide...","data" => $_POST], JSON_UNESCAPED_UNICODE);
    die();
}


$t = array();

$sql = "UPDATE `droits` SET  `$champ` = '$val' WHERE `droits`.`droits_id` = :tid";
$response = apiCreator($DB, $sql, "update", $t, false);
// Audits
AuditSystem($DB, "Modification", "Modification de droits ", $response);

echo $response;