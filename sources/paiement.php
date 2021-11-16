<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
require_once '../fonctions/StatusPaiements.php';
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
  $nombreDeReponses = 0;  
  $listMontants = '';       
            if(  
                isset($obj->compte) && !empty($obj->compte) 
              
                    ){
                
//                $beneficiaire = secure($obj->beneficiaire);
                $compte = secure($obj->compte);
                
                if(isset($obj->nature) )
                $nature = secure($obj->nature);
                else $nature ="";
               
               
                if(isset($obj->budget) )
                $budget = secure($obj->budget);
                else $budget ="";
               
               
                if(isset($obj->categorie) )
                $categorie = secure($obj->categorie);
                else $categorie ="";
               
               
                if(isset($obj->montant) )
                $montant = secure($obj->montant);
                else $montant =0;
               
               
                if(isset($obj->type) )
                $type = secure($obj->type);
                else $type ="";

              


                        // conversion des id-engagement en string 
                       $listId = implode(",", $obj->id);  
                     
              foreach ($obj->id  as $item) {
                  # code...
                   $TabSolde = useBdd('getSolde', ['id'=>$item]) ; 
                   if($type == "Pourcentage"){
                  
                    $montantFinal[$item]= $TabSolde['SOLDE']*$montant/100;
                  } elseif($type == "Tranche"){
                       $montantFinal[$item]= (int) $montant ;
                  }elseif($type == "Total"){
                   
                    $montantFinal[$item]= $TabSolde['SOLDE'];
                  }
                  $beneficiaire[$item] = $TabSolde['BENEFICIAIRE'];
                  $motif[$item] = $TabSolde['MOTIF'];
              }
             
               try {
                   $statutCreation = StatutCreation();
                   foreach ($obj->id as $item) {
                       # création des paiements
                       $t1 = array(
                    'tcompte' => $compte,
                    'tnature' => $nature,
                    'tbudget' => $budget,
                    'tcategorie' => $categorie,
                    'tstatut' => $statutCreation,
                    'tmontant' => $montantFinal[$item] ,
                    'tbenef' => $beneficiaire[$item] ,
                    'tmotif' => $motif[$item] ,
                    
                );
              
                       $req1 = $DB->prepare(
                           "INSERT INTO `paiement` ( `ID_STATUT_PAIEMENT`,  `DATE_PAIEMENT`,  `MONTANT_PAIEMENT`, `CODE_COMPTE`,   `CODE_NATURE`,  `CODE_BUDGET_PAIE`,  `ID_CATEGORIE_PAIEMENT`, BENEFICIAIRE_PAIEMENT, MOTIF_PAIEMENT) VALUES (:tstatut,  '".date('Y-m-d')."',  :tmontant, :tcompte,    :tnature,   :tbudget,  :tcategorie, :tbenef, :tmotif );"
                       );
                                
                       $req1->execute($t1);
                       $idPaiement = $DB->lastInsertId();

                       $t2 = array(
                    'tpaiement' => $idPaiement,
                    'tengagement' => $item,
                    'tmontant' => $montantFinal[$item] ,
                    
                        );
               
                       $req2 = $DB->prepare(
                           "INSERT INTO `engagement_paiement` (  `IDPAIEMENT`, `IDENGAGEMENT`, `MONTANT`) VALUES ( :tpaiement, :tengagement, :tmontant);"
                       );
                 
                      if( $req2->execute($t2) )
                   
               if( $montantFinal[$item] = $TabSolde['SOLDE']) { $statut = 3; } else { $statut = 2; }
                $t3 = array(
                    'tidengagement' => $idPaiement,                    
                    'tstatut' => $statut ,                    
                        );
               
                       $req3 = $DB->prepare(
                           "UPDATE engagement SET ID_STATUT_ENGAGEMENT = :tstatut WHERE ID_ENGAGEMENT = :tidengagement "
                       );
                 
                        $req3->execute($t3) ;
                   
            $nombreDeReponses ++;
            $listMontants = $listMontants.'*'.$montantFinal[$item];
                    
                   }               
                      
                   $infoHttp = [
        "reponse" => "success",
        "message" => "".$nombreDeReponses.' paiement(s) enregistré(s) avec succès   ' ,
    ];
                   //**/// */
               }  catch (\Throwable $e) {
                $infoHttp = [
                "reponse" => "error",
                "message" => $e . $nombreDeReponses,
            ];  
           # code...
///////////// Audit système ///////////////////

    }
                
                
            }else{
                  
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "veuillez vérifier votre connexion",
                ];
            }
           
            break;
      
        case 'F': // creation paiement fusionné
  $nombreDeReponses = 0;  
  $listMontants = '';       
            if(  
                isset($obj->compte) && !empty($obj->compte) 
              
                    ){
                
                $compte = secure($obj->compte);
                
                if(isset($obj->nature) )
                $nature = secure($obj->nature);
                else $nature ="";
               
               
                if(isset($obj->budget) )
                $budget = secure($obj->budget);
                else $budget ="";
               
               
                if(isset($obj->categorie) )
                $categorie = secure($obj->categorie);
                else $categorie ="";
               
               
                if(isset($obj->montant) )
                $montant = secure($obj->montant);
                else $montant =0;
               
               
                if(isset($obj->type) )
                $type = secure($obj->type);
                else $type ="";

              


                        // conversion des id-engagement en string 
                       $listId = implode(",", $obj->id);  
                     
                foreach ($obj->id  as $item) {
                  # code...
                   $TabSolde = useBdd('getSolde', ['id'=>$item]) ; 
                  $montantFinal[$item]= $TabSolde['SOLDE'];
                  $beneficiaire[$item] = $TabSolde['BENEFICIAIRE'];
                  $motif[$item] = $TabSolde['MOTIF'];
              }
             
               try {
                   $statutCreation = StatutCreation();
                   # création du paiements
                       $t1 = array(
                    'tcompte' => $compte,
                    'tnature' => $nature,
                    'tbudget' => $budget,
                    'tcategorie' => $categorie,
                    'tstatut' => $statutCreation,
                    'tmontant' => array_sum($montantFinal) ,
                    'tbenef' => $beneficiaire[$item] ,
                    'tmotif' => $motif[$item] ,
                );
              
                       $req1 = $DB->prepare(
                           "INSERT INTO `paiement` ( `ID_STATUT_PAIEMENT`,  `DATE_PAIEMENT`,  `MONTANT_PAIEMENT`, `CODE_COMPTE`,   `CODE_NATURE`,  `CODE_BUDGET_PAIE`,  `ID_CATEGORIE_PAIEMENT`, BENEFICIAIRE_PAIEMENT, MOTIF_PAIEMENT) VALUES (:tstatut,  '".date('Y-m-d')."',  :tmontant, :tcompte,    :tnature,   :tbudget,  :tcategorie, :tbenef, :tmotif );"
                       );
                                
                       $req1->execute($t1);
                       $idPaiement = $DB->lastInsertId();
                   foreach ($obj->id as $item) {
                       

                       $t2 = array(
                    'tpaiement' => $idPaiement,
                    'tengagement' => $item,
                    'tmontant' => $montantFinal[$item] ,
                    
                        );
               
                       $req2 = $DB->prepare(
                           "INSERT INTO `engagement_paiement` (  `IDPAIEMENT`, `IDENGAGEMENT`, `MONTANT`) VALUES ( :tpaiement, :tengagement, :tmontant);"
                       );
                 
                      if( $req2->execute($t2) )
                   
                $statut = 3;  
                $t3 = array(
                    'tidengagement' => $idPaiement,                    
                    'tstatut' => $statut ,                    
                        );
               
                       $req3 = $DB->prepare(
                           "UPDATE engagement SET ID_STATUT_ENGAGEMENT = :tstatut WHERE ID_ENGAGEMENT = :tidengagement "
                       );
                 
                        $req3->execute($t3) ;
                   
            $nombreDeReponses ++;
            $listMontants = $listMontants.'*'.$montantFinal[$item];
                    
                   }               
                      
                   $infoHttp = [
        "reponse" => "success",
        "message" =>  ' paiement  enregistré  avec succès   ' ,
    ];
                   //**/// */
               }  catch (\Throwable $e) {
                $infoHttp = [
                "reponse" => "error",
                "message" => $e . $nombreDeReponses,
            ];  
           # code...
///////////// Audit système ///////////////////

    }
                
                
            }else{
                  
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "veuillez vérifier votre connexion",
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



