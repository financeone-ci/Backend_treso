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
        case 'C': // creation
            if(
                isset($obj->login) && !empty($obj->login)
                    
                    ){
                 
                $login = secure($obj->login);
                $email = secure($obj->email);
                $role = secure($obj->role);
                $actif = secure($obj->actif);
                $nom = secure($obj->nom);
                $prenom = secure($obj->prenom);
                $profil = secure($obj->profil);
                if(isset($obj->tel) )
                $tel = secure($obj->tel);
                else $tel ="";
                // modifier dans la BDD
                $t1 = array(
                    'tlogin' => $login,
                    'tnom' => $nom,
                    'tprenom' => $prenom,
                    'tactif' => $actif,
                    'temail' => $email,
                    'trole' => $role,
                    'tprofil' => $profil,
                    'ttel' => $tel,
                     'tpwd' => sha1("user"),
                    //  'tpwd' => password_hash("user", PASSWORD_DEFAULT),
                    ///////////// 
                );
               
                $req1 = $DB->prepare("INSERT INTO `user` (`user_nom`, `user_prenom`, `user_login`, `user_pwd`, `user_email`, `user_tel`, `user_role`, `user_actif`, `profil_id`) VALUES (:tnom, :tprenom, :tlogin, :tpwd, :temail, :ttel, :trole, :tactif, :tprofil)");
                if($req1->execute($t1)){
// cas où la requête s'est bien exécutée
/////////////////////////////////////////


 ///////////// Audit système ///////////////////
                   $tnew = array( // tableau des nouvelles valeurs
                    'tlogin' => $login,
                    'tnom' => $nom,
                    'tprenom' => $prenom,
                    'tactif' => $actif,
                    'temail' => $email,
                    'trole' => $role,
                    'tprofil' => $profil,
                    'ttel' => $tel,
                );
                       
                        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
                            'tid' => "",
                            'tnom' => "",
                            'tip' => $ip,
                            'tmachine' => $machine,
                            'taction' => 'Création utilisateur',
                            'tdescription' => "",
                            'tissue' => "1",
                                      );
                  audit_sys($t2, $tnew,$jeton);
                     ///////////// Audit système ///////////////////
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                }else{
                    
///////////// Audit système ///////////////////
$tnew = array( // tableau des nouvelles valeurs
    'tlogin' => $login,
    'tnom' => $nom,
    'tprenom' => $prenom,
    'tactif' => $actif,
    'temail' => $email,
    'trole' => $role,
    'tprofil' => $profil,
    'ttel' => $tel,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'Création utilisateur',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
     ///////////// Audit système ///////////////////
              
                    $infoHttp = [
                        "reponse" => "error",
                        "message" =>"Impossible",
                    ];
                }
            }else{
                  
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "veuillez vérifier votre connexion",
                ];
            }
            break;
        case 'R':
            if(isset($_GET['read_all']))
            {
                try{
                    $sql = "SELECT `user_id` as id,`user_login`,`user_email`,`user_tel`,`user_role`,`profil_libelle`, profil.profil_id as id_profil, user_nom, user_prenom, user_actif FROM `user` JOIN profil ON user.profil_id = profil.profil_id ORDER BY user_login";
                    $req = $DB->query($sql);
                    $row_user = $req->fetchAll(PDO::FETCH_OBJ);
                    
                    $infoHttp = [
                        "reponse" => "success",
                        "infos" => $row_user,
                    ];
                }catch (PDOException $e)
                {
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion impossible",
                    ];
                }
            }else
            // connexion des utilisateurs
            if(isset($obj->user) && !empty($obj->user) && isset($obj->pwd) && !empty($obj->pwd))
            {
                $pwd = sha1(secure($obj->pwd));
                // $pwd = password_verify(secure($obj->pwd), PASSWORD_DEFAULT);
                $user = secure($obj->user);
                try{
                    $sql = "SELECT user_id, user_login, user_nom, user_prenom, user_email, user_tel, id_societe, user_role
                            FROM user
                                 JOIN profil ON user.profil_id = profil.profil_id
                            WHERE user_login = '$user' AND user_pwd = '$pwd'";
                    $req = $DB->query($sql);
                    $count = $req->rowCount();
                    if ($count > 0) {
                        $d = $req->fetch();
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
                        $newConnexion = $d['user_new_connexion'];

                        $iduser = $d['user_id'];
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

                         // MAJ Audit
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
                        $req2 = $DB->prepare("INSERT INTO `audit_cnx` (`audit_cnx_userid`, `audit_cnx_usernom`, `audit_cnx_ip`, `audit_cnx_machine`, `audit_cnx_action`, `audit_cnx_issue`, audit_cnx_description) VALUES (:tid, :tnom, :tip, :tmachine, 'in', 0, 'Paramètres de connexion incorrects')");
                        $req2->execute($t2);

                        $infoHttp = [
                            "reponse" => "error",
                            "message" => "Paramètres de connexion incorrects",
                            "jeton" => false,
                        ];
                    }
                }catch (PDOException $e){
                     // MAJ Audit
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                    $t2 = array(
                        'tid' => "Undefined",
                        'tnom' => $obj->user,
                        'tip' => $ip,
                        'tmachine' => $machine,
                    );
                    $req2 = $DB->prepare("INSERT INTO `audit_cnx` (`audit_cnx_userid`, `audit_cnx_usernom`, `audit_cnx_ip`, `audit_cnx_machine`, `audit_cnx_action`, `audit_cnx_issue`, audit_cnx_description) VALUES (:tid, :tnom, :tip, :tmachine, 'in', 0, '".$e."')");
                    $req2->execute($t2);

                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                        "jeton" => false,
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres de connexion incorrects",
                    "jeton" => false,
                ];
            }
            break;
        case 'U': // MAJ
            if(
                isset($obj->login) && !empty($obj->login)
                 && isset($obj->id) && !empty($obj->id) 
                 && isset($obj->email) && !empty($obj->email) 
                 && isset($obj->role) && !empty($obj->role)
                  && isset($obj->tel) 
                   && isset($obj->profil) && !empty($obj->profil) 
                   
                   && isset($obj->prenom)  && !empty($obj->prenom)
                    && isset($obj->nom) && !empty($obj->nom)
                    
                    ){
                $id = secure($obj->id);
                $login = secure($obj->login);
                $email = secure($obj->email);
                $role = secure($obj->role);
                $actif = secure($obj->actif);
                $nom = secure($obj->nom);
                $prenom = secure($obj->prenom);
                
                $profil = secure($obj->profil);
                if(isset($obj->tel) )
                $tel = secure($obj->tel);
                else $tel ="";
                // modifier dans la BDD
                $t1 = array(
                    'tlogin' => $login,
                    'tnom' => $nom,
                    'tprenom' => $prenom,
                    'tactif' => $actif,
                    'temail' => $email,
                    'trole' => $role,
                    'tprofil' => $profil,
                    'ttel' => $tel,
                    'tid' => $id,
                );
                $req1 = $DB->prepare("UPDATE `user` SET `user_login` = :tlogin,   `user_email` = :temail, `user_tel` = :ttel, `user_role` = :trole, `user_nom` = :tnom , `user_prenom` = :tprenom , `user_actif` = :tactif , `profil_id` = :tprofil WHERE `user`.`user_id` = :tid;
                ");
                if($req1->execute($t1)){
                  
            
      
 ///////////// Audit système ///////////////////
 $tnew = array( // tableau des nouvelles valeurs
    'tlogin' => $login,
    'tnom' => $nom,
    'tprenom' => $prenom,
    'tactif' => $actif,
    'temail' => $email,
    'trole' => $role,
    'tprofil' => $profil,
    'ttel' => $tel,
);
       
        $t2 = array( // tableau des paramètres à enregistrer dans l'audit
            'tid' => "",
            'tnom' => "",
            'tip' => $ip,
            'tmachine' => $machine,
            'taction' => 'MAJ utilisateur',
            'tdescription' => "",
            'tissue' => "1",
                      );
  audit_sys($t2, $tnew,$jeton);
     ///////////// Audit système ///////////////////
                     
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Enregistré avec succès",
                    ];
                   
                }else{
                    $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres incorrects_",
                ];
            }
            break;
        case 'D':// Suppression 
            if(isset($_GET['id'] ) && !empty($_GET['id'])){
                $id = secure($_GET['id']);
                  
                 $t1 = array(
                    
                    'tid' => $id,
                );
                $req1 = $DB->prepare("DELETE FROM user WHERE user_id = :tid");
                if($req1->execute($t1)){
                   
                      // MAJ Audit ******************************************************
              $ip = $_SERVER['REMOTE_ADDR'];
              $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
       $t2 = array(
               'tid' => "",
               'tnom' => "",
               'tip' => $ip,
               'tmachine' => $machine,
                         );
      $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'D', 'Suppression d\'un utilisateur', '1', '', 'userId: ".$id."',  :tid);
      ");
      $req2->execute($t2);
                  
                        // cas où la requête s'est bien exécutée
                        $infoHttp = [
                        "reponse" => "success",
                        "message" => "Supprimé avec succès",
                    ];
                    
                }else{
                     // MAJ Audit ******************************************************
              $ip = $_SERVER['REMOTE_ADDR'];
              $machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);
       $t2 = array(
               'tid' => "",
               'tnom' => "",
               'tip' => $ip,
               'tmachine' => $machine,
                         );
      $req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, 'D', 'Suppression d\'un utilisateur', '0', '', 'userId: ".$id."',  :tid);
      ");
      $req2->execute($t2);
      $infoHttp = [
                        "reponse" => "error",
                        "message" => "Connexion aux données impossible, veuillez vérifier votre connexion.",
                    ];
                }
            }else{
                $infoHttp = [
                    "reponse" => "error",
                    "message" => "Paramètres incorrects",
                ];
            }
            break;
        default :
            $infoHttp = [
                "reponse" => "error",
                "message" => "Lien incorrect",
                "jeton" => false,
            ];
    }
}else{
    $infoHttp = [
        "reponse" => "error",
        "message" => "Lien incorrect",
        "jeton" => false,
    ];
}

echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);


?>
