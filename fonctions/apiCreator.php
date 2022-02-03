<?php 
function apiCreator($DB, string $requete, string $type="read", array $donnees = [], $societe = true /* crée une API et recupère la réponse */ ){
    $infoHttp = [ // reponse 
        "reponse" => "error",
        "message" => "...",
    ];
    $msg = "Enregistré avec succès";
    $header = apache_request_headers(); // autorisation 
    $obj = json_decode(file_get_contents('php://input')); 
    if (isset($obj->values)){
        $obj = $obj->values;
    } 
    
    if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true){

        // Récupération de la société
        $jeton = $header['Authorization'];
        $payload = tokenData($jeton);
        $soci = $payload->user_societe;

        $donneesSecurisees = []; // données utilisées pour le traitement
        //// récupération des données depuis un formulaire
        if(!empty($donnees)){
            foreach ($donnees as $key => $value) {
                # code...
                if(!isset($obj->$value)){
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Champ $value inconnu",
                    ]; 
                return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
                }
                $donneesSecurisees[$key] = secure($obj->$value);
            }
            if($societe == true){
               $donneesSecurisees['societe'] = $soci; 
            }
        }
       // Lecture 
        if($type == "read")
        {
            $req = $DB->query($requete);
            $data = $req->fetchAll(PDO::FETCH_OBJ);
            $req->closeCursor();
            $infoHttp = [
                "reponse" => "success",
                "infos" => $data,
            ]; 
        }
        else{
        // mise à jour , suppression
            if($type == "update" OR  $type == "delete" ){
                if($type == "update"){
                    $msg = "Mise à jour avec succès";
                    if(isset($obj) && !empty($obj) && isset($_GET['id']) && !empty($_GET['id']))
                    {
                        $id = $_GET['id'];
                        $donneesSecurisees['tid'] = $id;
                    }else{
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Paramètres incorrects",
                        ]; 
                        return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
                    }
                }else{
                    $msg = "Supprimé avec succès";
                    if(isset($_GET['id']) && !empty($_GET['id']))
                    {
                        $id = $_GET['id'];
                        $donneesSecurisees['tid'] = $id;
                    }else{
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Paramètres incorrects",
                        ]; 
                        return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
                    }
                }
            }
        // traitement création, mise à jour , suppression //
            try{
                $req = $DB->prepare($requete);
                $req->execute($donneesSecurisees);
                $infoHttp = [
                    "reponse" => "success",
                    "message" => $msg,
                ]; 
            }catch (PDOException $e) {
                //throw $th;
                $MYSQL_DUPLICATE_CODES=array(1062, 23000);
                if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES)) {
                    // doublon
                    if($e->getCode() == 1062){
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Code déjà utilisé",
                        ];
                    }else{
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Suppression impossible",
                        ];
                    }
                } else {
                    // an error other than duplicate entry occurred
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Service indisponible",
                    ];
                }
            }
        }
    }else{
        $infoHttp = [
            "reponse" => "error",
            "message" => "Accès refusé.",
        ]; 
    }
   return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
}