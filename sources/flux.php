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
                if(isset($obj->description) )
                $description = secure($obj->description);
                else $description ="";
               
                if(isset($obj->sens) ){
                     if($obj->sens =="Credit" )
                     $sens = "C"; elseif($obj->sens =="Debit" )
                     $sens = "D"; else $sens = "";
                }
               
                else $sens ="";
               
                if(isset($obj->incr_solde_flux) )
                $incr_solde = secure($obj->incr_solde_flux);
                else $incr_solde ="";
               
                if(isset($obj->incr_quota_flux) )
                $incr_quota = secure($obj->incr_quota_flux);
                else $incr_quota ="";
               
                if(isset($obj->categorie) )
                $categorie = secure($obj->categorie);
                else $categorie ="";

                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'tdescription' =>$description,
                    'tsens' => $sens,
                    'tincr_solde' => $incr_solde,
                    'tincr_quota' => $incr_quota,
                    'tcategorie' => $categorie,
                );
               
                $req1 = $DB->prepare("INSERT INTO `flux` (`CODE_FLUX`, `DESCRIPTION_FLUX`, `SENS_FLUX`, `INCR_SOLDE_FLUX`, `INCR_QUOTA_FLUX`, `ID_CATEGORIE_FLUX`) VALUES ( :tcode, :tdescription, :tsens, :tincr_solde, :tincr_quota, :tcategorie);");
                if($req1->execute($t1)){
// cas o?? la requ??te s'est bien ex??cut??e

                        
 ///////////// Audit syst??me ///////////////////
 $tnew =  array(
    'tcode' => $code,
    'tdescription' =>$description,
    'tsens' => $sens,
    'tincr_solde' => $incr_solde,
    'tincr_quota' => $incr_quota,
    'tcategorie' => $categorie,
    
);
        $t2 = array( // tableau des param??tres ?? enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Cr??ation flux',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
     ///////////// Audit syst??me ///////////////////
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistr?? avec succ??s",
                    ];
                }else{
                ///////////// Audit syst??me ///////////////////
                $tnew =  array(
                    'tcode' => $code,
                    'tdescription' =>$description,
                    'tsens' => $sens,
                    'tincr_solde' => $incr_solde,
                    'tincr_quota' => $incr_quota,
                    'tcategorie' => $categorie,
                );
       
        $t2 = array( // tableau des param??tres ?? enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Cr??ation flux',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);
     ///////////// Audit syst??me ///////////////////
                    $infoHttp = [
                        "reponse" => "error",
                        "message" =>"Impossible",
                    ];
                }
            }else{
                  
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "veuillez v??rifier votre connexion",
                ];
            }
           
            break;
        case 'R':
            
                try{
                    $sql = "SELECT `ID_FLUX` AS id, `CODE_FLUX`, `DESCRIPTION_FLUX`, `SENS_FLUX`, `INCR_SOLDE_FLUX`, `INCR_QUOTA_FLUX`, flux.`ID_CATEGORIE_FLUX` AS ID_CATEGORIE_FLUX, CODE_CATEGORIE_FLUX FROM `flux` JOIN categorie_flux ON flux.ID_CATEGORIE_FLUX = categorie_flux.ID_CATEGORIE_FLUX";
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
                        "message" => "Connexion aux donn??es impossible, veuillez v??rifier votre connexion.",
                    ];
                }
           
           
            
            break;
        case 'U': // MAJ
            
            if(
                isset($obj->id) && !empty($obj->id)
                    
                    ){
                $id = secure($obj->id);
                $code = secure($obj->code);
                if(isset($obj->description) )
                $description = secure($obj->description);
                else $description ="";
               
                if(isset($obj->sens) ){
                     if($obj->sens =="Credit" or $obj->sens =="C"  ){
                         $sens = "C";
                     }
                      elseif($obj->sens =="Debit" or $obj->sens =="D"  ){
                          $sens = "D"; 
                      } else  $sens ="";
                     
                }
               
                else $sens ="";
               
                if(isset($obj->incr_solde_flux) )
                $incr_solde = secure($obj->incr_solde_flux);
                else $incr_solde ="";
               
                if(isset($obj->incr_quota_flux) )
                $incr_quota = secure($obj->incr_quota_flux);
                else $incr_quota ="";
               
                if(isset($obj->categorie) )
                $categorie = secure($obj->categorie);
                else $categorie ="";
               
               

                // modifier dans la BDD
                $t1 =  array(
                    'tid' => $id,
                    'tcode' => $code,
                    'tdescription' =>$description,
                    'tsens' => $sens,
                    'tincr_solde' => $incr_solde,
                    'tincr_quota' => $incr_quota,
                    'tcategorie' => $categorie,
                    
                );
                
                $req1 = $DB->prepare("UPDATE `flux` SET `CODE_FLUX` = :tcode, `DESCRIPTION_FLUX` = :tdescription, `SENS_FLUX` = :tsens, `INCR_SOLDE_FLUX` = :tincr_solde, `INCR_QUOTA_FLUX` = :tincr_quota, `ID_CATEGORIE_FLUX` = :tcategorie WHERE `flux`.`ID_FLUX` = :tid;
                ");
                if($req1->execute($t1)){
                  
                        ///////////// Audit syst??me ///////////////////
                        $tnew =  array(
                            'tcode' => $code,
                            'tdescription' =>$description,
                            'tsens' => $sens,
                            'tincr_solde' => $incr_solde,
                            'tincr_quota' => $incr_quota,
                            'tcategorie' => $categorie,
                            'tid' => $id,
                            
                        );
       
        $t2 = array( // tableau des param??tres ?? enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ flux',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
                     
                        // cas o?? la requ??te s'est bien ex??cut??e
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistr?? avec succ??s",
                    ];
                   
                }else{
                    ///////////// Audit syst??me ///////////////////
                    $tnew =  array(
                        'tcode' => $code,
                        'tdescription' =>$description,
                        'tsens' => $sens,
                        'tincr_solde' => $incr_solde,
                        'tincr_quota' => $incr_quota,
                        'tcategorie' => $categorie,
                        
                    );
       
        $t2 = array( // tableau des param??tres ?? enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ flux',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux donn??es impossible, veuillez v??rifier votre connexion.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Param??tres incorrects_",
                ];
            }
           
            break;
        case 'D':// Suppression 
           
            if(isset($_GET['id'] ) && !empty($_GET['id'])){
                $id = secure($_GET['id']);
                  
                 $t1 = array(
                    
                    'tid' => $id,
                );
                $req1 = $DB->prepare("DELETE FROM flux WHERE ID_FLUX = :tid");
                if($req1->execute($t1)){
                   
                      ///////////// Audit syst??me ///////////////////
 $tnew = array(
    'tcode' => "",
    'tdescription' =>"",
    'tsens' => "",
    'tincr_solde' => "",
    'tincr_quota' => "",
    'tcategorie' => "",
    
);
       
        $t2 = array( // tableau des param??tres ?? enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppression flux',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
                  
                        // cas o?? la requ??te s'est bien ex??cut??e
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Supprim?? avec succ??s",
                    ];
                    
                }else{
     ///////////// Audit syst??me ///////////////////
 $tnew = array(
  
    'tid' => $id,
);
       
        $t2 = array( // tableau des param??tres ?? enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppression flux',
            'tdescription' => "",
            'tissue' => "0",
                      );
  audit_sys($t2, $tnew,$jeton);                
      $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux donn??es impossible, veuillez v??rifier votre connexion.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Param??tres incorrects",
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


