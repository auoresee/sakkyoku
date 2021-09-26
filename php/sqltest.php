<?php

require_once "sqlaccess.php";
//require_once "songaccess.php";

$sqlip = "localhost";


//$pdo = new PDO('mysql:host=' . $sqlip . ';dbname=test_db;charset=utf8','sakkyoku_user','G2a7s_r3B');

main();

function main(){
	initPDO("noverdi");
	
	createSongTable();
	
	/*if(!checkSongExistsInDB(12)){
		addSongToDB($songobj);
	}*/
	
	outputSongListDB();
	
	
	echo "succeed";
}

function createSongTable(){
	$pdo = getPDO();
	
	$qry = $pdo->prepare('CREATE TABLE IF NOT EXISTS song_table (
		song_id int PRIMARY KEY,
		user_id int, 
		name VARCHAR(250),
		is_on_release boolean,
		created_date DATETIME,
		released_date DATETIME DEFAULT "2000-01-01 00:00:00",
		last_updated_date DATETIME
	) default charset=utf8');

	$qry->execute();
}

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

//for debug
function outputSongListDB(){
	$pdo = getPDO();
	
	$song_table = "song_table";
	
	$qry = $pdo->prepare("SELECT * FROM " . $song_table);
	$qry->execute();
	
	$arr = $qry->fetchAll();
	var_dump($arr);
	
	return false;
}

function addSongToDB($song){
	$pdo = getPDO();
	
	$qry = $pdo->prepare("INSERT INTO song_table (
		song_id, user_id, name, is_on_release, created_date, last_updated_date
	) VALUES (
		:song_id, :user_id, :name, :is_on_release, :created_date, :last_updated_date
	)");
	
	$prm = array(
		':song_id' => $song->songID,
		':user_id' => $song->userID,
		':name' => $song->name,
		':is_on_release' => $song->isOnRelease,
		':created_date' => timestamp2datetime((int) ($song->createdDate / 1000)),
		':last_updated_date' => timestamp2datetime((int) ($song->lastUpdatedDate / 1000))
	);
	
	$qry->execute($prm);
	
}





?>