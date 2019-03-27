<?php
error_reporting(E_ERROR | E_PARSE);
require __DIR__.'/../vendor/autoload.php';
require 'lib/somosioticos_dialogflow.php';
credenciales('jholguin9004', 'Sofi789456');

//partidos.consulta.vs.ultimo
if(intent_recibido('partidos.consulta.vs.ultimo')){
	$params = obtener_variables();
	if(isset($params['personas'])){
		enviar_texto(implode(', ', $params['personas']) . 'jugaron');
	}
}