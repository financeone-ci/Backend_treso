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
           
            if(
                isset($obj->code) && !empty($obj->code)
                    
                    ){
                 
                $code = secure($obj->code);
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
               
                if(isset($obj->complement) )
                $complement = secure($obj->complement);
                else $complement ="";
               
                if(isset($obj->adresse) )
                $adresse = secure($obj->adresse);
                else $adresse ="";
               
                if(isset($obj->contact) )
                $contact = secure($obj->contact);
                else $contact ="";
               
                if(isset($obj->fax) )
                $fax = secure($obj->fax);
                else $fax ="";
               
                if(isset($obj->email) )
                $email = secure($obj->email);
                else $email ="";
               
                if(isset($obj->siege) )
                $siege = secure($obj->siege);
                else $siege ="";
               

                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'tlibelle' => $libelle,
                    'tcomplement' => $complement,
                    'tadresse' => $adresse,
                    'tcontact' => $contact,
                    'tfax' => $fax,
                    'temail' => $email,
                    'tsiege' => $siege,
                );
               
                $req1 = $DB->prepare("INSERT INTO `societe` (`CODE_SOCIETE`, `LIBELLE_SOCIETE`, `COMPLEMENT_SOCIETE`, `ADRESSE_SOCIETE`, `TEL_SOCIETE`, `FAX_SOCIETE`, `EMAIL_SOCIETE`, `SIEGE`) VALUES (:tcode, :tlibelle, :tcomplement, :tadresse, :tcontact, :tfax, :temail, :tsiege);");
                
                if($req1->execute($t1)){
// cas où la requête s'est bien exécutée

                        
 ///////////// Audit système ///////////////////
 $tnew = array(
    'tcode' => $code,
    'tlibelle' => $libelle,
    'tcomplement' => $complement,
    'tadresse' => $adresse,
    'tcontact' => $contact,
    'tfax' => $fax,
    'temail' => $email,
    'tsiege' => $siege,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création devise',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
     ///////////// Audit système ///////////////////
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                }else{
                ///////////// Audit système ///////////////////
 $tnew = array(
    'tcode' => $code,
    'tlibelle' => $libelle,
    'tcomplement' => $complement,
    'tadresse' => $adresse,
    'tcontact' => $contact,
    'tfax' => $fax,
    'temail' => $email,
    'tsiege' => $siege,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création devise',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);
     ///////////// Audit système ///////////////////
                    $infoHttp = [
                        "reponse" => "error",
                        "message" =>"Impossible",
                    ];
                }
            }else{
                  
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "veuillez vérifier votre connexion",
                ];
            }
           
            break;
        case 'R':
            
                try{
                    $sql = "SELECT `ID_SOCIETE` AS id,`CODE_SOCIETE`,`LIBELLE_SOCIETE`,`COMPLEMENT_SOCIETE`,`ADRESSE_SOCIETE`,`TEL_SOCIETE`,`FAX_SOCIETE`,`EMAIL_SOCIETE`,`SIEGE` FROM `societe` ";
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
           
           
            
            break;
        case 'U': // MAJ
            
            if(
                isset($obj->id) && !empty($obj->id)
                    
                    ){
                $id = secure($obj->id);
                if(isset($obj->code) )
                $code = secure($obj->code);
                else $code ="";
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
               
                if(isset($obj->complement) )
                $complement = secure($obj->complement);
                else $complement ="";
               
                if(isset($obj->adresse) )
                $adresse = secure($obj->adresse);
                else $adresse ="";
               
                if(isset($obj->contact) )
                $contact = secure($obj->contact);
                else $contact ="";
               
                if(isset($obj->fax) )
                $fax = secure($obj->fax);
                else $fax ="";
               
                if(isset($obj->email) )
                $email = secure($obj->email);
                else $email ="";
               
                if(isset($obj->siege) )
                $siege = secure($obj->siege);
                else $siege ="";

                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'tlibelle' => $libelle,
                    'tcomplement' => $complement,
                    'tadresse' => $adresse,
                    'tcontact' => $contact,
                    'tfax' => $fax,
                    'temail' => $email,
                    'tsiege' => $siege,
                    'tid' => $id,
                );
                
                $req1 = $DB->prepare("UPDATE `societe` SET `CODE_SOCIETE` = :tcode, `LIBELLE_SOCIETE` = :tlibelle, `COMPLEMENT_SOCIETE` = :tcomplement, `ADRESSE_SOCIETE` = :tadresse, `TEL_SOCIETE` = :tcontact, `FAX_SOCIETE` = :tfax, `EMAIL_SOCIETE` = :temail, `SIEGE` = :tsiege WHERE `societe`.`ID_SOCIETE` = :tid;
                ");
                if($req1->execute($t1)){
                  
                        ///////////// Audit système ///////////////////
 $tnew = array(
    'tcode' => $code,
    'tlibelle' => $libelle,
    'tcomplement' => $complement,
    'tadresse' => $adresse,
    'tcontact' => $contact,
    'tfax' => $fax,
    'temail' => $email,
    'tsiege' => $siege,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ societe',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
                     
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                }else{
                    ///////////// Audit système ///////////////////
 $tnew = array(
    'tcode' => $code,
    'tlibelle' => $libelle,
    'tcomplement' => $complement,
    'tadresse' => $adresse,
    'tcontact' => $contact,
    'tfax' => $fax,
    'temail' => $email,
    'tsiege' => $siege,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ societe',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres incorrects_",
                ];
            }
           
            break;
        case 'D':// Suppression 
           
            if(isset($_GET['id'] ) && !empty($_GET['id'])){
                $id = secure($_GET['id']);
                  
                 $t1 = array(
                    
                    'tid' => $id,
                );
                $req1 = $DB->prepare("DELETE FROM societe WHERE ID_SOCIETE = :tid");
                if($req1->execute($t1)){
                   
                      ///////////// Audit système ///////////////////
 $tnew = array(
    'tcode' => "",
    'tlibelle' => "",
    'tcomplement' => "",
    'tadresse' => "",
    'tcontact' => "",
    'tfax' => "",
    'temail' => "",
    'tsiege' => "",
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppression societe',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
                  
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Supprimé avec succès",
                    ];
                    
                }else{
     ///////////// Audit système ///////////////////
 $tnew = array(
  
    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppresion societe',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);                
      $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres incorrects",
                ];
            }
           
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
