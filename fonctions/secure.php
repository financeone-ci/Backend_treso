<?php
// ************************************ Declarations
require_once 'AuditSys.php';
require_once 'AuditCnx.php';

$ip = $_SERVER['REMOTE_ADDR'];
$machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);

// fonction securité requête
function secure($chaine){
//    return addslashes(htmlspecialchars(strip_tags($chaine)));
    return htmlspecialchars(strip_tags($chaine));
}

//************* conversion date venant de excel
function convert_date_excel($date)
{
	$real_date = ((int)$date-25569)*86400; 
	$real_date = date("Y-m-d", $real_date); 
	return $real_date;
}

//*********************************************************** */
function useBdd(string $type, array $donnees=[]){
    $dns = 'mysql:host=localhost; dbname=treso_app';
    $user = 'root';
    $pwd = '';
    $resultat = '';

    //option de connexion
    $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
                    PDO::ATTR_PERSISTENT => true,
                    );

    //initialisation de la connexion 
    $DB = new PDO ($dns,$user,$pwd,$option);
    $DB->setAttribute (PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    if($type == 'getSolde'){
            $sql1 = "SELECT `ID_ENGAGEMENT` AS id, 
            IFNULL(engagement.`MONTANT` -  SUM(engagement_paiement.`MONTANT`) , engagement.`MONTANT`) AS SOLDE, BENEFICIAIRE, MOTIF  
            FROM `engagement` LEFT JOIN `engagement_paiement` ON engagement.ID_ENGAGEMENT = engagement_paiement.IDENGAGEMENT WHERE ID_ENGAGEMENT = ".$donnees['id']." GROUP BY ID_ENGAGEMENT " ;
            $req1 = $DB->query($sql1);
             $resultat = $req1->fetch();            
    }
    elseif($type == 'getBenef'){
         
            $sql1 = "SELECT BENEFICIAIRE   FROM  `engagement` WHERE IDENGAGEMENT = ".$donnees['id'] ;
            $req1 = $DB->query($sql1);
             $resultat = $req1->fetch();            
    }

    elseif($type == 'statutCreation'){
         
        $sql = "SELECT valider, autoriser, approuver  FROM securite";
        $req = $DB->query($sql);
        $resultat = $req->fetch();            
    }

    return $resultat;
}

// Vérifier la validité du token**********************************************************************************
function ChekToken($jeton)
{
    $tok = false;
    $key='08101783738219be049b80b50a8a7d22ec9a2b02255bac14b6242ac58f738ed3';
    if(isset($jeton) && !empty($jeton)){
        $decoded = JWT::decode($jeton, $key, array('HS256'));
        if($decoded){
            $tok = true;
        }
    }
    return $tok;
}

// Récupérer les informations du token**************************************************************************
function tokenData($jeton)
{
    $data = '';
    $key='08101783738219be049b80b50a8a7d22ec9a2b02255bac14b6242ac58f738ed3';
    if(isset($jeton) && !empty($jeton)){
        $decoded = JWT::decode($jeton, $key, array('HS256'));
        if($decoded){
            $data = $decoded;
        }
    }
    return $data;
}