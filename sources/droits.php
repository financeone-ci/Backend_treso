<?php
header('Content-Type: application/json; charset=utf8');
header('Access-Control-Allow-origin: *');
header('Access-Control-Allow-Headers: *');
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
$obj = json_decode(file_get_contents('php://input'));

//require_once '../fonctions/getToken.php';
$infoHttp = array();
if(isset($_GET['type'])  && !empty($_GET['type'])  ){
    if(isset($_GET['idprofil'])) $idP = $_GET['idprofil']; else
    $idP = 0;
    switch ($_GET['type']){
        case 'C': // Création
            break;
        case 'R': // Lecture
                try{
                    $sql = "SELECT droits.e_smenu_id as id , e_smenu_libelle, droits_lecture, droits_creer, droits_modifier, droits_supprimer, smenu.smenu_id as idsmenu, smenu_libelle, droits_id, profil_libelle
                            FROM droits 
                                JOIN e_smenu ON droits.e_smenu_id = e_smenu.e_smenu_id 
                                JOIN profil ON droits.profil_id = profil.profil_id 
                                JOIN smenu ON e_smenu.smenu_id = smenu.smenu_id 
                            WHERE profil.profil_id = '$idP'";
                    $req = $DB->query($sql);
                 
                        $d = $req->fetchAll(PDO::FETCH_OBJ);
                        
                        $infoHttp = [
                            "reponse" => "success",
                            "infos" => $d,
                        ];
                }catch (PDOException $e)
                {
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                        "jeton" => false,
                    ];
                }
            break;
        case 'U': // Modification
            if(isset($obj->id) && !empty($obj->id) ){
                $id = secure($obj->id);
                $column = secure($obj->column);
               $droit =  secure($obj->value);
               if($droit==1){
                   $droit = 0;
               }else{
                    $droit = 1;
               }
               
                // modifier dans la BDD
                $t1 = array(
                     
                    'tid' => $id,
                );
                $req1 = $DB->prepare("UPDATE `droits` SET `droits`.`".$column."` = $droit WHERE `droits`.`droits_id` = :tid");
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
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'U', 'Mise à jour de droits', '1', '',  'id:".$id."',  :tid);
");
$req2->execute($t2); 
                     
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "fait",
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
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'U', 'MAJ droits', '0', '',  '".$e."',  :tid);
");
$req2->execute($t2); 
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => " Paramètres incorrects_",
                ];
            }
            break; 
        case 'D': // Suppression
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
        "message" => "Impossible d'afficher des données*",
    ];
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);?>