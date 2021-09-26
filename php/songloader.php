<?php

require_once "useraccess.php";
require_once "songaccess.php";
require_once "debug.php";

main();

/*

receiving GET request format:

song_id=xxx &
mode= view or edit

*/

function main(){
	cookieInit();
	
	$song_id = $_GET['song_id'];
	//$mode = $_GET['mode'];
	
	try{
		$songobj = loadSong($song_id);
	}catch(Exception $e){
		echo '!error: ',  $e->getMessage(), "\n";
	}
	outputSong($songobj);
	
	saveDebugLog();
}


//outputs song as json to standard output
function outputSong($songobj){
	$json = json_encode($songobj);
	
	if($songobj->userID == $_SESSION['user_id']){
		$ismysong = "true";
	}
	else {
		$ismysong = "false";
	}
	
	echo('{ "sender": "songloader", "userID": ' . $_SESSION['user_id'] . ', "songID": ' . $songobj->songID . ' , "isMySong": ' . $ismysong . ', "song": ' . $json . ' }');
}



?>