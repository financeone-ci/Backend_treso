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
            try{
                $sql = "SELECT securite_id as id, securite_taille, securite_majuscule, securite_carc_speciaux, securite_chiffres, securite_duree_pwd, valider, autoriser, approuver FROM securite";
                $req = $DB->query($sql);
                $count = $req->rowCount();
                if($count == 0)
                {
                    // Initialisation de la table securite
                    $t1 = array(
                        'id' => 1,
                        'taille' => 8,
                        'maj' => false,
                        'carac' => false,
                        'chiffre' => false,
                        'duree' => 0,
                        'valider' => 0,
                        'autoriser' => 0,
                        'approuver' => 0,
                    );
                    $req1 = $DB->prepare("INSERT INTO securite (securite_id, securite_taille, securite_majuscule, securite_carc_speciaux, securite_chiffres, securite_duree_pwd, valider, autoriser, approuver)
                    VALUES (:id, :taille, :maj, :carac, :chiffre, :duree)");
                    if($req1->execute($t1))
                    {
                        // Relecture des paramètres de cnx
                        $sql2 = "SELECT securite_id as id, securite_taille, securite_majuscule, securite_carc_speciaux, securite_chiffres, securite_duree_pwd, valider,  autoriser, approuver FROM securite";
                        $req2 = $DB->query($sql2);
                        $row_securite = $req2->fetchAll(PDO::FETCH_OBJ);
                        $infoHttp = [
                            "reponse" => "success",
                            "infos" => $row_securite,
                        ];
                        $req1->closeCursor();
                        $req2->closeCursor();
                    }else{
                        $infoHttp = [
                            "reponse" => "success",
                            "infos" => "Initialisation paramètres impossible",
                        ];
                    }
                }else{
                    $row_securite = $req->fetchAll(PDO::FETCH_OBJ);
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row_securite,
                    ];
                }
                $req->closeCursor();

            }catch (PDOException $e)
            {
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Chargement des données impossible.",
                ];
            }
            break;
        case 'U': // MAJ
            if(isset($obj->id) && !empty($obj->id)){
                try{
                    $t1 = array(
                        'tid' => secure($obj->id),
                        'ttaille' => secure($obj->securite_taille),
                        'tmaj' => secure($obj->securite_majuscule),
                        'tcarac' => secure($obj->securite_carc_speciaux),
                        'tchiffre' => secure($obj->securite_chiffres),
                        'tduree' => secure($obj->securite_duree_pwd),
                        'tvalider' => secure($obj->valider),
                        'tautoriser' => secure($obj->autoriser),
                        'tapprouver' => secure($obj->approuver),
                    );
                    $req1 = $DB->prepare("UPDATE securite SET securite_taille = :ttaille, securite_majuscule = :tmaj, securite_carc_speciaux = :tcarac, securite_duree_pwd = :tduree, securite_chiffres = :tchiffre, valider = :tvalider, autoriser = :tautoriser, approuver = :tapprouver
                                                WHERE securite_id = :tid ");
                    $req1->execute($t1);
                    // cas où la requête s'est bien exécutée
                    $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                }catch (PDOException $e){
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Mise à jour des données impossible.",
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
        "message" => "Impossible de charger les données",
    ];
}
echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);?>