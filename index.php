<?php 
require __DIR__.'/vendor/autoload.php';
require 'webhooks/lib/fifazo.php';
$fifazo = new fifazo('<br/>');
$params = array(
    'personas' => array(
        'Daniel', 'Cristian'
    )
);
if(count($params['personas']) == 2){
    $str = $fifazo->getVsInfo($params['personas'], false);
    enviar_texto($str);
}elseif(count($params['personas']) == 1){
    $perso = reset($params['personas']);
    $str = $fifazo->getPlayerInfo($perso, false);
    enviar_texto($str);
}

function enviar_texto($str){
    echo $str;
    die();
}