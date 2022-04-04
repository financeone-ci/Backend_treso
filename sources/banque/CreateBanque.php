<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API

$t = array(
    'tcode' => 'CODE_BANQUE',
    'tlibelle' => 'LIBELLE_BANQUE',
    'tdirecteur' => 'DG',
    'tadresse' => 'ADRESSE_BANQUE',
    'tcontact' => 'CONTACT_BANQUE',
    'tgestionnaire' => 'GESTIONNAIRE',
    'tadresse_web' => 'ADRESSE_WEB_BANQUE',
);

$req =  "INSERT INTO `banque` ( `CODE_BANQUE`, `LIBELLE_BANQUE`, `DG`, `GESTIONNAIRE`, `ADRESSE_BANQUE`, `ADRESSE_WEB_BANQUE`, `CONTACT_BANQUE`) VALUES ( :tcode, :tlibelle, :tdirecteur, :tgestionnaire, :tadresse, :tadresse_web, :tcontact)" ;
$response = apiCreator($DB, $req, "create", $t, false);
// Audits
AuditSystem($DB, "Création", "Création de BANQUE",  $response);
/* $t2 = array(
    'tidBanque'=>$idBanque,
);
$req2 = $DB->prepare("INSERT INTO dimcheque (IDBANQUE) values (:tidBanque)");
*/
$issue = json_decode($response); 
  if(isset($issue->data))
    {
        // récupération des données depuis $response 
        $itemID = $issue->payload->item_id;
      
            // création des dimCheq
       
                $t2 = array();
                $req2 =  "INSERT INTO dimcheque (IDBANQUE) values ($itemID)" ;
               apiCreator($DB, $req2, "create", $t2, false, false);     
    }
        
/*


*/

// Audits
AuditSystem($DB, "Création", "Création de banque",  $response);

echo $response;