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
                if(isset($obj->solde_i) )
                $solde_i = secure($obj->solde_i);
                else $solde_i ="";
               
                if(isset($obj->comptable) )
                $comptable = secure($obj->comptable);
                else $comptable ="";
               
                if(isset($obj->rib) )
                $rib = secure($obj->rib);
                else $rib ="";
               
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
               
                if(isset($obj->gestionnaire) )
                $gestionnaire = secure($obj->gestionnaire);
                else $gestionnaire ="";
               
                if(isset($obj->civilite) )
                $civilite = secure($obj->civilite);
                else $civilite ="";
               
                if(isset($obj->service) )
                $service = secure($obj->service);
                else $service ="";
               
                if(isset($obj->tel) )
                $tel = secure($obj->tel);
                else $tel ="";
               
                if(isset($obj->email) )
                $email = secure($obj->email);
                else $email ="";
               
                if(isset($obj->banque) )
                $banque = secure($obj->banque);
                else $banque ="";
               
                if(isset($obj->fichier) )
                $fichier = secure($obj->fichier);
                else $fichier ="";
               
                if(isset($obj->societe) )
                $societe = secure($obj->societe);
                else $societe ="";
               
                if(isset($obj->devise) )
                $devise = secure($obj->devise);
                else $devise ="";
               
                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'tsolde_i' => $solde_i,
                    'tcomptable' => $comptable,
                    'trib' => $rib,
                    'tlibelle' => $libelle,
                    'tgestionnaire' => $gestionnaire,
                    'tcivilite' => $civilite,
                    'tservice' => $service,
                    'ttel' => $tel,
                    'temail' => $email,
                    'tbanque' => $banque,
                    'tfichier' => $fichier,
                    'tsociete' => $societe,
                    'tdevise' => $devise,
                   
                );
               
                $req1 = $DB->prepare("INSERT INTO `compte` ( `CODE_COMPTE`, `SOLDE_INITIAL_COMPTE`, `COMPTE_COMPTABLE`, `RIB`, `LIBELLE_COMPTE`, `GESTIONNAIRE_COMPTE`, `CIV_GESTIONNAIRE_COMPTE`, `SERVICE_GESTIONNAIRE_COMPTE`, `TEL_GESTIONNAIRE_COMPTE`, `EMAIL_GESTIONNAIRE_COMPTE`, `IDBANQUE`, `COMPTE_FICHIER`, `ID_SOCIETE`, `ID_DEVISE`) VALUES ( :tcode, :tsolde_i, :tcomptable, :trib, :tlibelle, :tgestionnaire, :tcivilite, :tservice, :ttel, :temail, :tbanque, :tfichier, :tsociete, :tdevise);");
               
               try {
                   $req1->execute($t1);

     // cas où la requête s'est bien exécutée

                        
 ///////////// Audit système ///////////////////
 $tnew =  array(
    'tcode' => $code,
    'tsolde_i' => $solde_i,
    'tcomptable' => $comptable,
    'trib' => $rib,
    'tlibelle' => $libelle,
    'tgestionnaire' => $gestionnaire,
    'tcivilite' => $civilite,
    'tservice' => $service,
    'ttel' => $tel,
    'temail' => $email,
    'tbanque' => $banque,
    'tfichier' => $fichier,
    'tsociete' => $societe,
    'tdevise' => $devise,
   
);
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création compte',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
     ///////////// Audit système ///////////////////
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];          } 
                    catch (\Throwable $e) {
                       
                   # code...
        ///////////// Audit système ///////////////////
                $tnew =  array(
                    'tcode' => $code,
                    'tsolde_i' => $solde_i,
                    'tcomptable' => $comptable,
                    'trib' => $rib,
                    'tlibelle' => $libelle,
                    'tgestionnaire' => $gestionnaire,
                    'tcivilite' => $civilite,
                    'tservice' => $service,
                    'ttel' => $tel,
                    'temail' => $email,
                    'tbanque' => $banque,
                    'tfichier' => $fichier,
                    'tsociete' => $societe,
                    'tdevise' => $devise,
                   
                );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création compte',
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
             "message" => "Code ou RIB déjà utilisé, veuillez en saisir un autre.",
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
                    $sql = "SELECT `ID_COMPTE` AS id, `CODE_COMPTE`, `SOLDE_INITIAL_COMPTE`, `COMPTE_COMPTABLE`, `RIB`, `LIBELLE_COMPTE`, `GESTIONNAIRE_COMPTE`, `CIV_GESTIONNAIRE_COMPTE`, `SERVICE_GESTIONNAIRE_COMPTE`, `TEL_GESTIONNAIRE_COMPTE`, `EMAIL_GESTIONNAIRE_COMPTE`, `CODE_BANQUE` ,  banque.`IDBANQUE` AS ID_BANQUE , `COMPTE_FICHIER`, `ID_SOCIETE`,compte.ID_DEVISE AS ID_DEVISE, `CODE_DEVISE` FROM `compte` JOIN banque ON banque.IDBANQUE = compte.IDBANQUE JOIN devise ON compte.ID_DEVISE = devise.IDDEVISE";
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
                if(isset($obj->solde_i) )
                $solde_i = secure($obj->solde_i);
                else $solde_i ="";
               
                if(isset($obj->comptable) )
                $comptable = secure($obj->comptable);
                else $comptable ="";
               
                if(isset($obj->rib) )
                $rib = secure($obj->rib);
                else $rib ="";
               
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
               
                if(isset($obj->gestionnaire) )
                $gestionnaire = secure($obj->gestionnaire);
                else $gestionnaire ="";
               
                if(isset($obj->civilite) )
                $civilite = secure($obj->civilite);
                else $civilite ="";
               
                if(isset($obj->service) )
                $service = secure($obj->service);
                else $service ="";
               
                if(isset($obj->tel) )
                $tel = secure($obj->tel);
                else $tel ="";
               
                if(isset($obj->email) )
                $email = secure($obj->email);
                else $email ="";
               
                if(isset($obj->banque) )
                $banque = secure($obj->banque);
                else $banque ="";
               
                if(isset($obj->fichier) )
                $fichier = secure($obj->fichier);
                else $fichier ="";
               
                if(isset($obj->societe) )
                $societe = secure($obj->societe);
                else $societe ="";
               
                if(isset($obj->devise) )
                $devise = secure($obj->devise);
                else $devise ="";
               
               

                // modifier dans la BDD
                $t1 =  array(
                    'tcode' => $code,
                    'tsolde_i' => $solde_i,
                    'tcomptable' => $comptable,
                    'trib' => $rib,
                    'tlibelle' => $libelle,
                    'tgestionnaire' => $gestionnaire,
                    'tcivilite' => $civilite,
                    'tservice' => $service,
                    'ttel' => $tel,
                    'temail' => $email,
                    'tbanque' => $banque,
                    'tsociete' => $societe,
                    'tdevise' => $devise,
                    'tid' => $id,
                );
                
                $req1 = $DB->prepare("UPDATE `compte` SET `CODE_COMPTE` = :tcode, `SOLDE_INITIAL_COMPTE` = :tsolde_i, `COMPTE_COMPTABLE` = :tcomptable, `RIB` = :trib, `LIBELLE_COMPTE` = :tlibelle, `GESTIONNAIRE_COMPTE` = :tgestionnaire, `CIV_GESTIONNAIRE_COMPTE` = :tcivilite, `SERVICE_GESTIONNAIRE_COMPTE` = :tservice, `TEL_GESTIONNAIRE_COMPTE` = :ttel, `EMAIL_GESTIONNAIRE_COMPTE` = :temail, `ID_SOCIETE` = :tsociete, IDBANQUE = :tbanque, `ID_DEVISE` = :tdevise WHERE `compte`.`ID_COMPTE` = :tid;
                ");
                try {
                    $req1->execute($t1);
                  
                        ///////////// Audit système ///////////////////
                        $tnew =  array(
                            'tcode' => $code,
                            'tsolde_i' => $solde_i,
                            'tcomptable' => $comptable,
                            'trib' => $rib,
                            'tlibelle' => $libelle,
                            'tgestionnaire' => $gestionnaire,
                            'tcivilite' => $civilite,
                            'tservice' => $service,
                            'ttel' => $tel,
                            'temail' => $email,
                            'tbanque' => $banque,
                            'tfichier' => $fichier,
                            'tsociete' => $societe,
                            'tdevise' => $devise,
                           
                        );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ compte bancaire',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
                     
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                } catch (\Throwable $e) {
                      ///////////// Audit système ///////////////////
                    $tnew =  array(
                        'tcode' => $code,
                        'tsolde_i' => $solde_i,
                        'tcomptable' => $comptable,
                        'trib' => $rib,
                        'tlibelle' => $libelle,
                        'tgestionnaire' => $gestionnaire,
                        'tcivilite' => $civilite,
                        'tservice' => $service,
                        'ttel' => $tel,
                        'temail' => $email,
                        'tbanque' => $banque,
                        'tfichier' => $fichier,
                        'tsociete' => $societe,
                        'tdevise' => $devise,
                       
                    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ compte bancaire',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);
                     
                    $MYSQL_DUPLICATE_CODES=array(1062, 23000);

                    if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES)) {
                        // duplicate entry, do something else
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Code ou RIB déjà utilisé, veuillez en saisir un autre.",
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
                $req1 = $DB->prepare("DELETE FROM compte WHERE ID_COMPTE = :tid");
                if($req1->execute($t1)){
                   
                      ///////////// Audit système ///////////////////
 $tnew =array(
    'tcode' => "",
    'tsolde_i' => "",
    'tcomptable' => "",
    'trib' => "",
    'tlibelle' => "",
    'tgestionnaire' => "",
    'tcivilite' => "",
    'tservice' => "",
    'ttel' => "",
    'temail' => "",
    'tbanque' => "",
    'tfichier' => "",
    'tsociete' => "",
    'tdevise' => "",
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppression compte',
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
            'taction' => 'Suppression compte',
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



