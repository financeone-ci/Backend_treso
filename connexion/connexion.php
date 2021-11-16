<?php
 header('Content-Type: application/json; charset=utf8');
 header('Access-Control-Allow-origin: *');
header('Access-Control-Allow-Headers: *'); 
//header('Content-Type: multipart/form-data');



		try {
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
			}catch (PDOException $e)
		{
			$infoHttp = [
				"reponse" => "error",
				"message" => "Connexion aux données impossible.",
				"jeton" => false,
			];
			echo json_encode($infoHttp, JSON_UNESCAPED_UNICODE);
			die();
		}
$key= '08101783738219be049b80b50a8a7d22ec9a2b02255bac14b6242ac58f738ed3';
?>
