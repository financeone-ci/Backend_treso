<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
require_once '../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
if(isset($_GET['jeton']))
{
    $jeton = secure($_GET['jeton']);
 }else{
    $jeton = "";
 }

if(isset($_GET['type']) && !empty($_GET['type'])){
    switch ($_GET['type']){
        case 'C':
            // creation de devise
            if(isset($_POST['code']) && !empty($_POST['code']))
            {
                try{
                    $code = secure($_POST['code']);
                    $libelle = secure($_POST['libelle']);
                    $taux = secure($_POST['taux']);
                    $base = secure($_POST['base']);
                    $cent = secure($_POST['cent']);
                    $sigle = secure($_POST['sigle']);

                    // modifier dans la BDD
                    $t1 = array(
                        'tcode' => $code,
                        'tlibelle' => $libelle,
                        'taux' => $taux,
                        'tbase' => $base,
                        'tcent' => $cent,
                        'tsigle' => $sigle,
                    );

                    $req1 = $DB->prepare("INSERT INTO `devise` (`CODE_DEVISE`, `LIBELLE_DEVISE`, `TAUX_DEVISE`, `DEVISE_DE_BASE`, `LIBELLE_CENTIMES`, `SIGLE_DEVISE`) VALUES ( :tcode, :tlibelle, :taux, :tbase, :tcent, :tsigle);
                ");
                    $req1->execute($t1);

                    // cas où la requête s'est bien exécutée
                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => $code,
                        'tlibelle' => $libelle,
                        'taux' => $taux,
                        'tbase' => $base,
                        'tcent' => $cent,
                        'tsigle' => $sigle,
                    );
                    // tableau des paramètres à enregistrer dans l'audit
                    $t2 = array(
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

                }catch (Exception $e)
                {
                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => $code,
                        'tlibelle' => $libelle,
                        'taux' => $taux,
                        'tbase' => $base,
                        'tcent' => $cent,
                        'tsigle' => $sigle,
                    );
                    // tableau des paramètres à enregistrer dans l'audit
                    $t2 = array(
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
                        "message" =>"Enregistrement impossible",
                    ];
                }
               /* $code = secure($obj->code);
                if(isset($obj->libelle) )*/
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Connexion impossible",
                ];
            }
            break;
        case 'R':
            
                try{
                    $sql = "SELECT `IDDEVISE` As id,`CODE_DEVISE`,`LIBELLE_DEVISE`,`TAUX_DEVISE`,`DEVISE_DE_BASE`,`LIBELLE_CENTIMES`,`SIGLE_DEVISE` FROM `devise` ";
                    $req = $DB->query($sql);
                    $row_user = $req->fetchAll(PDO::FETCH_OBJ);
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row_user,
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
            
            if(isset($obj->id) && !empty($obj->id))
            {
                $id = secure($obj->id);
                $code = secure($obj->code);
                
                if(isset($obj->libelle) )
                $libelle = secure($obj->libelle);
                else $libelle ="";
                if(isset($obj->taux) )
                $taux = secure($obj->taux);
                else $taux ="";
                if(isset($obj->base) )
                $base = secure($obj->base);
                else $base ="";
                if(isset($obj->cent) )
                $cent = secure($obj->cent);
                else $cent ="";
                if(isset($obj->sigle) )
                $sigle = secure($obj->sigle);
                else $sigle ="";

                // modifier dans la BDD
                $t1 = array(
                    'tid' => $id,
                    'tcode' => $code,
                    'tlibelle' => $libelle,
                    'ttaux' => $taux,
                    'tbase' => $base,
                    'tcent' => $cent,
                    'tsigle' => $sigle,
                );
                 
                $req1 = $DB->prepare("UPDATE `devise` SET `CODE_DEVISE` = :tcode, `LIBELLE_DEVISE` = :tlibelle, `TAUX_DEVISE` = :ttaux, `DEVISE_DE_BASE` = :tbase, `LIBELLE_CENTIMES` = :tcent, `SIGLE_DEVISE` = :tsigle WHERE `devise`.`IDDEVISE` = :tid; 
                ");
                if($req1->execute($t1)){
                  
                        ///////////// Audit système ///////////////////
 $tnew = array(
    'tid' => $id,
    'tcode' => $code,
    'tlibelle' => $libelle,
    'taux' => $taux,
    'tbase' => $base,
    'tcent' => $cent,
    'tsigle' => $sigle,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ devise',
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
    'taux' => $taux,
    'tbase' => $base,
    'tcent' => $cent,
    'tsigle' => $sigle,
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
        case 'D':// Suppression 
           
            if(isset($_GET['id'] ) && !empty($_GET['id'])){
                $id = secure($_GET['id']);
                  
                 $t1 = array(
                    
                    'tid' => $id,
                );
                $req1 = $DB->prepare("DELETE FROM devise WHERE IDDEVISE = :tid");
                if($req1->execute($t1)){
                   
                      ///////////// Audit système ///////////////////
 $tnew = array(
    'tcode' => '',
    'tlibelle' => '',
    'taux' => '',
    'tbase' => '',
    'tcent' => '',
    'tsigle' => '',
    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppression devise',
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
    'tcode' => '',
    'tlibelle' => '',
    'taux' => '',
    'tbase' => '',
    'tcent' => '',
    'tsigle' => '',
    'tid' => $id,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Suppresion devise',
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

