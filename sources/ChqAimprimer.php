<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
$obj = json_decode(file_get_contents('php://input'));
$infoHttp = array();

try{
    // Récupérer les chèques non imprimés du compte
    if(isset($_GET['cpte']) && !empty($_GET['cpte']))
    {
        $cpte = $_GET['cpte'];
        $sql = "SELECT IDPAIEMENT as id, DATE_PAIEMENT, MONTANT_PAIEMENT, BENEFICIAIRE_PAIEMENT, MOTIF_PAIEMENT, CODE_BUDGET_PAIE, DEVISE
                FROM paiement
                WHERE CODE_COMPTE = '$cpte' 
                    AND CODE_NATURE = 'CHQE' 
                    AND ID_STATUT_PAIEMENT = 4";
        $req = $DB->query($sql);
        $dataCompte = $req->fetchAll(PDO::FETCH_OBJ);
        $req->closeCursor();
        foreach ($dataCompte as $key=>$item) {
            # découpage du montant
            $dataCompte[$key]->MONTANT_PAIEMENT2 = number_format($item->MONTANT_PAIEMENT, 0, "", ".")   ;
            $dataCompte[$key]->entier = intval($item->MONTANT_PAIEMENT)   ;
            $dataCompte[$key]->date_fr = date("d-m-Y", strtotime($item->DATE_PAIEMENT))   ;
            $dataCompte[$key]->date_impr = date("d-m-Y")   ;
            $dataCompte[$key]->decimal = (int) substr($item->MONTANT_PAIEMENT, strrpos($item->MONTANT_PAIEMENT, '.')+1) ;
            if($dataCompte[$key]->decimal == "00") $dataCompte[$key]->decimal = null;
             
        }
       
        $infoHttp = [
            "reponse" => "success",
            "infos" => $dataCompte,
        ];  
    }else{
        $infoHttp = [
            "reponse" => "success",
            "infos" => "Paramètres incorrects.",
        ]; 
    }
}catch(PDOException $e)
{
    $infoHttp = [
        "reponse" => "error",
        "infos" => "Impossible de charger les données.",
    ];
}

// affichage en JSON
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);