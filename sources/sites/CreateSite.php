<?php
// Création de site**************************

require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
$header = apache_request_headers();

if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)
{
    if(isset($obj) && !empty($obj))
    {
        $t = array(
            'tcode' => secure($obj->code),
            'tdesc' => secure($obj->desciption),
            'trepre' => secure($obj->representant),
            'tlocal' => secure($obj->localisation),
            'tsoci' => secure($obj->societe),
        );
        try{
            $req = $DB->prepare("INSERT INTO sites VALUES(:tcode, :tdesc, :trepre, :tlocal, :tsoci)");
            $req->execute($t);
            $infoHttp = [
                "reponse" => "success",
                "message" => "Enregistré",
            ]; 
        } catch (PDOException $e) {
            //throw $th;
            $MYSQL_DUPLICATE_CODES=array(1062, 23000);

            if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES)) {
                // duplicate entry, do something else
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Code déjà utilisé",
                ];
            } else {
                // an error other than duplicate entry occurred
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Enregistrement impossible",
                ];
            }
        }
    }else{
        $infoHttp = [
            "reponse" => "error",
            "message" => "Impossible de lire les données",
        ]; 
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Accès refusé.",
    ]; 
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);