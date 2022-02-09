<?php

// Envoi de password par mail**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';

// Récupération des données postées dans $obj
$obj = json_decode(file_get_contents('php://input'));
$obj=$obj->values;

$infoHttp = array();

if(isset($obj) && !empty($obj))
{
    $pwd = sha1(secure($obj->pwd));
    // $pwd = password_verify(secure($obj->pwd), PASSWORD_DEFAULT);
    $user = secure($obj->user);

    $sql = "SELECT user_id, user_login, user_nom, user_prenom, user_email, user_tel, id_societe, user_role, user_new_connexion
            FROM user
                    JOIN profil ON user.profil_id = profil.profil_id
            WHERE user_login = '$user' AND user_pwd = '$pwd'";
    $req = $DB->query($sql);
    $count = $req->rowCount();
    if ($count > 0) {
        $d = $req->fetch();
        $newConnexion = $d['user_new_connexion'];
        $iduser = $d['user_id'];

        // Payload du JWT
        $payload = array(
            'user_id' => $d['user_id'],
            'user_login' => $d['user_login'],
            'user_nom' => $d['user_nom'],
            'user_prenom' => $d['user_prenom'],
            'user_email' => $d['user_email'],
            'user_tel' => $d['user_tel'],
            'user_societe' => $d['id_societe'],
            'user_role' => $d['user_role'],
            'issued_at' => date('Y-m-d H:i:s'),
            'exp' => time()+54000000,
        );
        
        // Récupérer les informations liées au droits de l'utilisateur
        $sql1 = "SELECT e_smenu_libelle, droits.e_smenu_id as esmenu_id, profil_libelle, e_smenu_lien, droits_lecture, droits_creer, droits_modifier, droits_supprimer, menu.menu_id as idmenu, smenu.smenu_id as idsmenu, smenu_libelle, menu_libelle
                FROM e_smenu JOIN droits ON e_smenu.e_smenu_id = droits.e_smenu_id
                            JOIN profil ON droits.profil_id = profil.profil_id
                            JOIN user ON profil.profil_id = user.profil_id
                            JOIN smenu ON e_smenu.smenu_id = smenu.smenu_id
                            JOIN menu ON smenu.menu_id = menu.menu_id
                WHERE user_id = '$iduser' AND droits.droits_lecture = 1";
        $req1 = $DB->query($sql1);
        $droit = $req1->fetchAll(PDO::FETCH_OBJ);

        // Récupérer les chemins du menu
        $sql2 = "SELECT e_smenu_libelle, e_smenu_lien, smenu_id
                 FROM e_smenu 
                 GROUP BY smenu_id";
        $req2 = $DB->query($sql2);
        $menu = $req2->fetchAll(PDO::FETCH_OBJ);

        // Audit
        $ip = $_SERVER['REMOTE_ADDR'];
        $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $t2 = array(
            'tid' => $d['user_id'],
            'tnom' => $d['user_nom'].' '.$d['user_prenom'],
            'tip' => $ip,
            'tmachine' => $machine,
        );
        $req2 = $DB->prepare("INSERT INTO `audit_cnx` (`audit_cnx_userid`, `audit_cnx_usernom`, `audit_cnx_ip`, `audit_cnx_machine`, `audit_cnx_action`) VALUES (:tid, :tnom, :tip, :tmachine, 'in')");
        $req2->execute($t2);

        // Génération du jeton JWT
        $jwt = getToken($key, $payload);
        $infoHttp = [
            "reponse" => "success",
            "message" => "",
            "jeton" => $jwt,
            "droit" => $droit,
            "newConnexion" => $newConnexion,
        ];
    }else{
        // MAJ Audit
        $ip = $_SERVER['REMOTE_ADDR'];
        $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $t2 = array(
            'tid' => "Undefined",
            'tnom' => $obj->user,
            'tip' => $ip,
            'tmachine' => $machine,
        );
        $req2 = $DB->prepare("INSERT INTO `audit_cnx` (`audit_cnx_userid`, `audit_cnx_usernom`, `audit_cnx_ip`, `audit_cnx_machine`, `audit_cnx_action`, `audit_cnx_issue`, audit_cnx_description) VALUES (:tid, :tnom, :tip, :tmachine, 'in', 0, 'Echec : Paramètres de connexion incorrects')");
        $req2->execute($t2);

        $infoHttp = [
            "reponse" => "error",
            "message" => "Paramètres de connexion incorrects",
        ];
    }
}else{
    $infoHttp = [
            "reponse" => "error",
            "message" => "Paramètres incorrects",
        ]; 
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);