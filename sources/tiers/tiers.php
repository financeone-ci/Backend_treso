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

    
    if(isset($obj->tel) )
    $tel = secure($obj->tel);
    else $tel ="";
    if(isset($obj->adresse) )
    $adresse = secure($obj->adresse);
    else $adresse ="";
    if(isset($obj->beneficiaire) )
    $beneficiaire = secure($obj->beneficiaire);
    else $beneficiaire ="";
    if(isset($obj->ref) )
    $ref = secure($obj->ref);
    else $ref ="";
     $mpdef ="";
    if(isset($obj->fournisseur) )
    $fournisseur = secure($obj->fournisseur);
    else $fournisseur ="";
    if(isset($obj->civilite) )
    $civilite = secure($obj->civilite);
    else $civilite ="";
    if(isset($obj->nom) )
    $nom = secure($obj->nom);
    else $nom ="";
    if(isset($obj->fonction) )
    $fonction = secure($obj->fonction);
    else $fonction ="";



    switch ($_GET['type']){
        case 'C': // creation
                 
            if(
                isset($obj->code) && !empty($obj->code)
                    
                    ){
                 
              
                        $code = secure($obj->code);
                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'ttel' => $tel,
                    'tadresse' => $adresse,
                    'tbeneficiaire' => $beneficiaire,
                    'tref' => $ref,
                    'tmpdef' => $mpdef,
                    'tfournisseur' => $fournisseur,
                    'tcivilite' => $civilite,
                    'tnom' => $nom,
                    'tfonction' => $fonction,
                );
               
                $req1 = $DB->prepare("INSERT INTO `tiers` (`CODE_TIERS`, `TEL_TIERS`, `ADRESSE_TIERS`, `BENEFICIAIRE_TIERS`, `REF_TIERS`, `MP_DEFAUT_TIERS`, `FOURNISSEUR_TIERS`, `CIV_REPRESENTANT_TIERS`, `NOM_REPRESENTANT_TIERS`, `FONCTION_REPRESENTANT_TIERS`) VALUES ( :tcode, :ttel, :tadresse, :tbeneficiaire, :tref, :tmpdef, :tfournisseur, :tcivilite, :tnom, :tfonction );
                ");
                if($req1->execute($t1)){
    // cas où la requête s'est bien exécutée
    $idTiers= $DB->lastInsertId();
    foreach ($obj->type_tiers as $item) {
        // 
        $t2 = array(
            'ttype' => $item->id,
            'tid' => $idTiers,
        );
        
$req2 = $DB->prepare("INSERT INTO `tiers_type_tiers` ( `ID_TYPE_TIERS`, `ID_TIERS`) VALUES ( :ttype, :tid);
        ");
        $req2->execute($t2);
    }
                        
    ///////////// Audit système ///////////////////
    $tnew = array(
        'tcode' => $code,
        'ttel' => $tel,
        'tadresse' => $adresse,
        'tbeneficiaire' => $beneficiaire,
        'tref' => $ref,
        'tmpdef' => $mpdef,
        'tfournisseur' => $fournisseur,
        'tcivilite' => $civilite,
        'tnom' => $nom,
        'tfonction' => $fonction,
    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création  tiers',
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
         
        'ttel' => $tel,
        'tadresse' => $adresse,
        'tbeneficiaire' => $beneficiaire,
        'tref' => $ref,
        'tmpdef' => $mpdef,
        'tfournisseur' => $fournisseur,
        'tcivilite' => $civilite,
        'tnom' => $nom,
        'tfonction' => $fonction,
    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création  tiers',
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
                    $sql = "SELECT 
                    `ID_TIERS` AS id, `CODE_TIERS`,`CODE_TIERS`,`ADRESSE_TIERS`,`BENEFICIAIRE_TIERS`,`REF_TIERS`,`MP_DEFAUT_TIERS`,`FOURNISSEUR_TIERS`, `TEL_TIERS`, `CIV_REPRESENTANT_TIERS`, `NOM_REPRESENTANT_TIERS`, `FONCTION_REPRESENTANT_TIERS`
                    FROM `tiers`";
                    $req = $DB->query($sql);
                    $row = $req->fetchAll(PDO::FETCH_OBJ);
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row,
                    ];
        foreach ($infoHttp['infos'] as $key=>$item) {
            # code...
          
            
            $sql_ = "SELECT type_tiers.`ID_TYPE_TIERS` AS id, CODE_TYPE_TIERS, `LIBELLE_TYPE_TIERS`  FROM `tiers_type_tiers` JOIN type_tiers ON type_tiers.ID_TYPE_TIERS = tiers_type_tiers.ID_TYPE_TIERS WHERE ID_TIERS = ".secure($item->id);
                    $req_ = $DB->query($sql_);
                    $row_ = $req_->fetchAll(PDO::FETCH_OBJ);
                   $infoHttp['infos'][$key]->type = $row_;
                   
        }
                    
                     
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

                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'ttel' => $tel,
                    'tadresse' => $adresse,
                    'tbeneficiaire' => $beneficiaire,
                    'tref' => $ref,
                    'tmpdef' => $mpdef,
                    'tfournisseur' => $fournisseur,
                    'tcivilite' => $civilite,
                    'tnom' => $nom,
                    'tfonction' => $fonction,
                    'tid' => $id,
                );
                
                $req1 = $DB->prepare("UPDATE `tiers` SET `CODE_TIERS` = :tcode, `TEL_TIERS` = :ttel, `ADRESSE_TIERS` = :tadresse, `BENEFICIAIRE_TIERS` = :tbeneficiaire, `REF_TIERS` = :tref, `MP_DEFAUT_TIERS` = :tmpdef, `FOURNISSEUR_TIERS` = :tfournisseur, `CIV_REPRESENTANT_TIERS` = :tcivilite, `NOM_REPRESENTANT_TIERS` = :tnom, `FONCTION_REPRESENTANT_TIERS` = :tfonction WHERE `tiers`.`ID_TIERS` = :tid;
                ");
                if($req1->execute($t1)){
                  
                        ///////////// Audit système ///////////////////
                        $DB->exec("DELETE FROM tiers_type_tiers WHERE ID_TIERS = $id");
                
                        foreach ($obj->type_tiers as $item) {
                            // 
                            $t2 = array(
                                'ttype' => $item->id,
                                'tid' => $id,
                            );
                            
$req2 = $DB->prepare("INSERT INTO `tiers_type_tiers` ( `ID_TYPE_TIERS`, `ID_TIERS`) VALUES ( :ttype, :tid);
                            ");
                            $req2->execute($t2);
                        }
 $tnew = array(
    'tcode' => $code,
    'ttel' => $tel,
    'tadresse' => $adresse,
    'tbeneficiaire' => $beneficiaire,
    'tref' => $ref,
    'tmpdef' => $mpdef,
    'tfournisseur' => $fournisseur,
    'tcivilite' => $civilite,
    'tnom' => $nom,
    'tfonction' => $fonction,
    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ type tiers',
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
 $tnew =array(
    'tcode' => $code,
    'ttel' => $tel,
    'tadresse' => $adresse,
    'tbeneficiaire' => $beneficiaire,
    'tref' => $ref,
    'tmpdef' => $mpdef,
    'tfournisseur' => $fournisseur,
    'tcivilite' => $civilite,
    'tnom' => $nom,
    'tfonction' => $fonction,
    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ type tiers',
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
                  $DB->exec("DELETE FROM tiers_type_tiers WHERE ID_TIERS = $id");
                 $t1 = array(
                    
                    'tid' => $id,
                );
                
                $req1 = $DB->prepare("DELETE FROM tiers WHERE ID_TIERS = :tid");
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
            'taction' => 'Suppression    tiers',
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
            'taction' => 'Suppresion   tiers',
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