<?php 
require __DIR__.'/vendor/autoload.php';
require 'webhooks/lib/fifazo.php';
$bl = "<br/>";

$parameters = array(
    'type' => 'partidos.consulta.vs.ultimo',
    'params' => array(
        'personas' => array(
            'Miguel'
        ),
        'fifazo' => 'último'
    ),
);

function intent_recibido($str){global $parameters; return ($parameters['type'] == $str);}
function enviar_texto($str){echo $str;die();}
function obtener_variables(){global $parameters;return $parameters['params'];}

//partidos.consulta.vs.ultimo
if(intent_recibido('partidos.consulta.vs.ultimo')){
	$params = obtener_variables();
	if(isset($params['personas'])){
        if(count($params['personas']) == 2){
            $fifazo = new fifazo($bl);
            $str = $fifazo->getVsInfo($params['personas'], false);
            enviar_texto($str);
        }elseif(count($params['personas']) == 1){
            $fifazo = new fifazo($bl);
            $perso = reset($params['personas']);
            $str = $fifazo->getPlayerInfo($perso, false);
            enviar_texto($str);
        }
	}
}