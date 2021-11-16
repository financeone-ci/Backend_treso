<?php
require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';
$obj = json_decode(file_get_contents('php://input'));
$infoHttp = array();

try{
    // Récupérer la liste des comptes
    $sql = "SELECT CODE_COMPTE, LIBELLE_COMPTE FROM compte ORDER BY CODE_COMPTE";
    $req = $DB->query($sql);
    $dataCompte = $req->fetchAll(PDO::FETCH_OBJ);
    $req->closeCursor();
    $TableCompte = array();
    foreach($dataCompte as $c)
    {
        // Compter le nombre de chèques non imprimés
        $cpte = $c->CODE_COMPTE;
        $sql = "SELECT IDPAIEMENT 
                FROM paiement 
                WHERE CODE_COMPTE = '$cpte' 
                    AND CODE_NATURE = 'CHQE' 
                    AND ID_STATUT_PAIEMENT = 4";
        $req = $DB->query($sql);
        $nbPaie = $req->rowCount();
        array_push($TableCompte, [
            "id" => $cpte,
            "LibCompte" => $c->LIBELLE_COMPTE,
            "nombre" => $nbPaie,
        ]);
    }
    $infoHttp = [
        "reponse" => "success",
        "infos" => $TableCompte,
    ];
}catch(PDOException $e){
    $infoHttp = [
        "reponse" => "error",
        "infos" => "Impossible de charger les données.",
    ];
}

// affichage en JSON
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
