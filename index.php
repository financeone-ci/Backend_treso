<?php

header('Content-Type: application/json; charset=utf8');
header('Access-Control-Allow-origin: *');
header('Access-Control-Allow-Headers: *');
echo gethostbyaddr($_SERVER['REMOTE_ADDR'])  ;

/* git */
?>