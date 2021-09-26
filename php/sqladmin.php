<?php

require_once "sqlaccess.php";
require_once "songaccess.php";

function getSongFileList() {
	chdir("../songs/");
	
	$filewildcard = "*.song";
	
	$files = glob($filewildcard);
	
	chdir("../php/");
	
	return $files;
}

function checkAndRecoverDBConsistency() {
	$file_list = getSongFileList();
	
	initPDO();
	$pdo = getPDO();

	if(!checkSongTableExistence()){
		createSongTable();
	}

	$songlistdb = retrieveSongIDListFromDB();

	for($i = 0; $i < count($file_list); $i++){
		$splitarr = explode( ".", $file_list[$i] );
		$song_id = intval($splitarr[0]);

		if(array_search($song_id, $songlistdb) != false){
			$song = loadSong($song_id);
			updateSongInDB($song);
		}
		else{
			echo("Found unregistered song file: Adding to database...<br>")
			$song = loadSong($song_id);
			addSongToDB($song);
		}

	}
}

?>