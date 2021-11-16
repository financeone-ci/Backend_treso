<?php
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
if(isset($_GET['jeton'])){
                    $jeton = secure($_GET['jeton']);
                 }else{
                    $jeton = "";
                 }
if(isset($_GET['type']) && !empty($_GET['type'])){
    // variables d'initialisation

   


    switch ($_GET['type']){
        case 'C': // creation
            
            break;
        case 'R':
            if(isset($_GET['id'])){
try{
                    $sql = "SELECT type_tiers.`ID_TYPE_TIERS` AS id, type_tiers.CODE_TYPE_TIERS, type_tiers.`LIBELLE_TYPE_TIERS`  FROM `tiers_type_tiers` JOIN type_tiers ON type_tiers.ID_TYPE_TIERS = tiers_type_tiers.ID_TYPE_TIERS WHERE ID_TIERS = ".secure($_GET['id']);
                    $req = $DB->query($sql);
                    $row = $req->fetchAll(PDO::FETCH_OBJ);
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row,
                    ];
                }catch (PDOException $e)
                {
  
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Lien incorrect*",
        "jeton" => false,
    ];
}
                
           
           
            
            break;
        case 'U': // MAJ
             
            break;
        case 'D':// Suppression 
         
            break;
        default :
            $infoHttp = [
                "reponse" => "error",
                "message" => "Lien incorrect",
                "jeton" => false,
            ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Lien incorrect",
        "jeton" => false,
    ];
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);

?>