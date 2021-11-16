<?php
require_once '../fonctions/StatusPaiements.php';
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
    switch ($_GET['type'])
    {
        case 'R':
            try
            {
                $sql = "SELECT IDPAIEMENT as id, LIBELLE_STATUT_PAIEMENT, BENEFICIAIRE_PAIEMENT, DATE_FORMAT(DATE_PAIEMENT, '%d-%m-%Y') as DATE_PAIE, MONTANT_PAIEMENT, MOTIF_PAIEMENT, CODE_NATURE, CODE_BUDGET_PAIE, CODE_COMPTE, CODE_CATEGORIE_PAIEMENT
                        FROM paiement
                             JOIN statut_paiement ON paiement.ID_STATUT_PAIEMENT = statut_paiement.ID_STATUT_PAIEMENT
                             JOIN categorie_paiement ON paiement.ID_CATEGORIE_PAIEMENT = categorie_paiement.ID_CATEGORIE_PAIEMENT
                        WHERE paiement.ID_STATUT_PAIEMENT = 3";
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
                    "message" => "Connexion aux données impossible.".$e,
                ];
            }
            break;
        case 'U': // MAJ
                try{
                    $id = $_GET['id'];
                        // modifier dans la BDD
                    $t1 = array(
                        'tstatut' => 4,
                    );

                    $req1 = $DB->prepare("UPDATE paiement SET ID_STATUT_PAIEMENT = :tstatut WHERE paiement.IDPAIEMENT IN ($id)");
                    $req1->execute($t1);
                    
                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => $id,
                    );

                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => 'Validation paiements',
                        'tdescription' => "",
                        'tissue' => "1",
                    );
                    audit_sys($t2, $tnew,$jeton);

                    // cas où la requête s'est bien exécutée
                    $infoHttp = [
                        "reponse" => "success",
                        "message" => "approuver avec succès",
                    ];
                }catch(PDOException $e)
                {
                    
                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => $id,
                    );

                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => 'Approbation de paiements',
                        'tdescription' => "",
                        'tissue' => "0",
                    );
                    audit_sys($t2, $tnew,$jeton);
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible.",
                    ];
                }
            break;
        default :
            $infoHttp = [
                "reponse" => "error",
                "message" => "paramètres incorrects",
            ];
          }
    }else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "paramètres incorrects",
    ];
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);

?>
