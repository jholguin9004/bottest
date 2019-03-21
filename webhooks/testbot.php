<?php
error_reporting(E_ERROR | E_PARSE);
include_once 'lib/somosioticos_dialogflow.php';
credenciales('jholguin9004', 'Sofi789456');
if(intent_recibido('prueba')){
	$params = obtener_variables();
	var_dump($params);
}
//
//
?>