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
    switch ($_GET['type']){
        case 'C': // creation
                 
            if(
                isset($obj->code) && !empty($obj->code)
                    
                    ){
                 
                $code = secure($obj->code);
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
    
                // modifier dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'tlibelle' => $libelle,
                );
               
                $req1 = $DB->prepare("INSERT INTO `type_tiers` ( `CODE_TYPE_TIERS`, `LIBELLE_TYPE_TIERS`) VALUES ( :tcode, :tlibelle);
                ");
                if($req1->execute($t1)){
    // cas où la requête s'est bien exécutée
    
                        
    ///////////// Audit système ///////////////////
    $tnew = array(
        'tcode' => $code,
        'tlibelle' => $libelle,
    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création  type tiers',
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
    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création  type tiers',
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
`ID_TYPE_TIERS` AS id,
`CODE_TYPE_TIERS`, `LIBELLE_TYPE_TIERS`
FROM `type_tiers`";
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

                // modifier dans la BDD
                $t1 = array(
                    'tid' => $id,
                    'tcode' => $code,
                    'tlibelle' => $libelle,
                );
                
                $req1 = $DB->prepare("UPDATE `type_tiers` SET `LIBELLE_TYPE_TIERS` = :tlibelle , `CODE_TYPE_TIERS` =:tcode   WHERE `type_tiers`.`ID_TYPE_TIERS` = :tid;
                ");
                if($req1->execute($t1)){
                  
                        ///////////// Audit système ///////////////////
               
 $tnew = array(
    'tid' => $id,
    'tcode' => $code,
    'tlibelle' => $libelle,
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
    'tid' => $id,
    'tcode' => $code,
    'tlibelle' => $libelle,
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
                  
                 $t1 = array(
                    
                    'tid' => $id,
                );
                $req1 = $DB->prepare("DELETE FROM type_tiers WHERE ID_TYPE_TIERS = :tid");
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
            'taction' => 'Suppression   type tiers',
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
            'taction' => 'Suppresion  type tiers',
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
