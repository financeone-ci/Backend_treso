<?php

switch ($_GET['$type']){
    case 'C':
        break;
    case 'R':
        if(isset($obj->user) && !empty($obj->user) && isset($obj->pwd) && !empty($obj->pwd)){
            print_r('ok');
            exit;
            $pwd = secure($obj->pwd);
            $user = secure($obj->user);
            try{
                $sql = "SELECT *
                        FROM user
                        WHERE user_login = '$user' AND user_pwd = '$pwd'";
                $req = $DB->query($sql);
                $count = $req->rowCount();
                if ($count > 0) {
                    $infoHttp = [
                        "reponse" => "oui",
                        "message" => "",
                    ];

                    $d = $req->fetchAll();
                }else{
                    $infoHttp = [
                        "reponse" => "non",
                        "message" => "Paramètres de connexion incorrects",
                    ];
                }
            }catch (PDOException $e)
            {
                echo "ERROR :" .$e->getMessage();
                die();
            }
        }
        break;
    case 'U':
        break;
    case 'D':
        break;
    default :

}
?>