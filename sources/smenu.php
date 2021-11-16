<?php
header('Content-Type: application/json; charset=utf8');
header('Access-Control-Allow-origin: *');
header('Access-Control-Allow-Headers: *');
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
//require_once '../fonctions/getToken.php';
$infoHttp = array();
if(isset($_GET['type'] )){
     
    switch ($_GET['type']){

        case 'R': // Lecture
                try{
                    $sql = "SELECT`smenu_id`, `smenu_libelle` FROM `smenu`";
                    $req = $DB->query($sql);
                 
                        $d = $req->fetchAll(PDO::FETCH_OBJ);
                        $infoHttp = [
                            "reponse" => "success",
                            "infos" => $d,
                        ];
                }catch (PDOException $e)
                {
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                        "jeton" => false,
                    ];
                }
            break;


        default :
        $infoHttp = [
            "reponse" => "error",
            "message" => "Impossible d'afficher des données",
        ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Impossible d'afficher des données",
    ];
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);?>