<?php 
require __DIR__.'/vendor/autoload.php';
require 'webhooks/lib/fifazo.php';
$fifazo = new fifazo();
$perso = array('Mono');
$perso = reset($perso);
$str = $fifazo->getPlayerInfo($perso, false);
echo $str;