<?php
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/DateFormat.php';
require_once '../../fonctions/getToken.php';
require '../../vendor/autoload.php';
//header('Content-Type: application/json; charset=utf8');
header('Access-Control-Allow-origin: *');
header('Access-Control-Allow-Headers: *');
//header('Content-Type: multipart/form-data');
$obj = json_decode(file_get_contents('php://input'));

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

            $ecritureTotal = 0;
            $ecritureImport = 0;
            $ecritureRejet = 0;

        
            $t = array(
                'tfile' => time()
            );
   if(!empty($obj))  {
    try {
        //code...
        // Cr�ation de l'importation
                $req = $DB->prepare("INSERT INTO import (CHEMIN_IMPORT) VALUES (:tfile)");
                $req->execute($t);
                $idImport = $DB->lastInsertId();
                $req->closeCursor();
    
                foreach ($obj as $key => $value) {
                    $ligne = $key +2;
        # code...
        $ecritureTotal++; // increment nbr de lignes
        if(isset($value->date))  $ldate = convert_date_excel($value->date); else $ldate = date("Y-m-d" );
        if(isset($value->pos_budget))   $lbudget = $value->pos_budget; else $lbudget ="";
        if(isset($value->pos_echeance))    $lcheance= convert_date_excel($value->pos_echeance) ; else $lcheance = date("Y-m-d");
        if(isset($value->pos_marche))   $lmarche = $value->pos_marche; else $lmarche ="";
        if(isset($value->pos_motif))   $lmotif = $value->pos_motif; else $lmotif ="";
        if(isset($value->pos_num))   $lnum = $value->pos_num; else $lnum ="";
        if(isset($value->pos_num_bon))   $lnum_bon = $value->pos_num_bon; else $lnum_bon ="";
        if(isset($value->pos_ref_benef))   $lref_benef = $value->pos_ref_benef; else $lref_benef ="";
        if(isset($value->pos_retenue))   $lretenue = $value->pos_retenue; else $lretenue ="";
        if(isset($value->pos_taxe))   $ltaxe = $value->pos_taxe; else $ltaxe ="";
    
    
    
            if(isset($value->pos_benef) && !empty($value->pos_benef)) {
                $lbeneficiaire = secure($value->pos_benef) ;
                if(isset($value->pos_montant) && !empty($value->pos_montant) && is_numeric($value->pos_montant) && floatval($value->pos_montant) > 0 ){
                    $lmontant = secure($value->pos_montant);
                    $t = array(
                        'tidImport' => $idImport,
                        'tidStatu' => 1,
                        'ttaxe' => $ltaxe,
                        'tnumEng' => $lnum,
                        'tbenef' => $lbeneficiaire,
                        'trefBenef' => $lref_benef,
                        'tmont' => $lmontant,
                        'tnumBon' => $lnum_bon,
                        'tmotif' => $lmotif,
                        'tdateEch' => $lcheance,
                        'tbudget' => $lbudget,
                        'tdateEngag' => $ldate,
                        'trefMarche' => $lmarche,
                        'tretenu' => $lretenue,
                        'ttypeImport' => "Automatique",
                        'tuser' => $user,
                    );
                    $ecritureImport++;
                   // Cr�ation de l'importation
                   $req = $DB->prepare("INSERT INTO engagement (IDIMPORT, ID_STATUT_ENGAGEMENT, TAXE, NUM_ENGAGEMENT, BENEFICIAIRE, REF_BENEFICIAIRE, MONTANT, NUM_BON, MOTIF, DATE_ECHEANCE, CODE_BUDGET, DATE_ENGAGEMENT, REF_MARCHE, RETENUE, TYPE_IMPORT, USER_IMPORT)
                   VALUES (:tidImport, :tidStatu, :ttaxe, :tnumEng, :tbenef, :trefBenef, :tmont, :tnumBon, :tmotif, :tdateEch, :tbudget, :tdateEngag, :trefMarche, :tretenu, :ttypeImport, :tuser)");
                    $req->execute($t);
                    $req->closeCursor();
                }else{
                    $ecritureRejet++;
                    // Rejet pour cause de montant incorrect
                    $t = array(
                        'tidImport' => $idImport,
                        'ttaxe' => $ltaxe,
                        'tnumEng' => $lnum,
                        'tbenef' => $lbeneficiaire,
                        'trefBenef' => $lref_benef,
                        'tmont' => "montant invalide",
                        'tnumBon' => $lnum_bon,
                        'tmotif' => $lmotif,
                        'tdateEch' => $lcheance,
                        'tbudget' => $lbudget,
                        'tdateEngag' => $ldate,
                        'trefMarche' => $lmarche,
                        'tretenu' => $lretenue,
                        'tmotifRejet' => "Montant incorrect Ligne: ".$ligne,
                    );
                    // Cr�ation de l'importation
                    $req = $DB->prepare("INSERT INTO rejet (IDIMPORT, TAXE, NUM_ENGAGEMENT, BENEFICIAIRE, REF_BENEFICIAIRE, MONTANT, NUM_BON, MOTIF, DATE_ECHEANCE, CODE_BUDGET, DATE_ENGAGEMENT, REF_MARCHE, RETENUE, MOTIF_REJET)
                                                   VALUES (:tidImport, :ttaxe, :tnumEng, :tbenef, :trefBenef, :tmont, :tnumBon, :tmotif, :tdateEch, :tbudget, :tdateEngag, :trefMarche, :tretenu, :tmotifRejet)");
                    $req->execute($t);
                    $req->closeCursor();
                }
        }else{
            $ecritureRejet++;
            // rejet pour cause de b�n�ficiaire vide
            $t = array(
                'tidImport' => $idImport,
                'ttaxe' => $ltaxe,
                'tnumEng' => $lnum,
                'tbenef' => "bénéficiaire invalide",
                'trefBenef' => $lref_benef,
                'tmont' => "",
                'tnumBon' => $lnum_bon,
                'tmotif' => $lmotif,
                'tdateEch' => $lcheance,
                'tbudget' => $lbudget,
                'tdateEngag' => $ldate,
                'trefMarche' => $lmarche,
                'tretenu' => $lretenue,
                'tmotifRejet' => "Bénéficiaire introuvable Ligne ".$ligne,
            );
            // Cr�ation de l'importation
            $req = $DB->prepare("INSERT INTO rejet (IDIMPORT, TAXE, NUM_ENGAGEMENT, BENEFICIAIRE, REF_BENEFICIAIRE, MONTANT, NUM_BON, MOTIF, DATE_ECHEANCE, CODE_BUDGET, DATE_ENGAGEMENT, REF_MARCHE, RETENUE, MOTIF_REJET)
                                               VALUES (:tidImport, :ttaxe, :tnumEng, :tbenef, :trefBenef, :tmont, :tnumBon, :tmotif, :tdateEch, :tbudget, :tdateEngag, :trefMarche, :tretenu, :tmotifRejet)");
            $req->execute($t);
            $req->closeCursor();
        }
    
    
    
    }
        // Mise � jour de l'importation
        $t = array(
            'tidImport' => $idImport,
            'tecritureTotal' => $ecritureTotal,
            'tecritureRejet' => $ecritureRejet,
            'tecritureImport' => $ecritureImport,
        );
        // Cr�ation de l'importation
        $req = $DB->prepare("UPDATE import SET TOTAL_IMPORT = :tecritureImport, TOTAL_REJET = :tecritureRejet, TOTAL_ECRITURE = :tecritureTotal
                                       WHERE IDIMPORT = :tidImport");
        $req->execute($t);
        $req->closeCursor();
        $stat = array("total" => $ecritureTotal, "imports" => $ecritureImport, "rejets" => $ecritureRejet);
                $infoHttp = [
                    "reponse" => "success",
                    "infos" => $stat,
                    "message" => "Importation terminée.",
                ];
    } catch (\Throwable $th) {
        //throw $th;
        $infoHttp = [
            "reponse" => "error",
            "message" => "Impossible d'importer le fichier. \n Veuillez reprendre l'importation.".$th,
        ];
    }
                
    
   } 
   else 
   {
    $infoHttp = [
        "reponse" => "error",
        "message" => "Impossible d'importer le fichier!!",
    ];
   }      

            // Importation des donn�es

 
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
?>