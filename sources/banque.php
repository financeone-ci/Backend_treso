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
           
            if(isset($obj->code) && !empty($obj->code))
            {
                $code = secure($obj->code);
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
               
                if(isset($obj->directeur) )
                $directeur = secure($obj->directeur);
                else $directeur ="";
               
                if(isset($obj->adresse) )
                $adresse = secure($obj->adresse);
                else $adresse ="";
               
                if(isset($obj->contact) )
                $contact = secure($obj->contact);
                else $contact ="";
               
                if(isset($obj->gestionnaire) )
                $gestionnaire = secure($obj->gestionnaire);
                else $gestionnaire ="";
               
                if(isset($obj->adresse_web) )
                $adresse_web = secure($obj->adresse_web);
                else $adresse_web ="";

                try{
                // table d'ajout banque
                $t1 = array(
                    'tcode' => $code,
                    'tlibelle' => $libelle,
                    'tdirecteur' => $directeur,
                    'tadresse' => $adresse,
                    'tcontact' => $contact,
                    'tgestionnaire' => $gestionnaire,
                    'tadresse_web' => $adresse_web,
                );
                // Enregistrement de la banque
                $req1 = $DB->prepare("INSERT INTO `banque` ( `CODE_BANQUE`, `LIBELLE_BANQUE`, `DG`, `GESTIONNAIRE`, `ADRESSE_BANQUE`, `ADRESSE_WEB_BANQUE`, `CONTACT_BANQUE`) VALUES ( :tcode, :tlibelle, :tdirecteur, :tgestionnaire, :tadresse, :tadresse_web, :tcontact)");

                    $req1->execute($t1);
                    $idBanque = $DB->lastInsertId();

                    // Enregistrement des mesures du chèque
                    $t2 = array(
                        'tidBanque'=>$idBanque,
                    );
                    $req2 = $DB->prepare("INSERT INTO dimcheque (IDBANQUE) values (:tidBanque)");
                    if($req2->execute($t2)){
                        // cas où la requête s'est bien exécutée
                        ///////////// Audit système ///////////////////
                        $tnew = array(
                            'tcode' => $code,
                            'tlibelle' => $libelle,
                            'tdirecteur' => $directeur,
                            'tadresse' => $adresse,
                            'tcontact' => $contact,
                            'tgestionnaire' => $gestionnaire,
                            'tadresse_web' => $adresse_web,
                        );
                        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                            'tid' => "",
                            'tnom' => "",
                            'tip' => $ip,
                            'tmachine' => $machine,
                            'taction' => 'Création',
                            'tdescription' => "Création banque",
                            'tissue' => "1",
                        );
                        audit_sys($t2, $tnew,$jeton);
                        ///////////// Audit système ///////////////////
                        $infoHttp = [
                            "reponse" => "success",
                            "message" => "Enregistré avec succès",
                        ];
                    }
                }catch (PDOException $e){
                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => $code,
                        'tlibelle' => $libelle,
                        'tdirecteur' => $directeur,
                        'tadresse' => $adresse,
                        'tcontact' => $contact,
                        'tgestionnaire' => $gestionnaire,
                        'tadresse_web' => $adresse_web,

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

                    $MYSQL_DUPLICATE_CODES=array(1062, 23000);

                    if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES)) {
                        // duplicate entry, do something else
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Code banque déjà utilisé, veuillez en saisir un autre.",
                        ];
                    } else {
                        // an error other than duplicate entry occurred
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Enregistrement impossible, veuillez vérifier votre connexion.",
                        ];
                    }
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
                    $sql = "SELECT `IDBANQUE` AS id, `CODE_BANQUE` , `LIBELLE_BANQUE`, `DG`, `GESTIONNAIRE`, `ADRESSE_BANQUE`, `ADRESSE_WEB_BANQUE`, `CONTACT_BANQUE` FROM `banque` ";
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
                $code = secure($obj->code);
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
               
                if(isset($obj->directeur) )
                $directeur = secure($obj->directeur);
                else $directeur ="";
               
                if(isset($obj->adresse) )
                $adresse = secure($obj->adresse);
                else $adresse ="";
               
                if(isset($obj->contact) )
                $contact = secure($obj->contact);
                else $contact ="";
               
                if(isset($obj->gestionnaire) )
                $gestionnaire = secure($obj->gestionnaire);
                else $gestionnaire ="";
               
                if(isset($obj->adresse_web) )
                $adresse_web = secure($obj->adresse_web);
                else $adresse_web ="";
               

                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'tlibelle' => $libelle,
                    'tdirecteur' => $directeur,
                    'tadresse' => $adresse,
                    'tcontact' => $contact,
                    'tgestionnaire' => $gestionnaire,
                    'tadresse_web' => $adresse_web,
                    'tid' => $id,
                   
                );
                
                $req1 = $DB->prepare("UPDATE `banque` SET `CODE_BANQUE` = :tcode, `LIBELLE_BANQUE` = :tlibelle, `DG` = :tdirecteur, `GESTIONNAIRE` = :tgestionnaire, `ADRESSE_BANQUE` = :tadresse, `ADRESSE_WEB_BANQUE` = :tadresse_web, `CONTACT_BANQUE` = :tcontact WHERE `banque`.`IDBANQUE` = :tid;
                ");
                if($req1->execute($t1)){
                  
                        ///////////// Audit système ///////////////////
                        $tnew = array(
                            'tcode' => $code,
                            'tlibelle' => $libelle,
                            'tdirecteur' => $directeur,
                            'tadresse' => $adresse,
                            'tcontact' => $contact,
                            'tgestionnaire' => $gestionnaire,
                            'tadresse_web' => $adresse_web,
                           
                        );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ banque',
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
                        'tdirecteur' => $directeur,
                        'tadresse' => $adresse,
                        'tcontact' => $contact,
                        'tgestionnaire' => $gestionnaire,
                        'tadresse_web' => $adresse_web,
                       
                    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ banque',
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
                try{
                    $id = secure($_GET['id']);
                    $t1 = array(
                        'tid' => $id,
                    );
                    $req = $DB->prepare("DELETE FROM dimcheque WHERE IDBANQUE = :tid");
                    $req->execute($t1);
                    $req1 = $DB->prepare("DELETE FROM banque WHERE IDBANQUE = :tid");
                    $req1->execute($t1);

                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => "",
                        'tlibelle' => "",
                        'tdirecteur' => "",
                        'tadresse' => "",
                        'tcontact' => "",
                        'tgestionnaire' => "",
                        'tadresse_web' => "",

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

                }catch (PDOException $e)
                {
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

                    $MYSQL_DUPLICATE_CODES=array(1062, 23000);
                    if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES))
                    {
                        // duplicate entry, do something else
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Liaisons détectées : Impossible de supprimer cet élément.",
                        ];
                    } else {
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                        ];
                    }
                }
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

