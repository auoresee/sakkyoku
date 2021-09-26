<?php

require_once "sqlaccess.php";
require_once "songaccess.php";

//データベースの整合性をチェックし修復する
function checkAndRecoverDBConsistency() {
	$file_list = getSongFileList();
	
	$pdo = getPDO();

	if(!checkSongTableExistence()){
		createSongTable();
	}

	$songlistdb = retrieveSongIDListFromDB();

	for($i = 0; $i < count($file_list); $i++){
		$splitarr = explode( ".", $file_list[$i] );
		$song_id = intval($splitarr[0]);

		$foundindex = array_search($song_id, $songlistdb);

		if($foundindex !== false){
			$song = loadSong($song_id);
			updateSongInDB($song);
			unset($songlistdb, $foundindex);
		}
		else{
			echo("Found unregistered song file: Adding to database...<br>");
			$song = loadSong($song_id);
			addSongToDB($song);
		}

	}

	//存在しない曲ファイルがDBに登録されている場合
	foreach($songlistdb as $id){
		echo("Found non-existent song file registered in database: Deleting from database...<br>");
		deleteSongFromDB($id);
	}

	echo("データベースの再構築が完了しました。");
}

function main(){
	initPDO('noverdi');
	
	//POST以外のリクエストの場合
	if($_SERVER["REQUEST_METHOD"] != "POST"){
		echo '!error: Invalid request';
	}

	if(!isset($_POST['password'])){
		echo("!error: Invalid query");
		putdebug("Invalid query");
		exit;
	}

	$password = $_POST['password'];

	if ( strcmp($password, $GLOBALS['SQL_PASSWORD']) != 0 ) {
		echo("!error: Incorrect password");
	}
	
	checkAndRecoverDBConsistency();
	
	saveDebugLog();
}

main();

?>