<?php
// Création de devise**************************
require_once '../../connexion/connexion.php';
require_once '../../fonctions/secure.php';
require_once '../../fonctions/getToken.php';
require_once '../../fonctions/apiCreator.php'; // créateur d'API


$t = array(
    'tlibelle' => 'libelle',
    ':tdescription' => 'description',
);
$req =  "INSERT INTO `profil` (  `profil_libelle`, `Profil_description`, `id_societe`) VALUES (  :tlibelle, :tdescription, :societe)" ;
$response = apiCreator($DB, $req,"create", $t);
$issue = json_decode($response); 
  if(isset($issue->data))
    {
        // récupération des données utilisateur depuis $response 
        $itemID = $issue->payload->item_id;
        // recuperation des sous-menus
        $reqSM = $DB->query("SELECT * FROM `e_smenu`");
        $sousM = $reqSM->fetchAll(PDO::FETCH_OBJ);
        $reqSM->closeCursor();

        if(!empty($sousM)){
            // création des droits pour chaque ssous menu
            foreach ($sousM as $key => $value) {
                $t2 = array();
                $req2 =  "INSERT INTO `droits` ( `droits_lecture`, `droits_creer`, `droits_modifier`, `droits_supprimer`, `e_smenu_id`, `profil_id`) VALUES (  '0', '0', '0', '0', '$value->e_smenu_id', $itemID)" ;
                $response2 = apiCreator($DB, $req2, "create", $t2, false);

            }
        }
    }
        
/*


*/

// Audits
AuditSystem($DB, "Création", "Création d'un nouveau profil",  $response);

echo $response;
