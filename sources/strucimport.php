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

if(isset($_GET['type']) && !empty($_GET['type'])){
    switch ($_GET['type']){
        case 'C':
            try{
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

                $req = $DB->prepare("INSERT INTO structure_fichier (code_structure_fichier, desc_structure_fichier, pos_taxe_engagement, pos_beneficiaire, pos_ref_beneficiaire, pos_montant_engagement, pos_num_engagement, pos_motif_engagement, 
                                                           pos_date_echeance, pos_code_budget, pos_num_bon_commande, pos_date_engagement, pos_ref_marche, pos_retenue_engagement)
                                               VALUES (:tcode, :tdescription, :tpos_taxe, :tpos_benef, :tpos_ref_benef, :tpos_montant, :tpos_num, :tpos_motif, :tpos_echeance, :tpos_budget, :tpos_num_bon, :tpos_date, :tpos_marche, :tpos_retenue)");
                $req->execute($t);

                ///////////// Audit système ///////////////////
                $tnew = array(
                    'tcode' => secure($obj->code),
                    'tlibelle' => secure($obj->description),
                );
                $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                    'tid' => "",
                    'tnom' => "",
                    'tip' => $ip,
                    'tmachine' => $machine,
                    'taction' => "Création",
                    'tdescription' => "Création structure d'importation",
                    'tissue' => "1",
                );
                audit_sys($t2, $tnew,$jeton);
                ///////////// Audit système ///////////////////

                $infoHttp = [
                    "reponse" => "success",
                    "infos" => "Enregistré avec succès.",
                ];
            }catch (PDOException $e)
            {
                ///////////// Audit système ///////////////////
                $tnew = array(
                    'tcode' => secure($obj->code),
                    'tlibelle' => secure($obj->description),
                );
                $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                    'tid' => "",
                    'tnom' => "",
                    'tip' => $ip,
                    'tmachine' => $machine,
                    'taction' => "Création",
                    'tdescription' => "Création structure d'importation",
                    'tissue' => "0",
                );
                audit_sys($t2, $tnew,$jeton);
                ///////////// Audit système ///////////////////

                $MYSQL_DUPLICATE_CODES=array(1062, 23000);

                if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES)) {
                    // duplicate entry, do something else
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Code structure déjà utilisé, veuillez en saisir un autre.",
                    ];
                } else {
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Enregistrement des données impossible.",
                    ];
                }
            }
            break;
        case 'R':
            if(isset($_GET['id']))
            {
                try{
                    $id = secure(($_GET['id']));
                    $sql = "SELECT idstructure_fichier as id, code_structure_fichier as code, desc_structure_fichier as description, pos_taxe_engagement as pos_taxe, pos_num_engagement as pos_num,
                                   pos_beneficiaire as pos_benef, pos_ref_beneficiaire as pos_ref_benef, pos_montant_engagement as pos_montant, pos_num_bon_commande as pos_num_bon,
                                   pos_motif_engagement as pos_motif, pos_date_echeance as pos_echeance, pos_code_budget as pos_budget, 
                                   pos_date_engagement as pos_date, pos_ref_marche as pos_marche, pos_retenue_engagement as pos_retenue
                            FROM structure_fichier
                            WHERE idstructure_fichier = $id";
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
                try{
                    $sql = "SELECT idstructure_fichier as id, code_structure_fichier as code, desc_structure_fichier as description, pos_taxe_engagement as pos_taxe, pos_num_engagement as pos_num,
                                   pos_beneficiaire as pos_benef, pos_ref_beneficiaire as pos_ref_benef, pos_montant_engagement as pos_montant, pos_num_bon_commande as pos_num_bon,
                                   pos_motif_engagement as pos_motif, pos_date_echeance as pos_echeance, pos_code_budget as pos_budget, pos_date_engagement as pos_date, pos_ref_marche as pos_marche, pos_retenue_engagement as pos_retenue
                            FROM structure_fichier
                            ORDER BY code_structure_fichier";
                    $req = $DB->query($sql);
                    $row_structure = $req->fetchAll(PDO::FETCH_OBJ);
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row_structure,
                    ];
                }catch (PDOException $e)
                {
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Chargement des données impossible.",
                    ];
                }
            }
            break;
        case 'U': // MAJ
            if(isset($obj->id) && !empty($obj->id))
            {
                try{
                    $t = array
                    (
                        'tid' => secure($obj->id),
                        'tcode' => secure($obj->code),
                        'tdescription' => secure($obj->description),
                        'tpos_taxe' => secure($obj->pos_taxe),
                        'tpos_benef' => secure($obj->pos_benef),
                        'tpos_ref_benef' => secure($obj->pos_ref_benef),
                        'tpos_montant' => secure($obj->pos_montant),
                        'tpos_num' => secure($obj->pos_num),
                        'tpos_num_bon' => secure($obj->pos_num_bon),
                        'tpos_motif' => secure($obj->pos_motif),
                        'tpos_echeance' => secure($obj->pos_echeance),
                        'tpos_budget' => secure($obj->pos_budget),
                        'tpos_date' => secure($obj->pos_date),
                        'tpos_marche' => secure($obj->pos_marche),
                        'tpos_retenue' => secure($obj->pos_retenue),
                    );

                    $req = $DB->prepare("UPDATE structure_fichier 
                                                   SET code_structure_fichier = :tcode, desc_structure_fichier = :tdescription, pos_taxe_engagement = :tpos_taxe,  pos_num_engagement = :tpos_num,
                                                           pos_beneficiaire = :tpos_benef, pos_ref_beneficiaire = :tpos_ref_benef, pos_montant_engagement = :tpos_montant, pos_num_bon_commande = :tpos_num_bon,
                                                           pos_motif_engagement = :tpos_motif, pos_date_echeance = :tpos_echeance, pos_code_budget = :tpos_budget,  
                                                           pos_date_engagement = :tpos_date, pos_ref_marche = :tpos_marche, pos_retenue_engagement = :tpos_retenue
                                                    WHERE idstructure_fichier = :tid");

                    $req->execute($t);

                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => secure($obj->code),
                        'tlibelle' => secure($obj->description),
                    );
                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => "Modification",
                        'tdescription' => "Modification structure d'importation",
                        'tissue' => "1",
                    );
                    audit_sys($t2, $tnew,$jeton);
                    ///////////// Audit système ///////////////////

                    $infoHttp = [
                        "reponse" => "success",
                        "message" => "Modifiée avec succès.",
                    ];
                }catch (PDOException $e)
                {
                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tcode' => secure($obj->code),
                        'tlibelle' => secure($obj->description),
                    );
                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => "Modification",
                        'tdescription' => "Modification structure d'importation",
                        'tissue' => "0",
                    );
                    audit_sys($t2, $tnew,$jeton);
                    ///////////// Audit système ///////////////////


                    $MYSQL_DUPLICATE_CODES=array(1062, 23000);

                    if (in_array($e->getCode(),$MYSQL_DUPLICATE_CODES))
                    {
                        // duplicate entry, do something else
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Code structure déjà utilisé, veuillez en saisir un autre.",
                        ];
                        // Audit
                    } else {
                        // Audit
                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Mise à jour des données impossible.",
                        ];
                    }
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
            if(isset($_GET['id']) && !empty($_GET['id']))
            {
                try{
                    $id = secure($_GET['id']);
                    // modifier dans la BDD
                    $t1 = array(
                        'tid' => $id
                    );
                    $req1 = $DB->prepare("DELETE FROM structure_fichier WHERE idstructure_fichier = :tid");
                    $req1->execute($t1);

                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tid' => $id
                    );
                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => "Suppression",
                        'tdescription' => "Suppression structure d'importation",
                        'tissue' => "1",
                    );
                    audit_sys($t2, $tnew,$jeton);
                    ///////////// Audit système ///////////////////

                    $infoHttp = [
                        "reponse" => "success",
                        "message" => "supprimé avec succès.",
                    ];

                }catch (PDOException $e)
                {
                    ///////////// Audit système ///////////////////
                    $tnew = array(
                        'tid' => $id
                    );
                    $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                        'tid' => "",
                        'tnom' => "",
                        'tip' => $ip,
                        'tmachine' => $machine,
                        'taction' => "Suppression",
                        'tdescription' => "Suppression structure d'importation",
                        'tissue' => "1",
                    );
                    audit_sys($t2, $tnew,$jeton);
                    ///////////// Audit système ///////////////////

                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "suppression impossible.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "suppression impossible.",
                ];
            }
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

$infoHttp[ "debug"] =  
        json_encode($obj, JSON_UNESCAPED_UNICODE) ;
 //var_dump($infoHttp);

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);?>