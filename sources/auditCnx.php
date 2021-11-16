<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
$obj = json_decode(file_get_contents('php://input'));
$infoHttp = array();
$id = uniqid();
if(isset($_GET['type']) && !empty($_GET['type'])){
    switch ($_GET['type']){
        case 'C':
            if(isset($_GET['deconnexion']))
            {
                $ip = $_SERVER['REMOTE_ADDR'];
             $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
      $t2 = array(
              'tid' => secure($_GET['user_id']),
              'tnom' => secure($_GET['user_nom']),
              'traison' => secure($_GET['raison']),
              'tip' => $ip,
              'tmachine' => $machine,
                        );
     $req2 = $DB->prepare("INSERT INTO `audit_cnx` (`audit_cnx_userid`, `audit_cnx_usernom`, `audit_cnx_ip`, `audit_cnx_machine`, `audit_cnx_action`,`audit_cnx_description`) VALUES (:tid, :tnom, :tip, :tmachine, 'out', :traison)");
     $req2->execute($t2);
            }
            else
            {}
             break;
        case 'R':
           
             try{
                $sql = "SELECT `audit_cnx_id` AS id,`audit_cnx_userid`,`audit_cnx_usernom`,`audit_cnx_ip`,`audit_cnx_machine`,`audit_cnx_action`,`audit_cnx_issue`,`audit_cnx_description`, `audit_cnx_date` FROM `audit_cnx` WHERE `audit_cnx_date` BETWEEN '".$_GET['debut']."' AND '".$_GET['fin']." 23:59' ORDER BY `audit_cnx`.`audit_cnx_date` DESC";
                $req = $DB->query($sql);
                $row_audit = $req->fetchAll(PDO::FETCH_OBJ);
                $infoHttp = [
                    "reponse" => "success",
                    "infos" => $row_audit,
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
            
            break;
            ///////////////////////////////
        case 'D': // Suppression 
             # Do nothing
            
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