<?php
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/DateFormat.php';
require_once '../../fonctions/getToken.php';
require '../../vendor/autoload.php';
header('Content-Type: multipart/form-data');

use PhpOffice\PhpSpreadsheet\Spreadsheet;

$obj = json_decode(file_get_contents('php://input'));
$infoHttp = array();
$tabExtension = array();
$tabExtension = array('xlsx', 'xls');

if(isset($_GET['jeton'])){
    $jeton = secure($_GET['jeton']);
}else{
    $jeton = "";
}

//if(isset($obj->fichier) && !empty($obj->fichier) && isset($obj->structure) && !empty($obj->structure))
//if(isset($_POST['submit']))
if(1 == 1)
{
    // Upload du fichier Excel
    $struc = $obj->structure;
    $file = $obj->fichier->name;
//    $file = basename($file->name);

    // Controle de l'extension
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    if(!in_array($extension, ['Xlsx', 'Xls']))
    {
        // Upload
        move_uploaded_file($file, '../../fichiers/');
    }else
        {
        $infoHttp = [
            "reponse" => "error",
            "message" => "Format de fichier non supporté.",
        ];
    }

    // Récupérer l'extension du fichier
    $exten = new SplFileInfo($file);

    // Vérifier si le fichier est un fichier Excel
    if(in_array($extension,$tabExtension))
    {
        $ecritureTotal = 0;
        $ecritureImport = 0;
        $ecritureRejet = 0;

        try{
            $t = array(
                'tfile' => $file
            );
            // Création de l'importation
            $req = $DB->prepare("INSERT INTO import (CHEMIN_IMPORT) VALUES (:tfile)");
            $req->execute($t);
            $idImport = $DB->lastInsertId();
            $req->closeCursor();

            // Récupérer les positions de la structure
            $sql = "SELECT * FROM structure_fichier WHERE idstructure_fichier = $struc";
            $req = $DB->query($sql);
            $data = $req->fetch(PDO::FETCH_UNIQUE);
            $req->closeCursor();

            // Importation des données

            // Ouverture du fichier
            /**  identifier le type de fichier  **/
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file);
            /**  Créer un lecteur pour le type de fichier identifié  **/
            if($extension == "xlsx"){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            if($extension == "xls"){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }

            $reader->setReadDataOnly(true);
            /**  chargement du fichier  **/
            $spreadsheet = $reader->load($file);
            /**  se positionner sur la page acive  **/
            $worksheet = $spreadsheet->getActiveSheet();
            $nbLigne = $worksheet->getHighestRow();

            $i = 1;
            // Voire si le fichier contien une entête
            if(isset($_POST['header']) && !empty($_POST['header']) && intval($_POST['header']) == 1){
                $i = 2;
            }

            // boucler les données contenu dans le fichier et les lire
            for ($row = $i; $row <= $nbLigne; ++$row)
            {
                $ecritureTotal++;

                $taxe = trim($worksheet->getCellByColumnAndRow($data['pos_taxe_engagement'],$row)->getValue());
                $numEng = trim($worksheet->getCellByColumnAndRow($data['pos_num_engagement'],$row)->getValue());
                $benef = trim($worksheet->getCellByColumnAndRow($data['pos_beneficiaire'],$row)->getValue());
                $refBenef = trim($worksheet->getCellByColumnAndRow($data['pos_ref_beneficiaire'],$row)->getValue());
                $mont = trim($worksheet->getCellByColumnAndRow($data['pos_montant_engagement'],$row)->getValue());
                $numBon = trim($worksheet->getCellByColumnAndRow($data['pos_num_bon_commande'],$row)->getValue());
                $motif = trim($worksheet->getCellByColumnAndRow($data['pos_motif_engagement'],$row)->getValue());
                $dateEch = trim($worksheet->getCellByColumnAndRow($data['pos_date_echeance'],$row)->getValue());
                $budget = trim($worksheet->getCellByColumnAndRow($data['pos_code_budget'],$row)->getValue());
                $dateEngag = trim($worksheet->getCellByColumnAndRow($data['pos_date_engagement'],$row)->getValue());
                $refMarche = trim($worksheet->getCellByColumnAndRow($data['pos_ref_marche'],$row)->getValue());
                $retenu = trim($worksheet->getCellByColumnAndRow($data['pos_retenue_engagement'],$row)->getValue());

                // traitement et correction des dates
                if($dateEch == "")
                {
                    $dateEch = date("Y-m-d");
                }else{
                    $dateEch = DateFormat($dateEch);
                }
                if($dateEngag == "")
                {
                    $dateEngag = date("Y-m-d");
                }else{
                    $dateEngag = DateFormat($dateEngag);
                }

                // Controle de l'existantce du libellé du beneficiaire
                if($benef <> "")
                {
                    // Controle du montant
                    if($mont <> "" && is_numeric($mont) && floatval($mont) > 0)
                    {
                        // Récupération de l'utilisateur
                        if(!empty($jeton)) {
                            $decoded = JWT::decode($jeton, $key, array('HS256'));
                            $user = $decoded->user_nom . ' ' . $decoded->user_prenom;
                        }else{
                            $user = "utilisateur introuvable";
                        }

                        // Importation de l'engagement
                        $ecritureImport++;
                        $t = array(
                            'tidImport' => $idImport,
                            'tidStatu' => 1,
                            'ttaxe' => $taxe,
                            'tnumEng' => $numEng,
                            'tbenef' => $benef,
                            'trefBenef' => $refBenef,
                            'tmont' => $mont,
                            'tnumBon' => $numBon,
                            'tmotif' => $motif,
                            'tdateEch' => $dateEch,
                            'tbudget' => $budget,
                            'tdateEngag' => $dateEngag,
                            'trefMarche' => $refMarche,
                            'tretenu' => $retenu,
                            'ttypeImport' => "Automatique",
                            'tuser' => $user,
                        );
                        // Création de l'importation
                        $req = $DB->prepare("INSERT INTO engagement (IDIMPORT, ID_STATUT_ENGAGEMENT, TAXE, NUM_ENGAGEMENT, BENEFICIAIRE, REF_BENEFICIAIRE, MONTANT, NUM_BON, MOTIF, DATE_ECHEANCE, CODE_BUDGET, DATE_ENGAGEMENT, REF_MARCHE, RETENUE, TYPE_IMPORT, USER_IMPORT)
                                                       VALUES (:tidImport, :tidStatu, :ttaxe, :tnumEng, :tbenef, :trefBenef, :tmont, :tnumBon, :tmotif, :tdateEch, :tbudget, :tdateEngag, :trefMarche, :tretenu, :ttypeImport, :tuser)");
                        $req->execute($t);
                        $req->closeCursor();
                    }else{
                        $ecritureRejet++;
                        // Rejet pour cause de montant incorrect
                        $t = array(
                            'tidImport' => $idImport,
                            'ttaxe' => $taxe,
                            'tnumEng' => $numEng,
                            'tbenef' => $benef,
                            'trefBenef' => $refBenef,
                            'tmont' => $mont,
                            'tnumBon' => $numBon,
                            'tmotif' => $motif,
                            'tdateEch' => $dateEch,
                            'tbudget' => $budget,
                            'tdateEngag' => $dateEngag,
                            'trefMarche' => $refMarche,
                            'tretenu' => $retenu,
                            'tmotifRejet' => "Montant incorrect.",
                        );
                        // Création de l'importation
                        $req = $DB->prepare("INSERT INTO rejet (IDIMPORT, TAXE, NUM_ENGAGEMENT, BENEFICIAIRE, REF_BENEFICIAIRE, MONTANT, NUM_BON, MOTIF, DATE_ECHEANCE, CODE_BUDGET, DATE_ENGAGEMENT, REF_MARCHE, RETENUE, MOTIF_REJET)
                                                       VALUES (:tidImport, :ttaxe, :tnumEng, :tbenef, :trefBenef, :tmont, :tnumBon, :tmotif, :tdateEch, :tbudget, :tdateEngag, :trefMarche, :tretenu, :tmotifRejet)");
                        $req->execute($t);
                        $req->closeCursor();
                    }
                }else{
                    $ecritureRejet++;
                    // rejet pour cause de bénéficiaire vide
                    $t = array(
                        'tidImport' => $idImport,
                        'ttaxe' => $taxe,
                        'tnumEng' => $numEng,
                        'tbenef' => $benef,
                        'trefBenef' => $refBenef,
                        'tmont' => $mont,
                        'tnumBon' => $numBon,
                        'tmotif' => $motif,
                        'tdateEch' => $dateEch,
                        'tbudget' => $budget,
                        'tdateEngag' => $dateEngag,
                        'trefMarche' => $refMarche,
                        'tretenu' => $retenu,
                        'tmotifRejet' => "Bénéficiaire introuvable.",
                    );
                    // Création de l'importation
                    $req = $DB->prepare("INSERT INTO rejet (IDIMPORT, TAXE, NUM_ENGAGEMENT, BENEFICIAIRE, REF_BENEFICIAIRE, MONTANT, NUM_BON, MOTIF, DATE_ECHEANCE, CODE_BUDGET, DATE_ENGAGEMENT, REF_MARCHE, RETENUE, MOTIF_REJET)
                                                       VALUES (:tidImport, :ttaxe, :tnumEng, :tbenef, :trefBenef, :tmont, :tnumBon, :tmotif, :tdateEch, :tbudget, :tdateEngag, :trefMarche, :tretenu, :tmotifRejet)");
                    $req->execute($t);
                    $req->closeCursor();
                }

                // Objet statistique
//                $infoHttp = ['total' => $ecritureTotal, 'imports' => $ecritureImport, 'rejets' => $ecritureRejet];
            }

            // Mise à jour de l'importation
            $t = array(
                'tidImport' => $idImport,
                'tecritureTotal' => $ecritureTotal,
                'tecritureRejet' => $ecritureRejet,
                'tecritureImport' => $ecritureImport,
            );
            // Création de l'importation
            $req = $DB->prepare("UPDATE import SET TOTAL_IMPORT = :tecritureImport, TOTAL_REJET = :tecritureRejet, TOTAL_ECRITURE = :tecritureTotal
                                           WHERE IDIMPORT = :tidImport");
            $req->execute($t);
            $req->closeCursor();

            //************************************AUDIT***********************************
            //************************************AUDIT***********************************

            $stat = array("total" => $ecritureTotal, "imports" => $ecritureImport, "rejets" => $ecritureRejet);
            $infoHttp = [
                "reponse" => "success",
                "infos" => $stat,
                "message" => "Importation terminée.",
            ];
        }catch (PDOException $e){
            $infoHttp = [
                "reponse" => "error",
                "message" => "Impossible d'importer le fichier. \n Veuillez reprendre l'importation.",
            ];
        }
    }else{
        $infoHttp = [
            "reponse" => "error",
            "message" => "Extension de fichier non supportée. \n Veuillez vérifier que le fichier $file est au format Excel.",
        ];
    }
    // Importation des lignes
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "connexion impossible...",
    ];
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
?>