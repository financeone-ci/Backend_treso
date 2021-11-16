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
// R�cup�ration de l'utilisateur
if(!empty($jeton)) {
    $decoded = JWT::decode($jeton, $key, array('HS256'));
    $user = $decoded->user_nom . ' ' . $decoded->user_prenom;
}else{
    $user = "utilisateur introuvable";
}

if(isset($_GET['type']) && !empty($_GET['type'])){
    switch ($_GET['type']){
        case 'C': // creation
           
            if(  
                isset($obj->beneficiaire) && !empty($obj->beneficiaire) &&
                isset($obj->montant) && !empty($obj->montant) &&
                isset($obj->motif) && !empty($obj->motif)
                    ){
                
//                $beneficiaire = secure($obj->beneficiaire);
                $beneficiaire = secure($obj->beneficiaire);
                $montant = secure($obj->montant);
                $motif = secure($obj->motif);
                if(isset($obj->num_engagement) )
                $num_engagement = secure($obj->num_engagement);
                else $num_engagement ="";
               
                if(isset($obj->ref_benef) )
                $ref_benef = secure($obj->ref_benef);
                else $ref_benef ="";
               
                if(isset($obj->echeance) && !empty($obj->echeance)  )
                $echeance = secure($obj->echeance);
                else $echeance= date("Y-m-d") ;
               
               
                if(isset($obj->num_bon) )
                $num_bon = secure($obj->num_bon);
                else $num_bon ="";
               
                if(isset($obj->code_budget) )
                $code_budget = secure($obj->code_budget);
                else $code_budget ="";
               
                if(isset($obj->ref_marche) )
                $ref_marche = secure($obj->ref_marche);
                else $ref_marche ="";
               
                if(isset($obj->retenue) )
                $retenue = secure($obj->retenue);
                else $retenue ="";
               
                if(isset($obj->taxe) )
                $taxe = secure($obj->taxe);
                else $taxe ="";
               
                
                if(isset($obj->date_engagement) && !empty($obj->date_engagement) )
                $date_engagement = secure($obj->date_engagement);
                else $date_engagement = date("Y-m-d") ;
               
                
               
                // modifier dans la BDD
                $t1 = array(
                    'tbeneficiaire' => $beneficiaire,
                    'tmontant' => $montant,
                    'tmotif' => $motif,
                    'tnum_engagement' => $num_engagement,
                    'tref_benef' => $ref_benef,
                    'techeance' => $echeance,
                    'tnum_bon' => $num_bon,
                    'tcode_budget' => $code_budget,
                    'tref_marche' => $ref_marche,
                    'tretenue' => $retenue,
                    'ttaxe' => $taxe,
                    'tdate_engagement' => $date_engagement,
                );
               
                $req1 = $DB->prepare("INSERT INTO `engagement` (`NUM_ENGAGEMENT`, `BENEFICIAIRE`, `REF_BENEFICIAIRE`, `MONTANT`, `NUM_BON`, `MOTIF`, `TYPE_IMPORT`, `USER_IMPORT`, `DATE_ECHEANCE`, `ID_STATUT_ENGAGEMENT`, `CODE_BUDGET`,`DATE_ENGAGEMENT`, `REF_MARCHE`, `RETENUE`, `TAXE`) VALUES (:tnum_engagement , :tbeneficiaire, :tref_benef , :tmontant, :tnum_bon, :tmotif, 'manuelle', '$user', :techeance, '1', :tcode_budget,:tdate_engagement, :tref_marche, :tretenue, :ttaxe);");
               
               try {
                   $req1->execute($t1);

     // cas où la requête s'est bien exécutée

                        
 ///////////// Audit système ///////////////////
            $tnew =  array(
                'tbeneficiaire' => $beneficiaire,
                'tmontant' => $montant,
                'tmotif' => $motif,
                'tnum_engagement' => $num_engagement,
                'tref_benef' => $ref_benef,
                'techeance' => $echeance,
                'tnum_bon' => $num_bon,
                'tcode_budget' => $code_budget,
                'tref_marche' => $ref_marche,
                'tretenue' => $retenue,
                'ttaxe' => $taxe,
                'tdate_engagement' => $date_engagement,
            );
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création engagement',
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
                        $infoHttp = [
                        "reponse" => "error",
                        "message" => $e,
                    ];  
                   # code...
        ///////////// Audit système ///////////////////
                $tnew = array(
                    'tbeneficiaire' => $beneficiaire,
                    'tmontant' => $montant,
                    'tmotif' => $motif,
                    'tnum_engagement' => $num_engagement,
                    'tref_benef' => $ref_benef,
                    'techeance' => $echeance,
                    'tnum_bon' => $num_bon,
                    'tcode_budget' => $code_budget,
                    'tref_marche' => $ref_marche,
                    'tretenue' => $retenue,
                    'ttaxe' => $taxe,
                    'tdate_engagement' => $date_engagement,
                );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création engagement',
            'tdescription' => "",
            'tissue' => "0",
                      );
        audit_sys($t2, $tnew,$jeton);
         ///////////// Audit système ///////////////////
            }
                
            }else{
                  
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "veuillez vérifier votre connexion",
                ];
            }
            break;
        case 'R':

            if(isset($_GET['paie'])){
                try{
                     
 
                    $sql = "SELECT `ID_ENGAGEMENT` AS id,`NUM_ENGAGEMENT`,`BENEFICIAIRE`,`REF_BENEFICIAIRE`, 
                    engagement.`MONTANT`,  `NUM_BON`,`MOTIF`,`TYPE_IMPORT`,`USER_IMPORT`,`DATE_ECHEANCE`,`ID_STATUT_ENGAGEMENT`,`CODE_BUDGET`,`DATE_IMPORTATION`,`IDIMPORT`,`DATE_ENGAGEMENT`,`REF_MARCHE`,`RETENUE`,`TAXE` FROM `engagement` LEFT JOIN `engagement_paiement` ON engagement.ID_ENGAGEMENT = engagement_paiement.IDENGAGEMENT GROUP BY ID_ENGAGEMENT
                    ORDER BY DATE_ENGAGEMENT, DATE_IMPORTATION DESC ";
                    $req = $DB->query($sql);
                    $row = $req->fetchAll(PDO::FETCH_OBJ);
                    $tableauSoldes=[]; // Tableau des paiements
                    
                    foreach ($row as $item) {
                        
                        $solde =useBdd('getSolde',  ['id'=>$item->id ] );
  
                          if(  $solde['SOLDE']  > 0 ) // tri  des soldes supérieurs à 0
                        $tableauSoldes[] = ['id'=>$item->id, 'SOLDE'=> (float) $solde['SOLDE'], 'BENEFICIAIRE'=>$item->BENEFICIAIRE, 'MONTANT'=> (float) $item->MONTANT, 'NUM_ENGAGEMENT' => $item->NUM_ENGAGEMENT,  'REF_BENEFICIAIRE' => $item->REF_BENEFICIAIRE,  'NUM_BON' => $item->NUM_BON,  'MOTIF' => $item->MOTIF,  'TYPE_IMPORT' => $item->TYPE_IMPORT,  'DATE_ECHEANCE' => $item->DATE_ECHEANCE,  'CODE_BUDGET' => $item->CODE_BUDGET,  'DATE_IMPORTATION' => $item->DATE_IMPORTATION,  'IDIMPORT' => $item->IDIMPORT,  'DATE_ENGAGEMENT' => $item->DATE_ENGAGEMENT,  'REF_MARCHE' => $item->REF_MARCHE,  'RETENUE' => $item->RETENUE,  'TAXE' => $item->TAXE, ];
                        
                    }
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $tableauSoldes,
                    ];
                }catch (PDOException $e)
                {
  
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }else{
                try{
                    $dateDeb = secure($_GET['debut']);
                    $dateFin = secure($_GET['fin'].' 23:59');
    
                    $sql = "SELECT `ID_ENGAGEMENT` AS id,`NUM_ENGAGEMENT`,`BENEFICIAIRE`,`REF_BENEFICIAIRE`,engagement.`MONTANT`, `NUM_BON`,`MOTIF`,`TYPE_IMPORT`,`USER_IMPORT`,`DATE_ECHEANCE`,`ID_STATUT_ENGAGEMENT`,`CODE_BUDGET`,`DATE_IMPORTATION`,`IDIMPORT`,`DATE_ENGAGEMENT`,`REF_MARCHE`,`RETENUE`,`TAXE` FROM `engagement`  LEFT JOIN `engagement_paiement` ON engagement.ID_ENGAGEMENT = engagement_paiement.IDENGAGEMENT  WHERE DATE_ENGAGEMENT BETWEEN '$dateDeb' AND '$dateFin' ORDER BY DATE_ENGAGEMENT, DATE_IMPORTATION DESC ";
                    $req = $DB->query($sql);
                    $row = $req->fetchAll(PDO::FETCH_OBJ);
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row,
                        "get" => $dateDeb . " et " .$dateFin . ' / ' .$_GET['debut'],
                    ];
                }catch (PDOException $e)
                {
  
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }
            break;
        case 'U': // MAJ
            
            if(
                isset($obj->id) && !empty($obj->id) &&
                isset($obj->beneficiaire) && !empty($obj->beneficiaire) &&
                isset($obj->montant) && !empty($obj->montant) &&
                isset($obj->motif) && !empty($obj->motif)
                    ){
                
                $id = secure($obj->id);
                $beneficiaire = secure($obj->beneficiaire);
                $montant = secure($obj->montant);
                $motif = secure($obj->motif);
                if(isset($obj->num_engagement) )
                $num_engagement = secure($obj->num_engagement);
                else $num_engagement ="";
               
                if(isset($obj->ref_beneficiaire) )
                $ref_beneficiaire = secure($obj->ref_beneficiaire);
                else $ref_beneficiaire ="";
               
                if(isset($obj->date_echeance) )
                $date_echeance = secure($obj->date_echeance);
                else $date_echeance =("Y-m-d");
               
                if(isset($obj->num_bon) )
                $num_bon = secure($obj->num_bon);
                else $num_bon ="";
               
                if(isset($obj->code_budget) )
                $code_budget = secure($obj->code_budget);
                else $code_budget ="";
               
                if(isset($obj->ref_marche) )
                $ref_marche = secure($obj->ref_marche);
                else $ref_marche ="";
               
                if(isset($obj->retenue) )
                $retenue = secure($obj->retenue);
                else $retenue ="";
               
                if(isset($obj->taxe) )
                $taxe = secure($obj->taxe);
                else $taxe ="";

                if(isset($obj->date_engagement) )
                $date_engagement = secure($obj->date_engagement);
                else $date_engagement = date("Y-m-d") ;

                // modifier dans la BDD
                $t1 =  array(
                    'tid' => $id,
                    'tbeneficiaire' => $beneficiaire,
                    'tmontant' => $montant,
                    'tmotif' => $motif,
                    'tnum_engagement' => $num_engagement,
                    'tref_beneficiaire' => $ref_beneficiaire,
                    'tdate_echeance' => $date_echeance,
                    'tnum_bon' => $num_bon,
                    'tcode_budget' => $code_budget,
                    'tref_marche' => $ref_marche,
                    'tretenue' => $retenue,
                    'ttaxe' => $taxe,
                    'tdate_engagement' => $date_engagement,
                );
                
                $req1 = $DB->prepare("UPDATE `engagement` SET `NUM_ENGAGEMENT` = :tnum_engagement, `BENEFICIAIRE` = :tbeneficiaire, `REF_BENEFICIAIRE` = :tref_beneficiaire, `MONTANT` = :tmontant, `NUM_BON` = :tnum_bon, `MOTIF` = :tmotif, `DATE_ECHEANCE` = :tdate_echeance, `CODE_BUDGET` = :tcode_budget, `DATE_ENGAGEMENT` = :tdate_engagement, `REF_MARCHE`=:tref_marche, `RETENUE` = :tretenue, `TAXE` = :ttaxe WHERE `engagement`.`ID_ENGAGEMENT` = :tid;
                ");
                try {
                    $req1->execute($t1);
                  
                        ///////////// Audit système ///////////////////
                        $tnew =  array(
                            'tid' => $id,
                            'tbeneficiaire' => $beneficiaire,
                            'tmontant' => $montant,
                            'tmotif' => $motif,
                            'tnum_engagement' => $num_engagement,
                            'tref_beneficiaire' => $ref_beneficiaire,
                            'tdate_echeance' => $date_echeance,
                            'tnum_bon' => $num_bon,
                            'tcode_budget' => $code_budget,
                            'tref_marche' => $ref_marche,
                            'tretenue' => $retenue,
                            'ttaxe' => $taxe,
                            'tdate_engagement' => $date_engagement,
                        );
       
                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                         'tnom' => "",
                         'tip' => $ip,
                        'tmachine' => $machine,
                         'taction' => 'Modification',
                         'tdescription' => "Modification de la saisie",
                          'tissue' => "1",
                      );
                    audit_sys($t2, $tnew,$jeton);
                     
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Modifié avec succès",
                    ];
                   
                } catch (PDOException $e) {
                      ///////////// Audit système ///////////////////
                    $tnew =  array(
                        'tid' => $id,
                        'tbeneficiaire' => $beneficiaire,
                        'tmontant' => $montant,
                        'tmotif' => $motif,
                        'tnum_engagement' => $num_engagement,
                        'tref_beneficiaire' => $ref_beneficiaire,
                        'tdate_echeance' => $date_echeance,
                        'tnum_bon' => $num_bon,
                        'tcode_budget' => $code_budget,
                        'tref_marche' => $ref_marche,
                        'tretenue' => $retenue,
                        'ttaxe' => $taxe,
                        'tdate_engagement' => $date_engagement,
                    );
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Modification',
            'tdescription' => "Modification de la saisie",
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
                  
                $req1 = $DB->prepare("DELETE FROM engagement WHERE ID_ENGAGEMENT  IN ($id)");
                if($req1->execute()){
                   
                      ///////////// Audit système ///////////////////
                $tnew = array(
                    'tid' => $id,
                );
       
                $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                    'tid' => "",
                    'tnom' => "",
                    'tip' => $ip,
                    'tmachine' => $machine,
                    'taction' => 'Suppression ENGAGEMENT',
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
            'taction' => 'Suppression engagement',
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



