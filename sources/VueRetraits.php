<?php
// AFFICHAGE DES RETRAITS DE PAIEMENT
require_once '../fonctions/StatusPaiements.php';
require_once '../fonctions/getToken.php';

$obj = json_decode(file_get_contents('php://input'));

$infoHttp = array();
if(isset($_GET['jeton']))
{
    $jeton = secure($_GET['jeton']);
}else{
    $jeton = "";
}

if(isset($_GET['debut']) && !empty($_GET['debut']) && isset($_GET['fin']) && !empty($_GET['fin'])){
    $dateDeb = secure($_GET['debut']);
    $dateFin = secure($_GET['fin']);
    $etat = $_GET['etat'];
    try
    {
        if($etat == 'true')
        {
            $sql = "SELECT IDPAIEMENT as id, BENEFICIAIRE_PAIEMENT, DATE_FORMAT(DATE_IMPRESSION, '%d-%m-%Y') as DATE_IMP,  DATE_FORMAT(DATE_REMISE, '%d-%m-%Y') as DATE_REMISE ,MONTANT_PAIEMENT, MOTIF_PAIEMENT, CODE_NATURE, CODE_COMPTE
            FROM paiement
            WHERE paiement.DATE_REMISE BETWEEN '$dateDeb' AND '$dateFin'
                AND paiement.BENEF_REMISE IS NOT NULL 
                AND paiement.CODE_NATURE <> 'VIRE'";
        }else{
            $sql = "SELECT IDPAIEMENT as id, BENEFICIAIRE_PAIEMENT, DATE_FORMAT(DATE_IMPRESSION, '%d-%m-%Y') as DATE_IMP, DATE_FORMAT(DATE_REMISE, '%d-%m-%Y') as DATE_REMISE, MONTANT_PAIEMENT, MOTIF_PAIEMENT, CODE_NATURE, CODE_COMPTE
            FROM paiement
            WHERE paiement.DATE_IMPRESSION BETWEEN '$dateDeb' AND '$dateFin'
                    AND paiement.BENEF_REMISE IS NULL 
                    AND paiement.CODE_NATURE <> 'VIRE'";
        }
        $req = $DB->query($sql);
        $row_paiement = $req->fetchAll(PDO::FETCH_OBJ);
        $infoHttp = [
            "reponse" => "success",
            "infos" => $row_paiement,
        ];
    }catch (PDOException $e)
    {
        $infoHttp = [
            "reponse" => "error",
            "message" => "Connexion aux données impossible.",
        ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "paramètres incorrects",
    ];
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);