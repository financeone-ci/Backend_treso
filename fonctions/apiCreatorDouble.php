<?php 
function apiCreatorDouble($DB, string $requete, string $requete2, $cleEtrangere ="", string $type="read", array $donnees = [], $societe = true /* crée une API et recupère la réponse */ ){
    $infoHttp = [ // reponse 
        "reponse" => "error",
        "message" => "...",
        "data" => [],
    ];
    $data = [];
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
                        "payload" => $payload,
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
            if($societe ==true ){
                $requete.= " WHERE ID_SOCIETE = $soci";
            }
            
            $req = $DB->query($requete);
            $data = $req->fetchAll(PDO::FETCH_OBJ);
            
            foreach ($data as $key => $value) {
                $cle = $value->id;
                $req2 = $DB->query($requete2." WHERE `$cleEtrangere` =  '$cle'");
                $data[$key]->donneesSup =  $req2->fetchAll(PDO::FETCH_OBJ);
            }
            $req->closeCursor();
            $infoHttp = [
                "reponse" => "success",
                "infos" => $data,
                "payload" => $payload,
               
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
                            "payload" => $payload,
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
                            "payload" => $payload,
                        ]; 
                        return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
                    }
                }
            }
            $tdata = $donneesSecurisees;
            // traitement création, mise à jour , suppression //
            try{
                // exécution de requete 2
                if($DB->exec($requete2)){}
                else {
                 return [
                    "reponse" => "error",
                    "message" => "Opération impossible",
                    "payload" => $payload,
                    "data" => $tdata,
                    ];  
                }
                $req = $DB->prepare($requete);
                $req->execute($donneesSecurisees);
                if($type == "update" OR  $type == "delete" ){
                    $payload->item_id = strval($donneesSecurisees['tid']);
                }else{
                    $payload->item_id = strval($DB->lastInsertId());
                }
                $infoHttp = [
                    "reponse" => "success",
                    "message" => $msg,
                    "payload" => $payload,
                    "data" => $tdata,
                ]; 
            }catch (PDOException $e) {
                if($type == "update" OR  $type == "delete" ){
                    $payload->item_id = strval($donneesSecurisees['tid']);
                }else{
                     $payload->item_id = 0;
                }
                // code déjà utilisé
                if(strpos($e->getMessage(), "1062" )){
                    return json_encode([
                            "reponse" => "error",
                            "message" => "Code déjà utilisé",
                            "payload" => $payload,
                            "data" => $tdata,
                        ], JSON_UNESCAPED_UNICODE);
                }
                // supprimer clé étrangère
                elseif(strpos($e->getMessage(), "1451" )){
                    return json_encode([
                            "reponse" => "error",
                            "message" => "Impossible de supprimer",
                            "payload" => $payload,
                            "data" => $tdata,
                        ], JSON_UNESCAPED_UNICODE);
                }
                else{
                    $MYSQL_DUPLICATE_CODES=array(1062, 23000);
                    if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES)) {
                        // doublon
                        if($e->getCode() == 1062){
                            $infoHttp = [
                                "reponse" => "error",
                                "message" => "Code déjà utilisé",
                                "payload" => $payload,
                                "data" => $tdata,
                            ];
                        }else{
                            $infoHttp = [
                                "reponse" => "error",
                                "message" => "Suppression impossible".$e->getMessage(),
                                "payload" => $payload,
                                "data" => $tdata,
                            ];
                        }
                    } else {
                        // an error other than duplicate entry occurred
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => $e->getMessage(),
                            "payload" => $payload,
                            "data" => $tdata,
                        ];
                    }  
                }
                
            }
        }
    }else{
        $infoHttp = [
            "reponse" => "error",
            "message" => "Accès refusé",
        ]; 
    }
   return json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
}