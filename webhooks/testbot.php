<?php
error_reporting(E_ERROR | E_PARSE);
require __DIR__.'/../vendor/autoload.php';
require 'lib/fifazo.php';
require 'lib/somosioticos_dialogflow.php';
credenciales('jholguin9004', 'Sofi789456');

//partidos.consulta.vs.ultimo
if(intent_recibido('partidos.consulta.vs.ultimo')){
	$params = obtener_variables();
	if(isset($params['personas'])){
        if(count($params['personas']) == 2){
            $fifazo = new fifazo();
            $str = $fifazo->getVsInfo($params['personas'], false);
            enviar_texto($str);
        }else{
            enviar_texto('Se necesitan por lo menos 2 personas para jugar un partido, no?');
        }
	}
}