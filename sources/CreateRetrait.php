<?php

// Enregistrement et Modification des retraits de paiements **************************
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
require_once '../fonctions/getToken.php';

header("Access-Control-Allow-origin: {$_SERVER['HTTP_ORIGIN']}");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");

// $obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
$header = apache_request_headers();

if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)
{
    if(isset($_GET['id']) && !empty($_GET['id']))
    {
        $succesFile = 0;
        $echecsFile = 0;
        $listId = explode(",",$_GET['id']);
        
        // Création des dossiers et Importation des fichiers 
        if(!empty($_FILES['fichier']))
        {
            foreach($listId as $idUnique )
            {    
                $fileLocation = '../uploads/paiements/retraits/'.$idUnique;
                mkdir($fileLocation);
                foreach ($_FILES['fichier']['name'] as $key => $value) 
                {
                    if(copy($_FILES['fichier']['tmp_name'][$key],  $fileLocation . '/'.$_FILES['fichier']['name'][$key]))
                    {
                        $succesFile ++;
                    } else   
                        $echecsFile ++;   
                }    
            }
        }
    
    $id = $_GET['id'];
    try{
        $benef = secure($_POST['beneficiaire']);
        $ident = secure($_POST['identite']);

        $t1 = array(
            'tbenef' => $benef,
            'tident' => $ident,
            'tuser' => $user,
        );
        $req = $DB->prepare("UPDATE paiement SET BENEF_REMISE = :tbenef, REF_REMISE = :tident, USER_REMISE = :tuser, DATE_REMISE = CURDATE() WHERE IDPAIEMENT IN ($id)");
        $req->execute($t1);

         $infoHttp = [
            "reponse" => "success",
            "message" => "Enregistré.",
            "succes" =>  $succesFile,
            "echecs" =>  $echecsFile,
         ]; 
    }catch(PDOException $e){
        $infoHttp = [
            "reponse" => "error",
            "message" => "Enregistrement impossible.",
            "succes" =>  $succesFile,
            "echecs" =>  $echecsFile,
        ]; 
    }
    }else{
        $infoHttp = [
            "reponse" => "error",
            "message" => "Enregistrement impossible.",
            ]; 
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Accès refusé.",
    ]; 
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
