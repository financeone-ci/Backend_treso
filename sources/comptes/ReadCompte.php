<?php
// Affiche la liste des comptes en tenant compte de l'appartenance à un ou plusieurs sites**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

$infoHttp = array();
$header = apache_request_headers();

if(isset($header['Authorization']) && ChekToken($header['Authorization']) == true)
{
    // Récupération de la société
    $jeton = $header['Authorization'];
    $payload = tokenData($jeton);
    $societe = $payload->user_societe;
    $user = $payload->user_id;

    // Sélectionner un compte
    if(isset($_GET['id']) && !empty($_GET['id']))
    {
        $IDcpte = secure($_GET['id']);
        $sql = "SELECT ID_COMPTE as id, CODE_COMPTE, SOLDE_INITIAL_COMPTE, COMPTE_COMPTABLE, RIB, LIBELLE_COMPTE, GESTIONNAIRE_COMPTE, CIV_GESTIONNAIRE_COMPTE, SERVICE_GESTIONNAIRE_COMPTE, TEL_GESTIONNAIRE_COMPTE, EMAIL_GESTIONNAIRE_COMPTE, banque.IDBANQUE as banq, ID_DEVISE, CODE_BANQUE, CODE_DEVISE
                FROM compte
                    JOIN banque ON compte.IDBANQUE = banque.IDBANQUE
                    JOIN devise ON compte.ID_DEVISE = devise.IDDEVISE
                WHERE ID_COMPTE = '$IDcpte'";
    }else{
        // Déterminer si l'utilisateur appartient à un site
        $sqlSite = "SELECT ID_SITE FROM site_user WHERE ID_USER = '$user'";
        $reqSite = $DB->query($sqlSite);
        if($reqSite->rowCount() > 0)
        {// l'utilisateur appartient à un site
            $sql = "SELECT ID_COMPTE as id, CODE_COMPTE, SOLDE_INITIAL_COMPTE, COMPTE_COMPTABLE, RIB, LIBELLE_COMPTE, GESTIONNAIRE_COMPTE, CIV_GESTIONNAIRE_COMPTE, SERVICE_GESTIONNAIRE_COMPTE, TEL_GESTIONNAIRE_COMPTE, EMAIL_GESTIONNAIRE_COMPTE, banque.IDBANQUE as banq, ID_DEVISE, CODE_BANQUE, CODE_DEVISE
                    FROM compte
                        JOIN banque ON compte.IDBANQUE = banque.IDBANQUE
                        JOIN devise ON compte.ID_DEVISE = devise.IDDEVISE
                    WHERE ID_COMPTE IN (SELECT ID_COMPTE FROM site_compte WHERE ID_SITE IN (SELECT ID_SITE FROM site_user WHERE ID_USER = '$user'))";
        }else{
            // L'utilisateur n'appartien à aucun compte
            $sql = "SELECT ID_COMPTE as id, CODE_COMPTE, SOLDE_INITIAL_COMPTE, COMPTE_COMPTABLE, RIB, LIBELLE_COMPTE, GESTIONNAIRE_COMPTE, CIV_GESTIONNAIRE_COMPTE, SERVICE_GESTIONNAIRE_COMPTE, TEL_GESTIONNAIRE_COMPTE, EMAIL_GESTIONNAIRE_COMPTE, banque.IDBANQUE as banq, ID_DEVISE, CODE_BANQUE, CODE_DEVISE
                    FROM compte
                        JOIN banque ON compte.IDBANQUE = banque.IDBANQUE
                        JOIN devise ON compte.ID_DEVISE = devise.IDDEVISE
                    WHERE ID_SOCIETE = '$societe'";
        }     
    }
    try{
        $req = $DB->query($sql);
        $data = $req->fetchAll(PDO::FETCH_OBJ);
        $infoHttp = [
            "reponse" => "success",
            "infos" => $data,
        ];
    }catch(PDOException $e){
        $infoHttp = [
            "reponse" => "errors",
            "message" => "Connexion impossible : ".$e,
        ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Accès refusé",
    ]; 
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);