<?php
// Audit d'utlisation du logiciel
function AuditSystem($BDD, $login, $action, $description, $issue = 'succès', $itemID, $NouvelValeur, $userID, $societe){
    $response = false;
    $t = array(
        'tuserID' => $userID,
        'tlogin' => $login,
        'taction' => $action,
        'tissue' => $issue,
        'tdescription' => $description,
        'tsociete' => $societe,
        'tmachine' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
        'tIP' => $_SERVER['REMOTE_ADDR'],
        'titemID' => $itemID,
        'tNouvelValeur' => $NouvelValeur,
    );
    $sql = "INSERT INTO audit_sys(audit_sys_usernom, audit_sys_ip, audit_sys_machine, audit_sys_action, audit_sys_description, audit_sys_issue, audit_sys_item_id, audit_sys_nouvelleValeur, audit_sys_userid, id_societe) VALUES (:tlogin, :tIP, :tmachine, :taction, :tdescription, :tissue, :titemID, :tNouvelValeur, :tuserID, :tsociete)";
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