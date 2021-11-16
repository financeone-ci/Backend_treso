<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
require_once '../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
if(isset($_GET['jeton'])){
                    $jeton = secure($_GET['jeton']);
                 }else{
                    $jeton = "";
                 }
if(isset($_GET['type']) && !empty($_GET['type'])){
    switch ($_GET['type']){
        case 'C': // creation
           
           
           
            break;
        case 'R':
            if(isset($_GET['filter']) ){
            try{
                               // $sens = secure($obj->sens);
                                $sql = "SELECT * FROM `categorie_flux` WHERE `SENS_CATEGORIE_FLUX` = '".secure($_GET['filter'])."' ";
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
                try{
                    $sql = "SELECT * FROM `categorie_flux` ";
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

