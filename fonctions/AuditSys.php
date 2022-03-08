<?php
// Audit d'utlisation du logiciel
function AuditSystem($BDD, $action, $description, $reponseRequete="" )
{
    $issue = json_decode($reponseRequete); 
    $response = false;
  if(isset($issue->data))
    // $data =  "";
  //    if($issue->data)
    {
        // récupération des données utilisateur depuis $reponseRequete
        $data = $issue->data;
        $login = $issue->payload->user_login;
        $itemID = $issue->payload->item_id;
        $userID = $issue->payload->user_id;
        $societe = $issue->payload->user_societe;

        // conversion du tableaux des nouvelles valeurs en chaine
        $implode = "";
        foreach ($data as $key => $value) {
            # code...
            $implode .= $key.': '.$value.' / ';
        }
        $data = $implode;
        if($issue->reponse === "error")
        {
            $issue_ = 'Echec';
        }elseif($issue->reponse === "success")
        {
            $issue_ = 'succès';
        }

        $t = array(
            'tuserID' => $userID,
            'tlogin' => $login,
            'taction' => $action,
            'tissue' => $issue_,
            'tdescription' => $description,
            'tsociete' => $societe,
            'tmachine' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'tIP' => $_SERVER['REMOTE_ADDR'],
            'titemID' => $itemID,
            'tNouvelValeur' => $data,
        );
        $sql = "INSERT INTO audit_sys(audit_sys_usernom, audit_sys_ip, audit_sys_machine, audit_sys_action, audit_sys_description, audit_sys_issue, audit_sys_item_id, audit_sys_nouvelleValeur, audit_sys_userid, id_societe) VALUES (:tlogin, :tIP, :tmachine, :taction, :tdescription, :tissue, :titemID, :tNouvelValeur, :tuserID, :tsociete)";
        try{
            $req = $BDD->prepare($sql);
            $req->execute($t);
            $response = true;
        }catch(PDOException $e)
        {
            $response = $e->getMessage();
            file_put_contents("log-".$issue_.date("d-m-y").'.txt', $description . " // ".$response);
        }
    }
    return $response;
}