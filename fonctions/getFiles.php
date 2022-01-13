<?php 
// Récupérer les fichiers dans un dossier
function getFiles($le_dossier,$id){
    $resultatTraite = []; 
    $server = 'http://localhost:8080/';
    try {
        if(is_dir($le_dossier)){
            if($dossier = opendir($le_dossier)){ 
                while(false !== ($fichier = readdir($dossier))){
                    if($fichier != '.' && $fichier != '..' && $fichier != 'index.   php' && $fichier != 'desktop.ini'){
                        $resultatTraite[] =$server.'api_treso_app/uploads/paiements/retraits/'.$id .'/'. $fichier ;
                    }
                }
                closedir($dossier); 
            }else
                $resultatTraite = 'Le dossier n\' a pas pu être ouvert';        
        }else
            $resultatTraite = [];
    } catch (\Throwable $th) {
        $resultatTraite = $th;
    }

    return $resultatTraite;
}