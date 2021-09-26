<?php

function getSongFileName($song_id){
	return "" . $song_id . ".song";
}

//returns the relative path of the song directory from /php/
function getSongDir(){
	return "../songs/";
}

function getSongFileList() {
	chdir("../songs/");
	
	$filewildcard = "*.song";
	
	$files = glob($filewildcard);
	
	chdir("../php/");
	
	return $files;
}

//loads song data from a song file as an object
function loadSong($song_id){
	$song_file_name = getSongFileName($song_id);
	$song_dir = getSongDir();
	$songjson = file_get_contents($song_dir . $song_file_name);
	
	return json_decode($songjson, false);
}

function createSongTable(){
	$pdo = getPDO();
	
	$qry = $pdo->prepare('CREATE TABLE song_table (
		song_id int PRIMARY KEY,
		user_id int, 
		name VARCHAR(250),
		is_on_release boolean,
		created_date DATETIME,
		last_updated_date DATETIME
	) default charset=utf8');

	$qry->execute();
}

//曲データがデータベースにあるかチェックする
function checkSongExistsInDB($song_id){
	$pdo = getPDO();
	
	$song_table = "song_table";
	
	$qry = $pdo->prepare("SELECT count(*) FROM " . $song_table . " WHERE song_id = :song_id");
	$qry->bindValue(':song_id', (int) $song_id, PDO::PARAM_INT);	//use explicit cast
	$qry->execute();
	
	$cnt = (int)$qry->fetchColumn();
	if($cnt >= 1){
		return true;
	}
	
	return false;
}

//曲IDのリストをデータベースから取得
function retrieveSongIDListFromDB(){
	$pdo = getPDO();

	$qry = $pdo->prepare("SELECT song_id FROM song_table");
	$qry->execute();
	$result = array();

	$cur = $qry->fetchColumn();

	while($cur !== false){
		$result[] = intval($cur);
		$cur = $qry->fetchColumn();
	}

	return $result;
}

//SQLで取得した行を曲情報オブジェクトに変換
function convertSQLRowToSongInfoObject($row){
	return new SongInfo($row['name'], $row['song_id'], $row['user_id'], $row['is_on_release'],
		datetime2timestamp($row['created_date']) * 1000, datetime2timestamp($row['last_updated_date']) * 1000,
		datetime2timestamp($row['released_date']) * 1000);
}

function addSongToDB($song){
	$pdo = getPDO();
	
	$qry = $pdo->prepare("INSERT INTO song_table (
		song_id, user_id, name, is_on_release, created_date, last_updated_date, released_date
	) VALUES (
		:song_id, :user_id, :name, :is_on_release, :created_date, :last_updated_date, :released_date
	)");
	
	$prm = array(
		':song_id' => $song->songID,
		':user_id' => $song->userID,
		':name' => $song->name,
		':is_on_release' => (int) $song->isOnRelease,
		':created_date' => timestamp2datetime((int) ($song->createdDate / 1000)),
		':last_updated_date' => timestamp2datetime((int) ($song->lastUpdatedDate / 1000)),
		':released_date' => timestamp2datetime((int) ($song->releasedDate / 1000))
	);
	
	$qry->execute($prm);
	
}

function updateSongInDB($song){
	$pdo = getPDO();
	
	$qry = $pdo->prepare("UPDATE song_table SET
		user_id = :user_id, name = :name, is_on_release = :is_on_release,
		created_date = :created_date, released_date = :released_date,
		last_updated_date = :last_updated_date
		WHERE song_id = :song_id
	");
	
	$prm = array(
		':song_id' => $song->songID,
		':user_id' => $song->userID,
		':name' => $song->name,
		':is_on_release' => (int) $song->isOnRelease,
		':created_date' => timestamp2datetime((int) ($song->createdDate / 1000)),
		':last_updated_date' => timestamp2datetime((int) ($song->lastUpdatedDate / 1000)),
		':released_date' => timestamp2datetime((int) ($song->releasedDate / 1000))
	);
	
	$qry->execute($prm);
	
}

function deleteSongFromDB($song_id) {
	$pdo = getPDO();

	$qry = $pdo->prepare("DELETE FROM song_table WHERE song_id = :song_id");
	$qry->bindValue(':song_id', (int) $song_id, PDO::PARAM_INT);	//use explicit cast
	$qry->execute();
}

?>