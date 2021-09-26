<?php

//for administration (not for user)

require_once "sqlaccess.php";
require_once "useraccess.php";
require_once "songinfo.php";
require_once "songaccess.php";
require_once "debug.php";

main();

function main(){
	cookieInit();
	initPDO('noverdi');
	
	$s_user_id = $_SESSION['user_id'];
	
	if(!isset($_POST['song_id'])){
		echo("!error: Invalid query");		//when not POSTed
		putdebug("Invalid query");
		exit;
	}
	
	$song_id = $_POST['song_id'];
	
	try{
		$song = loadSong($song_id);
	}catch(Exception $e){
		echo '!error: ',  $e->getMessage(), "\n";
	}

	if(!checkSongExistsInDB($song->songID)){
		addSongToDB($song);
	}
	else{
		updateSongInDB($song);
	}
	
	saveDebugLog();
}

function checkSongValid($song){
	return true;
}

function setIDtoSong($song){
	if($song->userID == 0){
		putdebug($song->userID);
		$song->userID = $_SESSION['user_id'];
	}
	if($song->songID == 0){
		putdebug($song->songID);
		$song->songID = getAvailableSongID();
	}
}

function saveSong($song){
	$json = json_encode($song);
	
	file_put_contents(getSongDir() . getSongFileName($song->songID), $json, LOCK_EX);
	
	echo('{ "sender": "songsaver", "userID": ' . $song->userID . ', "songID": ' . $song->songID . ' }');
}

function getAvailableSongID(){
	chdir(getSongDir());
	$files = glob("*.song");
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

function setSongToUserFile($user_id, $song_id, $song_name, $is_on_release){
	$user_filename = "../userdata/" . $user_id . ".user";
	if(file_exists($user_filename)){
		$data = file_get_contents($user_filename);
		$userobj = readUserData($data);
	}else{
		$userobj = generateNewUserData($user_id);
	}
	setSongToUserData($userobj, $song_id, $song_name, $is_on_release);
	$json = json_encode($userobj);
	file_put_contents($user_filename, $json, LOCK_EX);
}



function setSongToUserData($userobj, $song_id, $song_name, $is_on_release){
	if(!property_exists($userobj, 'songs')){
		$userobj->songs = array();
	}
	$newsong = new SongInfo($song_name, $song_id, $_SESSION['user_id'], $is_on_release, 0, 0, 0);
	$userobj->songs[count($userobj->songs)] = $newsong;
}




?>
