<?php
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
if(isset($_GET['jeton'])){
                    $jeton = secure($_GET['jeton']);
                 }else{
                    $jeton = "";
                 }
if(isset($_GET['type']) && !empty($_GET['type'])){
    // variables d'initialisation

    
    if(isset($obj->iban) )
    $iban = secure($obj->iban);
    else $iban ="";
    if(isset($obj->swift) )
    $swift = secure($obj->swift);
    else $swift ="";
    if(isset($obj->banque) )
    $banque = secure($obj->banque);
    else $banque ="";
    if(isset($obj->adresse) )
    $adresse = secure($obj->adresse);
    else $adresse ="";
    if(isset($obj->tiers) ){
        $codetiers = secure($obj->tiers);
        $sql = "SELECT `ID_TIERS` FROM `tiers` WHERE `CODE_TIERS` = '$codetiers'";
        $req = $DB->query($sql);
        $row = $req->fetch();
        $tiers = $row['ID_TIERS'];
    }
    
    else $tiers ="";
    if(isset($obj->libelle) )
    $libelle = secure($obj->libelle);
    else $libelle ="";
    



    switch ($_GET['type']){
        case 'C': // creation
                 
            if(
                isset($obj->rib) && !empty($obj->rib)
                    ){
                        $rib = secure($obj->rib);
                // insérer dans la BDD
                $t1 = array(
                    'trib' => $rib,
                    'tiban' => $iban,
                    'tswift' => $swift,
                    'tbanque' => $banque,
                    'tadresse' => $adresse,
                    'ttiers' => $tiers,
                    'tlibelle' => $libelle,
                );
               
                $req1 = $DB->prepare("INSERT INTO `compte_tiers` 
 (`RIB_COMPTE_TIERS`, `IBAN_COMPTE_TIERS`, `SWIFT_COMPTE_TIERS`, `BANQ_ID`, `ADRESSE_BANQUE`, `ID_TIERS`, `LIBELLE__COMPTE_TIERS`) 
                VALUES 
     (:trib, :tiban, :tswift, :tbanque, :tadresse, :ttiers, :tlibelle);                ");
                try { $req1->execute($t1) ;
    // cas où la requête s'est bien exécutée
                        
    ///////////// Audit système ///////////////////
    $tnew = array(
                    'trib' => $rib,
                    'tiban' => $iban,
                    'tswift' => $swift,
                    'tbanque' => $banque,
                    'tadresse' => $adresse,
                    'ttiers' => $tiers,
                    'tlibelle' => $libelle,
                );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création cpt tiers',
            'tdescription' => "",
            'tissue' => "1",
                      );
    audit_sys($t2, $tnew,$jeton);
     ///////////// Audit système ///////////////////
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                }catch (PDOException $e){
                ///////////// Audit système ///////////////////
    $tnew = array(
                    'trib' => $rib,
                    'tiban' => $iban,
                    'tswift' => $swift,
                    'tbanque' => $banque,
                    'tadresse' => $adresse,
                    'ttiers' => $tiers,
                    'tlibelle' => $libelle,
                );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création cpt  tiers',
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
             "message" => "RIB déjà utilisé, veuillez en saisir un autre.",
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
            if(isset($_GET['codetiers'])){
                $code=secure($_GET['codetiers']);
            try{
                                $sql = "SELECT `ID_COMPTE_TIERS` AS id, `RIB_COMPTE_TIERS`, `IBAN_COMPTE_TIERS`, `SWIFT_COMPTE_TIERS`, `BANQ_ID`, banque.`ADRESSE_BANQUE`, tiers.`ID_TIERS`, `LIBELLE__COMPTE_TIERS`, CODE_BANQUE, CODE_TIERS FROM `compte_tiers` JOIN banque ON banque.IDBANQUE = compte_tiers.BANQ_ID JOIN tiers ON tiers.ID_TIERS = compte_tiers.ID_TIERS WHERE tiers.`CODE_TIERS` = '$code'";
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
                $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion*",
                    ];
            }
                
           
           
            
            break;
        case 'U': // MAJ
             
            if(
                isset($obj->id) && !empty($obj->id)
               
                    ){
                $id = secure($obj->id);
                $rib = secure($obj->rib);

                // modifier dans la BDD
                $t1 = array(
                    'trib' => $rib,
                    'tiban' => $iban,
                    'tswift' => $swift,
                    'tbanque' => $banque,
                    'tadresse' => $adresse,
                    'ttiers' => $tiers,
                    'tlibelle' => $libelle,
                    'tid' => $id,
                );
                
                $req1 = $DB->prepare("UPDATE `compte_tiers` SET `RIB_COMPTE_TIERS`= :trib, `IBAN_COMPTE_TIERS`= :tiban, `SWIFT_COMPTE_TIERS`= :tswift, `BANQ_ID`= :tbanque, `ADRESSE_BANQUE`= :tadresse, `ID_TIERS`= :ttiers, `LIBELLE__COMPTE_TIERS`= :tlibelle WHERE `compte_tiers`.`ID_COMPTE_TIERS` = :tid;
                ");
                try { $req1->execute($t1);
                  
                        ///////////// Audit système ///////////////////
                      
 $tnew = array(
                    'trib' => $rib,
                    'tiban' => $iban,
                    'tswift' => $swift,
                    'tbanque' => $banque,
                    'tadresse' => $adresse,
                    'ttiers' => $tiers,
                    'tlibelle' => $libelle,
                );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ  cpt tiers',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
                     
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                }catch (PDOException $e){
                    ///////////// Audit système ///////////////////
 $tnew = array(
                    'trib' => $rib,
                    'tiban' => $iban,
                    'tswift' => $swift,
                    'tbanque' => $banque,
                    'tadresse' => $adresse,
                    'ttiers' => $tiers,
                    'tlibelle' => $libelle,
                );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ  cpt tiers',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);
  $MYSQL_DUPLICATE_CODES=array(1062, 23000);

                    if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES)) {
                        // duplicate entry, do something else
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "RIB déjà utilisé, veuillez en saisir un autre.",
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
                $req1 = $DB->prepare("DELETE FROM compte_tiers WHERE ID_COMPTE_TIERS = :tid");
                if($req1->execute($t1)){
                   
                      ///////////// Audit système ///////////////////
 $tnew = array(

    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppression  cpt  tiers',
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
            'taction' => 'Suppresion cpt  tiers',
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
