<?php

$debug = "";

function putdebug($str){
	global $debug;
	$debug .= $str. "\n";
}

function saveDebugLog(){
	file_put_contents("debug.dat", $GLOBALS['debug']);
}

?>