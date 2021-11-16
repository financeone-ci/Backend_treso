<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
$obj = json_decode(file_get_contents('php://input'));
$infoHttp = array();

try{
    // Récupérer les chèques non imprimés du compte
    if(isset($_GET['cpte']) && !empty($_GET['cpte']))
    {
        $cpte = $_GET['cpte'];
        $sql = "SELECT *
                FROM dimcheque JOIN banque ON dimcheque.IDBANQUE  = banque.IDBANQUE 
                WHERE dimcheque.IDBANQUE = (SELECT IDBANQUE FROM compte WHERE CODE_COMPTE = '$cpte')";
        $req = $DB->query($sql);
        $dataPosition = $req->fetchAll(PDO::FETCH_OBJ);
        $req->closeCursor();
        $infoHttp = [
            "reponse" => "success",
            "infos" => $dataPosition,
        ];  
    }else{
        $infoHttp = [
            "reponse" => "success",
            "infos" => "Paramètres incorrects.",
        ]; 
    }
}catch(PDOException $e)
{
    $infoHttp = [
        "reponse" => "error",
        "infos" => "Impossible de charger les données.",
    ];
}

// affichage en JSON
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);