<?php
header('Content-Type: application/json; charset=utf8');
header('Access-Control-Allow-origin: *');
header('Access-Control-Allow-Headers: *');

require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
$obj = json_decode(file_get_contents('php://input'));
$infoHttp = array();
$id = uniqid();
if(isset($_GET['type']) && !empty($_GET['type'])){
    switch ($_GET['type']){
        case 'C':

            break;
        case 'R':
            if(isset($_GET['id']))
            {
                try{
                    $id = secure(($_GET['id']));
                    $sql = "SELECT IDBANQUE as id, X_MONTCHIFFRE as xchiffre, Y_MONTCHIFFRE as ychiffre, X_MONTLETTRE1 as xlettre1, Y_MONTLETTRE1 as ylettre1,
                                   X_MONTLETTRE2 as xlettre2, Y_MONTLETTRE2 as ylettre2, X_DESTINATAIRE as xbenef, Y_DESTINATAIRE as ybenef,
                                   X_VILLE as xville, Y_VILLE as yville, X_DATE as xdate, Y_DATE as ydate, LONG_BAR_CFR_LTR as lglettre, X_DATE_COUP as xdatecp, Y_DATE_COUP as ydatecp,
                                   X_BENEF_COUP as xbenefcp, Y_BENEF_COUP as ybenefcp, X_MONT_COUP as xchiffrecp, Y_MONT_COUP as ychiffrecp, X_MOTIF as xmotif, Y_MOTIF as ymotif, 
                                   DEUX_DATE as deux_date, X_DATE_PART1 as xdate1, Y_DATE_PART1 as ydate1, X_DATE_PART2 as xdate2, Y_DATE_PART2 as ydate2
                            FROM dimcheque 
                            WHERE IDBANQUE = $id";
                    $req = $DB->query($sql);
                    $row_mesure = $req->fetch(PDO::FETCH_UNIQUE);
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row_mesure,
                    ];
                }catch (PDOException $e)
                {
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Chargement des données impossible.",
                    ];
                }
            }else{
                 $infoHttp = [
                        "reponse" => "error",
                        "message" => "Chargement des données impossible.",
                    ];
            }
            break;
        case 'U': // MAJ
            if(isset($obj->id) && !empty($obj->id))
            {
                try{
                    // modifier dans la BDD
                    $t1 = array
                    (
                        'tid' => secure($obj->id),
                        'tlglettre' => secure($obj->lglettre),
                        't2date' => secure($obj->deux_date),
                        'txchiffre' => secure($obj->xchiffre),
                        'tychiffre' => secure($obj->ychiffre),
                        'txlettre1' => secure($obj->xlettre1),
                        'tylettre1' => secure($obj->ylettre1),
                        'txlettre2' => secure($obj->xlettre2),
                        'tylettre2' => secure($obj->ylettre2),
                        'txbenef' => secure($obj->xbenef),
                        'tybenef' => secure($obj->ybenef),
                        'txville' => secure($obj->xville),
                        'tyville' => secure($obj->yville),
                        'txdate' => secure($obj->xdate),
                        'tydate' => secure($obj->ydate),
                        'txdate1' => secure($obj->xdate1),
                        'tydate1' => secure($obj->ydate1),
                        'txdate2' => secure($obj->xdate2),
                        'tydate2' => secure($obj->ydate2),
                        'txdatecp' => secure($obj->xdatecp),
                        'tydatecp' => secure($obj->ydatecp),
                        'txbenefcp' => secure($obj->xbenefcp),
                        'tybenefcp' => secure($obj->ybenefcp),
                        'txchiffrecp' => secure($obj->xchiffrecp),
                        'tychiffrecp' => secure($obj->ychiffrecp),
                        'txmotif' => secure($obj->xmotif),
                        'tymotif' => secure($obj->ymotif),
                    );
                    $req1 = $DB->prepare("UPDATE dimcheque SET X_MONTCHIFFRE = :txchiffre, Y_MONTCHIFFRE = :tychiffre, X_MONTLETTRE1 = :txlettre1, Y_MONTLETTRE1 = :tylettre1,
                                                           X_MONTLETTRE2 = :txlettre2, Y_MONTLETTRE2 = :tylettre2, X_DESTINATAIRE = :txbenef, Y_DESTINATAIRE = :tybenef,
                                                           X_VILLE = :txville, Y_VILLE = :tyville, X_DATE = :txdate, Y_DATE = :tydate, LONG_BAR_CFR_LTR = :tlglettre, X_DATE_COUP = :txdatecp, Y_DATE_COUP = :tydatecp,
                                                           X_BENEF_COUP = :txbenefcp, Y_BENEF_COUP = :tybenefcp, X_MONT_COUP = :txchiffrecp, Y_MONT_COUP = :tychiffrecp, X_MOTIF = :txmotif, Y_MOTIF = :tymotif, 
                                                           DEUX_DATE = :t2date, X_DATE_PART1 = :txdate1, Y_DATE_PART1 = :tydate1, X_DATE_PART2 = :txdate2, Y_DATE_PART2 = :tydate2
                                                    WHERE IDBANQUE = :tid");
                    $req1->execute($t1);
                    // cas où la requête s'est bien exécutée
                    $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                }catch (PDOException $e) {
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Mise à jour des données impossible 1.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Mise à jour des données impossible.",
                ];
            }
            break;
        ///////////////////////////////
        case 'D': // Suppression
            break;
        default :
            $infoHttp = [
                "reponse" => "error",
                "message" => "Impossible de charger les données",
            ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "connexion impossible.",
    ];
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);?>