<?php
// Audit de connexion et de déconnexion à l'application
function AuditConnexion($BDD, $userID, $login, $action, $issue = 1, $description, $societe){
    $response = false;
    $t = array(
        'tuserID' => $userID,
        'tlogin' => $login,
        'taction' => $action,
        'tissue' => $issue,
        'tdescription' => $description,
        'tsociete' => $societe,
        'tmachine' => $_SERVER['REMOTE_HOST'],
        'tIP' => $_SERVER['REMOTE_ADDR'],
    );
    $sql = "INSERT INTO audit_cnx(audit_cnx_userid, audit_cnx_usernom, audit_cnx_ip, audit_cnx_machine, audit_cnx_action, audit_cnx_issue, audit_cnx_description, id_societe) VALUES (:tuserID, :tlogin, :tIP, :tmachine, :taction, :tissue, :tdescription, :tsociete)";
    try{
        $req = $BDD->prepare($sql);
        $req->execute($t);
        $response = true;
    }catch(PDOException $e)
    {
        $response = $e->getMessage();
    }
    return $response;
}