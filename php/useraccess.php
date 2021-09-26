<?php

$session_lifetime = 86400 * 365;

// must be called before any output or $_SESSION access
function cookieInit(){
	global $session_lifetime;
	ini_set( 'session.gc_maxlifetime', $session_lifetime );
	ini_set( 'session.cookie_lifetime', $session_lifetime );

	session_start();

	if(!isset($_SESSION['login'])){
		registerUser();
	}
}

function registerUser(){
	$user_id = getAvailableUserID();
	$_SESSION['user_id'] = $user_id;
	$_SESSION['login'] = true;
}

//generate new user ID
function getAvailableUserID(){
	chdir("../userdata/");
	$files = glob("*.user");
	$maxid = 0;
	foreach ($files as $val) {
		putdebug($val);
		$splitarr = explode( ".", $val );
		$id = intval($splitarr[0]);
		if($id > $maxid){
			$maxid = $id;
		}
	}
	
	putdebug($maxid);
	
	chdir("../php/");
	
	return $maxid + 1;
}

class UserData {
	public $userID = 0;
	public $userName = "";
	public $songs = array();
}

function generateNewUserData($user_id){
	$ret = new UserData();
	$ret->userID = $user_id;
	return $ret;
}

function readUserData($data){
	$obj = json_decode($data, false);
	return $obj;
}

?>
