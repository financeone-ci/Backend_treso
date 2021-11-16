<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
$obj = json_decode(file_get_contents('php://input'));
$infoHttp = array();
$id = uniqid();
if(isset($_GET['type']) && !empty($_GET['type'])){
    switch ($_GET['type']){
        case 'C':
            if(isset($obj->libelle) && !empty($obj->libelle)){
                $libelle = secure($obj->libelle);
                if(isset($obj->description) )
                $description = secure($obj->description);
                else $description ="";
                 // création du profil
                // Enregistrer dans la BDD
                $t1 = array(
                    'tlibelle' => $libelle,
                    'tdescription' => $description,
                );
                $req1 = $DB->prepare("INSERT INTO profil (profil_libelle, profil_description)
                                                VALUES (:tlibelle, :tdescription)");
                if($req1->execute($t1)){
                  $idProfil = $DB->lastInsertId();
                    try{
                         // recuperation des elements du sous-menu pour attribuer les droits
                        $sql = "SELECT e_smenu_id FROM `e_smenu`";
                        $req = $DB->query($sql);
                        $d = $req->fetchAll();
                        // Initialisation des différents droits du profil
                        foreach( $d as $c ){
                            $t2 = array(
                                'tdl' => 0,
                                'tdc' => 0,
                                'tdm' => 0,
                                'tds' => 0,
                                'tesm' => $c['e_smenu_id'],
                                'tidp' => $idProfil,
                            );
                            $req2 = $DB->prepare("INSERT INTO `droits` (`droits_lecture`, `droits_creer`, `droits_modifier`, `droits_supprimer`, `e_smenu_id`, `profil_id`) 
                                                 VALUES (:tdl, :tdc, :tdm, :tds, :tesm, :tidp)");
                            $req2->execute($t2);
                        }
         // MAJ Audit ******************************************************
         $ip = $_SERVER['REMOTE_ADDR'];
         $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $t2 = array(
          'tid' => "",
          'tnom' => "",
          'tip' => $ip,
          'tmachine' => $machine,
                    );
 $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'C', 'Création d\'un profil', '1', '', 'Id: ".$idProfil." code: ".$libelle."  description:  ".$description." ',  :tid);
 ");
 $req2->execute($t2);
             
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                    }catch (PDOException $e)
                    {
                           // MAJ Audit ******************************************************
         $ip = $_SERVER['REMOTE_ADDR'];
         $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $t2 = array(
          'tid' => "",
          'tnom' => "",
          'tip' => $ip,
          'tmachine' => $machine,
                    );
 $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'C', 'Création d\'un profil', '0', '', '".$e." - Id: ".$idProfil." code: ".$libelle."  description:  ".$description." ',  :tid);
 ");
 $req2->execute($t2);
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Chargement des données impossible.",
                            "jeton" => false,
                        ];
                    }
                }else{
                      // MAJ Audit ******************************************************
         $ip = $_SERVER['REMOTE_ADDR'];
         $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $t2 = array(
          'tid' => "",
          'tnom' => "",
          'tip' => $ip,
          'tmachine' => $machine,
                    );
 $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'C', 'Création d\'un profil', '0', '', ' Chargement des données impossible.',  :tid);
 ");
 $req2->execute($t2);
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Chargement des données impossible.",
                    ];
                }
            }else{
                   // MAJ Audit ******************************************************
         $ip = $_SERVER['REMOTE_ADDR'];
         $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $t2 = array(
          'tid' => "",
          'tnom' => "",
          'tip' => $ip,
          'tmachine' => $machine,
                    );
 $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'C', 'Création d\'un profil', '0', '', 'Paramètres incorrects',  :tid);
 ");
 $req2->execute($t2);
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres incorrects",
                ];
            }
            break;
        case 'R':
           if(isset($_GET['id']))
           {
            try{
                $id = secure(($_GET['id']));
                            $sql = "SELECT profil_id as id, profil_libelle, profil_description FROM profil WHERE profil_id=$id";
                            $req = $DB->query($sql);
                            $row_profil = $req->fetch(PDO::FETCH_OBJ);
                            $infoHttp = [
                                "reponse" => "success",
                                "infos" => $row_profil,
                            ];
                        }catch (PDOException $e)
                        {
                              // MAJ Audit ******************************************************
         $ip = $_SERVER['REMOTE_ADDR'];
         $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $t2 = array(
          'tid' => "",
          'tnom' => "",
          'tip' => $ip,
          'tmachine' => $machine,
                    );
 $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'R', 'Lecture du profil ".$id."', '0', '', '".$e." ',  :tid);
 ");
 $req2->execute($t2);
                            $infoHttp = [
                                "reponse" => "error",
                                "message" => "Chargement des données impossible.",
                            ];
                        }
           }
           else
           { try{
                $sql = "SELECT profil_id as id, profil_libelle, profil_description FROM profil ORDER BY profil_libelle";
                $req = $DB->query($sql);
                $row_profil = $req->fetchAll(PDO::FETCH_OBJ);
    
                $infoHttp = [
                    "reponse" => "success",
                    "infos" => $row_profil,
                ];
            }catch (PDOException $e)
            {
                  // MAJ Audit ******************************************************
         $ip = $_SERVER['REMOTE_ADDR'];
         $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $t2 = array(
          'tid' => "",
          'tnom' => "",
          'tip' => $ip,
          'tmachine' => $machine,
                    );
 $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'R', 'Lecture des profils', '0', '', '".$e." ',  :tid);
 ");
 $req2->execute($t2);
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Chargement des données impossible.",
                ];
            }}
            break;
        case 'U': // MAJ
            if(isset($obj->libelle) && !empty($obj->libelle) && isset($obj->id) && !empty($obj->id)){
                $id = secure($obj->id);
                $libelle = secure($obj->libelle);
                if(isset($obj->description) )
                $description = secure($obj->description);
                else $description ="";
                // modifier dans la BDD
                $t1 = array(
                    'tlibelle' => $libelle,
                    'tdescription' => $description,
                    'tid' => $id,
                );
                $req1 = $DB->prepare("UPDATE profil SET  profil_libelle = :tlibelle, profil_description = :tdescription WHERE profil_id = :tid ");
                if($req1->execute($t1)){
                  
      // MAJ Audit ******************************************************
      $ip = $_SERVER['REMOTE_ADDR'];
      $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$t2 = array(
       'tid' => "",
       'tnom' => "",
       'tip' => $ip,
       'tmachine' => $machine,
                 );
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'U', 'Mise à jour d\'un profil', '1', '',  'Id: ".$id.", Code: ".$libelle."  description: ".$description."',  :tid);
");
$req2->execute($t2);                
                     
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                }else{
      // MAJ Audit ******************************************************
      $ip = $_SERVER['REMOTE_ADDR'];
      $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$t2 = array(
       'tid' => "",
       'tnom' => "",
       'tip' => $ip,
       'tmachine' => $machine,
                 );
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'U', 'Mise à jour d\'un profil', '0', '',  'Id: ".$id.", Code: ".$libelle."  description: ".$description."',  :tid);
");
$req2->execute($t2); 
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Chargement des données impossible.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres incorrects_",
                ];
            }
            break;
            ///////////////////////////////
        case 'D': // Suppression 
            if(isset($_GET['id'] ) && !empty($_GET['id'])){
                $id = secure($_GET['id']);
                 // création du profil
                 $t1 = array(
                    
                    'tid' => $id,
                );
                $req1 = $DB->prepare("DELETE FROM droits WHERE profil_id= :tid");
                if($req1->execute($t1)){
                   
                    try{
                     
                        
                        $req2 = $DB->prepare("DELETE FROM profil WHERE profil_id= :tid");
                            $req2->execute($t1);
                    // MAJ Audit ******************************************************
      $ip = $_SERVER['REMOTE_ADDR'];
      $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$t2 = array(
       'tid' => "",
       'tnom' => "",
       'tip' => $ip,
       'tmachine' => $machine,
                 );
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'U', 'Suppression à jour d\'un profil', '1', '',  'Id: ".$id."',  :tid);
");
$req2->execute($t2); 
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Supprimé avec succès",
                    ];
                    }catch (PDOException $e)
                    {
     // MAJ Audit ******************************************************
     $ip = $_SERVER['REMOTE_ADDR'];
     $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$t2 = array(
      'tid' => "",
      'tnom' => "",
      'tip' => $ip,
      'tmachine' => $machine,
                );
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'U', 'Suppression à jour d\'un profil', '0', '',  '".$e."',  :tid);
");
$req2->execute($t2); 
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Chargement des données impossible.",
                        ];
                    }
                }else{
                    // MAJ Audit ******************************************************
     $ip = $_SERVER['REMOTE_ADDR'];
     $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$t2 = array(
      'tid' => "",
      'tnom' => "",
      'tip' => $ip,
      'tmachine' => $machine,
                );
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'U', 'Suppression à jour d\'un profil', '0', '',  '".$e."',  :tid);
");
$req2->execute($t2); 
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Chargement des données impossible.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres incorrects",
                ];
            }
            ////////////////////////
            break;
        default :
            $infoHttp = [
                "reponse" => "error",
                "message" => "Impossible d'afficher des données",
            ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Impossible d'afficher des données",
    ];
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);?>