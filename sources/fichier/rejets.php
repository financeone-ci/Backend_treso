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
if(isset($_GET['type']) && !empty($_GET['type']))
{
    switch ($_GET['type'])
    {
        case 'C': // creation
            break;
        case 'R':
            try{
                $dateDeb = secure($_GET['debut']);
                $dateFin = secure($_GET['fin'].' 23:59');

                $sql = "SELECT `IDREJET` AS id,`IDIMPORT`,`TAXE`,`NUM_ENGAGEMENT`,`BENEFICIAIRE`,`REF_BENEFICIAIRE`,`MONTANT`,`NUM_BON`,`MOTIF`,`DATE_ECHEANCE`,`CODE_BUDGET`,`DATE_ENGAGEMENT`,`REF_MARCHE`,`RETENUE`,`DATE_REJET`,`MOTIF_REJET`,`DATE_IMPORTATION` 
                        FROM `rejet` 
                        WHERE DATE_IMPORTATION BETWEEN '$dateDeb' AND '$dateFin' ORDER BY DATE_IMPORTATION DESC";
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
                    "message" => $e,
//                    "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                ];
            }
            break;
        case 'U': // MAJ
            break;
        case 'D':// Suppression
            if(isset($_GET['id'] ) && !empty($_GET['id']))
            {
                try{
                    $id = secure($_GET['id']);
                    $req = $DB->prepare("DELETE FROM rejet WHERE IDREJET IN ($id)");
                    $req->execute();
//

                ///////////// Audit système /////////////////////
                    $tnew = array(
                        'tcode' => "",
                        'tlibelle' => "",
                        'tdirecteur' => "",
                        'tadresse' => "",
                        'tcontact' => "",
                        'tgestionnaire' => "",
                        'tadresse_web' => "",
                    );
                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => 'Suppression rejet',
                        'tdescription' => "",
                        'tissue' => "1",
                    );
                    audit_sys($t2, $tnew,$jeton);
//                    ///////////// Audit système ///////////////////

                    // cas où la requête s'est bien exécutée
                    $infoHttp = [
                        "reponse" => "success",
                        "message" => "Supprimé avec succès",
                    ];

                }catch (PDOException $e)
                {
                    $tnew = array(
                        'tid' => $id,
                    );

                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => 'Suppresion rejet',
                        'tdescription' => "",
                        'tissue' => "0",
                    );
                    audit_sys($t2, $tnew,$jeton);

                    $MYSQL_DUPLICATE_CODES=array(1062, 23000);
                    if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES))
                    {
                        // duplicate entry, do something else
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Liaisons détectées : Impossible de supprimer cet élément.",
                        ];
                    } else {
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                        ];
                    }
                }
            }
            break;
        default :
            $infoHttp = [
                "reponse" => "error",
                "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
            ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
    ];
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);?>