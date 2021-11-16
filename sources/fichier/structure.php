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
/*

 $t = array(
                    'tcode' => secure($obj->code),
                    'tdescription' => secure($obj->description),
                    'tpos_taxe' => secure($obj->pos_taxe),
                    'tpos_benef' => secure($obj->pos_benef),
                    'tpos_ref_benef' => secure($obj->pos_ref_benef),
                    'tpos_montant' => secure($obj->pos_montant),
                    'tpos_num' => secure($obj->pos_num),
                    'tpos_motif' => secure($obj->pos_motif),
                    'tpos_echeance' => secure($obj->pos_echeance),
                    'tpos_budget' => secure($obj->pos_budget),
                    'tpos_num_bon' => secure($obj->pos_num_bon),
                    'tpos_date' => secure($obj->pos_date),
                    'tpos_marche' => secure($obj->pos_marche),
                    'tpos_retenue' => secure($obj->pos_retenue),
                );
*/
    
    if(isset($obj->description) )
    $description = secure($obj->description);
    else $description ="";
     
    if(isset($obj->pos_taxe) )
    $taxe = secure($obj->pos_taxe);
    else $taxe ="";
     
    if(isset($obj->pos_num) )
    $engagement_num = secure($obj->pos_num);
    else $engagement_num ="";
     
    if(isset($obj->pos_benef) )
    $beneficiaire = secure($obj->pos_benef);
    else $beneficiaire ="";
     
    if(isset($obj->pos_ref_benef) )
    $ref_beneficiaire = secure($obj->pos_ref_benef);
    else $ref_beneficiaire ="";
     
    if(isset($obj->pos_montant) )
    $engagement_montant = secure($obj->pos_montant);
    else $engagement_montant ="";
     
    if(isset($obj->pos_num_bon) )
    $bon_commande_num = secure($obj->pos_num_bon);
    else $bon_commande_num ="";
     
    if(isset($obj->pos_motif) )
    $engagement_motif = secure($obj->pos_motif);
    else $engagement_motif ="";
     
    if(isset($obj->pos_echeance) )
    $date_echeance = secure($obj->pos_echeance);
    else $date_echeance ="";
     
    if(isset($obj->pos_budget) )
    $code_budget = secure($obj->pos_budget);
    else $code_budget ="";
     
    if(isset($obj->pos_date) )
    $engagement_date = secure($obj->pos_date);
    else $engagement_date ="";
     
    if(isset($obj->pos_marche) )
    $ref_marche = secure($obj->pos_marche);
    else $ref_marche ="";
     
    if(isset($obj->pos_retenue) )
    $engagement_retenue = secure($obj->pos_retenue);
    else $engagement_retenue ="";
     
    



    switch ($_GET['type']){
        case 'C': // creation
                 
            if(
                isset($obj->code) && !empty($obj->code)
                    ){
                        $code = secure($obj->code);
                // insérer dans la BDD
                $t1 = array(
                    'tcode' => $code,
                    'tdescription' => $description,
                    'ttaxe' => $taxe,
                    'tengagement_num' => $engagement_num,
                    'tbeneficiaire' => $beneficiaire,
                    'tref_beneficiaire' => $ref_beneficiaire,
                    'tengagement_montant' => $engagement_montant,
                    'tbon_commande_num' => $bon_commande_num,
                    'tengagement_motif' => $engagement_motif,
                    'tdate_echeance' => $date_echeance,
                    'tcode_budget' => $code_budget,
                    'tengagement_date' => $engagement_date,
                    'tref_marche' => $ref_marche,
                    'tengagement_retenue' => $engagement_retenue,
                    
                );
               
                $req1 = $DB->prepare("INSERT INTO `structure_fichier` (`code_structure_fichier`, `desc_structure_fichier`, `pos_taxe_engagement`, `pos_num_engagement`, `pos_beneficiaire`, `pos_ref_beneficiaire`, `pos_montant_engagement`, `pos_num_bon_commande`, `pos_motif_engagement`, `pos_date_echeance`, `pos_code_budget`, `pos_date_engagement`, `pos_ref_marche`, `pos_retenue_engagement`) VALUES (:tcode, :tdescription,   :ttaxe,   :tengagement_num,   :tbeneficiaire,   :tref_beneficiaire,   :tengagement_montant,   :tbon_commande_num,   :tengagement_motif,   :tdate_echeance,   :tcode_budget,   :tengagement_date,   :tref_marche,   :tengagement_retenue  );");
                try { $req1->execute($t1) ;
    // cas où la requête s'est bien exécutée
                        
    ///////////// Audit système ///////////////////
    $tnew = array(
        'code' => $code,
        'tdescription' => $description,
        'ttaxe' => $taxe,
        'tengagement_num' => $engagement_num,
        'tbeneficiaire' => $beneficiaire,
        'tref_beneficiaire' => $ref_beneficiaire,
        'tengagement_montant' => $engagement_montant,
        'tbon_commande_num' => $bon_commande_num,
        'tengagement_motif' => $engagement_motif,
        'tdate_echeance' => $date_echeance,
        'tcode_budget' => $code_budget,
        'tengagement_date' => $engagement_date,
        'tref_marche' => $ref_marche,
        'tengagement_retenue' => $engagement_retenue,
        
    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création structure importation',
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
        'code' => $code,
        'tdescription' => $description,
        'ttaxe' => $taxe,
        'tengagement_num' => $engagement_num,
        'tbeneficiaire' => $beneficiaire,
        'tref_beneficiaire' => $ref_beneficiaire,
        'tengagement_montant' => $engagement_montant,
        'tbon_commande_num' => $bon_commande_num,
        'tengagement_motif' => $engagement_motif,
        'tdate_echeance' => $date_echeance,
        'tcode_budget' => $code_budget,
        'tengagement_date' => $engagement_date,
        'tref_marche' => $ref_marche,
        'tengagement_retenue' => $engagement_retenue,
        
    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création structure importation',
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
             "message" => "Cod déjà utilisé, veuillez en saisir un autre.",
         ];
     } else {
         // an error other than duplicate entry occurred
         $infoHttp = [
             "reponse" => "error",
             "message" => "Enregistrement impossible, veuillez vérifier votre connexion.".$e,
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
                                $sql = "SELECT 
                                `idstructure_fichier` AS id, `code_structure_fichier`, `desc_structure_fichier`, `pos_taxe_engagement`, `pos_num_engagement`, `pos_beneficiaire`, `pos_ref_beneficiaire`, `pos_montant_engagement`, `pos_num_bon_commande`, `pos_motif_engagement`, `pos_date_echeance`, `pos_code_budget`, `pos_date_engagement`, `pos_ref_marche`, `pos_retenue_engagement`
                                FROM `structure_fichier` ";
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

                // modifier dans la BDD
                $t1 = array(
                    'code' => $code,
                    'tdescription' => $description,
                    'ttaxe' => $taxe,
                    'tengagement_num' => $engagement_num,
                    'tbeneficiaire' => $beneficiaire,
                    'tref_beneficiaire' => $ref_beneficiaire,
                    'tengagement_montant' => $engagement_montant,
                    'tbon_commande_num' => $bon_commande_num,
                    'tengagement_motif' => $engagement_motif,
                    'tdate_echeance' => $date_echeance,
                    'tcode_budget' => $code_budget,
                    'tengagement_date' => $engagement_date,
                    'tref_marche' => $ref_marche,
                    'tengagement_retenue' => $engagement_retenue,
                    'tid' => $id,
                );
                
                $req1 = $DB->prepare("UPDATE `compte_tiers` SET `RIB_COMPTE_TIERS`= :trib, `IBAN_COMPTE_TIERS`= :tiban, `SWIFT_COMPTE_TIERS`= :tswift, `BANQ_ID`= :tbanque, `ADRESSE_BANQUE`= :tadresse, `ID_TIERS`= :ttiers, `LIBELLE__COMPTE_TIERS`= :tlibelle WHERE `compte_tiers`.`ID_COMPTE_TIERS` = :tid;
                ");
                try { $req1->execute($t1);
                  
                        ///////////// Audit système ///////////////////
                      
 $tnew = array(
    'code' => $code,
    'tdescription' => $description,
    'ttaxe' => $taxe,
    'tengagement_num' => $engagement_num,
    'tbeneficiaire' => $beneficiaire,
    'tref_beneficiaire' => $ref_beneficiaire,
    'tengagement_montant' => $engagement_montant,
    'tbon_commande_num' => $bon_commande_num,
    'tengagement_motif' => $engagement_motif,
    'tdate_echeance' => $date_echeance,
    'tcode_budget' => $code_budget,
    'tengagement_date' => $engagement_date,
    'tref_marche' => $ref_marche,
    'tengagement_retenue' => $engagement_retenue,
    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ  structure fichier',
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
    'code' => $code,
    'tdescription' => $description,
    'ttaxe' => $taxe,
    'tengagement_num' => $engagement_num,
    'tbeneficiaire' => $beneficiaire,
    'tref_beneficiaire' => $ref_beneficiaire,
    'tengagement_montant' => $engagement_montant,
    'tbon_commande_num' => $bon_commande_num,
    'tengagement_motif' => $engagement_motif,
    'tdate_echeance' => $date_echeance,
    'tcode_budget' => $code_budget,
    'tengagement_date' => $engagement_date,
    'tref_marche' => $ref_marche,
    'tengagement_retenue' => $engagement_retenue,
    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ  structure fichier',
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