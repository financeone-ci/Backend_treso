<?php

require_once '../connexion/connexion.php';
require_once '../fonctions/secure.php';

// Statut du paiement à la création
function StatutCreation()
{
    $data = useBdd('statutCreation');

    if($data['valider'] == 1)
    {
        $statu = 1;
    }elseif ($data['autoriser'] == 1)
    {
        $statu = 2;
    }elseif ($data['approuver'] == 1)
    {
        $statu = 3;
    }else
        {
            $statu = 4;
    }
    return $statu;
}

// Statut du paiement àprès validation
function StatutValidation($DB)
{
    $sql = "SELECT autoriser, approuver FROM securite";
    $req = $DB->query($sql);
    $data = $req->fetch();

    if ($data['autoriser'] == 1)
    {
        $statu = 2;
    }elseif ($data['approuver'] == 1)
    {
        $statu = 3;
    }else
    {
        $statu = 4;
    }

    return $statu;
}

// Statut du paiement àprès autorisation
function StatutAutorisation($DB)
{
    $sql = "SELECT approuver  FROM securite";
    $req = $DB->query($sql);
    $data = $req->fetch();

    if ($data['approuver'] == 1)
    {
        $statu = 3;
    }else
    {
        $statu = 4;
    }

    return $statu;
}