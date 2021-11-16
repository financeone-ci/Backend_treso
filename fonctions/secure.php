<?php
// ************************************ Declarations
$ip = $_SERVER['REMOTE_ADDR'];
$machine = gethostbyaddr($_SERVER['REMOTE_ADDR']);

// fonction securité requête
function secure($chaine){
//    return addslashes(htmlspecialchars(strip_tags($chaine)));
    return htmlspecialchars(strip_tags($chaine));
}


// MAJ Audit ******************************************************

function audit_sys ($tval= array('tid' => "",'tnom' => "",'tip' => "",'tmachine' => "",'taction' => "",'tdescription' => "",'tissue' => "",'toldValue' => "",'tnewValue' => "",), $tabNewVal =[] , $jeton = "")
{$key='08101783738219be049b80b50a8a7d22ec9a2b02255bac14b6242ac58f738ed3';
    if(!empty($jeton)){
        $decoded = JWT::decode($jeton, $key, array('HS256'));
        $tval['tnom']  = $decoded->user_nom.' '.$decoded->user_prenom;
        $tval['tid']  = $decoded->user_id;
    }
    
    
   $tval['tnewValue']  = implode(",", $tabNewVal);
   
        $dns = 'mysql:host=localhost; dbname=treso_app';
        $user = 'root';
        $pwd = '';
    
        //option de connexion
        $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
                        PDO::ATTR_PERSISTENT => true,
                        );
    
        //initialisation de la connexion 
        $DB = new PDO ($dns,$user,$pwd,$option);
        $DB->setAttribute (PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$req2 = $DB->prepare("INSERT INTO `audit_sys` (`audit_sys_usernom`, `audit_sys_ip`, `audit_sys_machine`, `audit_sys_action`, `audit_sys_description`, `audit_sys_issue`, `audit_sys_ancienneValeur`, `audit_sys_nouvelleValeur`,  `audit_sys_userid`) VALUES (:tnom, :tip, :tmachine, :taction, :tdescription, :tissue, '', :tnewValue,  :tid);
");
$req2->execute($tval);

}

//************* conversion date venant de excel
function convert_date_excel($date){
	$real_date = ((int)$date-25569)*86400; 
	$real_date = date("Y-m-d", $real_date); 
	return $real_date;
}
////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
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



?>