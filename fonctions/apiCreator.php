<?php 
function apiCreator($DB, string $requete, string $type="read", array $donnees = [] /* crée une API et recupère la réponse */ ){
    $infoHttp = [ // reponse 
        "reponse" => "error",
        "message" => "...",
    ];
    $msg = "Enregistré";
    $header = apache_request_headers(); // autorisation 
    $obj = json_decode(file_get_contents('php://input')); 
    
    if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true){
        $donneesSecurisees = []; // données utilisées pour le traitement
        //// récupération des données depuis un formulaire
        if(!empty($donnees)){
            foreach ($donnees as $key => $value) {
                # code...
                if(!isset($obj->values->$value)){
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Le champ $value est inconnu",
                    ]; 
                return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
                // die();
                }
                $donneesSecurisees[$key] = secure($obj->values->$value);
            }
        }

//////////////////////////////////////////////////////
       // Lecture 
        if($type == "read" )
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

///////////////////////////////////////////////////////
        // mise à jour , suppression
            if($type == "update" OR  $type == "delete" ){
                $msg = "Opération effectuée";
                if(isset($obj) && !empty($obj) && isset($_GET['id']) && !empty($_GET['id']))
            {
                $id = $_GET['id'];
                $donneesSecurisees['tid'] = $id;
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Impossible de lire les données",
                ]; 
                return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
                // die();
                }
            }
            
////////-----------------------------------------------------//////
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
        
        }
    

    }else{
        $infoHttp = [
            "reponse" => "error",
            "message" => "Accès refusé.",
        ]; 
    }
    

   return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);

}