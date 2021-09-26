<?php

require_once "sqlaccess.php";
require_once "useraccess.php";
require_once "songaccess.php";
require_once "songinfo.php";
require_once "debug.php";


/*

receiving GET request format:

song_id=xxx &
mode= view or edit

*/

define("SONGLIST_RELEASE_ANY", 1);		//no matter whether released
define("SONGLIST_RELEASE_TRUE", 2);		//released songs only
define("SONGLIST_RELEASE_FALSE", 3);	//not released songs only

define("SONGLIST_TARGET_ALL", 1);
define("SONGLIST_TARGET_MY", 2);
define("SONGLIST_TARGET_SPECIFIED_USER", 3);

define("SONGLIST_SORT_LAST_UPDATED_DATE", 1);
define("SONGLIST_SORT_RELEASED_DATE", 2);
define("SONGLIST_SORT_CREATED_DATE", 3);
define("SONGLIST_SORT_VOTE", 4);



main();



function main(){
	cookieInit();
	
	initPDO("noverdi");		//initialize SQL
	
	$target = (int) $_GET['target'];	//gets all song, my song or a specific user's song
	$release = (int) $_GET['release'];	//released songs only, or all songs
	$sort = (int) $_GET['sort'];	//sorts in order of time, popularity or etc
	$offset = (int) $_GET['offset'];	//ex. offset is 30, gets 31-60
	$num = (int) $_GET['num'];	//how many songs requested
	
	$list = getSongList($target, $release, $sort, $num, $offset);
	
	outputSongList($list);
	
	saveDebugLog();
}

function outputSongList($list){
	$listjson = json_encode($list);
	$json = '{ "sender": "songlistmanager", "userID": ' . $_SESSION['user_id'] . ', "songList": ' . $listjson . '}';
	
	echo $json;
}

function getSongList($target, $release, $sort, $num, $offset){
	$pdo = getPDO();
	
	$where = "WHERE 1=1 ";	// place holder
	$userid = -1;
	$order = "last_updated_date";
	

	
	
	if($release == SONGLIST_RELEASE_TRUE){
		$where .= " AND is_on_release = true";
	}
	else if($release == SONGLIST_RELEASE_FALSE){
		$where .= " AND is_on_release = false";
	}
	
	if($sort == SONGLIST_SORT_RELEASED_DATE){
		$order = "released_date";
	}
	if($sort == SONGLIST_SORT_CREATED_DATE){
		$order = "created_date";
	}
	
	$prm = array(
		':order' => $order,
		':num' => (int) $num,
		':offset' => (int) $offset
	);
	
	if($target == SONGLIST_TARGET_MY){
		$userid = (int) $_SESSION['user_id'];
		$where .= " AND user_id = :userid";
	}
	else if($target == SONGLIST_TARGET_SPECIFIED_USER){
		$userid = (int) $_GET['user_id'];
		$where .= " AND user_id = :userid";
	}
	
	$statement = "SELECT * FROM song_table " . $where . " ORDER BY :order LIMIT :num OFFSET :offset";
	
	$qry = $pdo->prepare($statement);
	
	$qry->bindValue(':order', $order);
	$qry->bindValue(':num', (int) $num, PDO::PARAM_INT);
	$qry->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
	if($userid >= 0){
		$qry->bindValue(':userid', (int) $userid, PDO::PARAM_INT);
	}
	
	$qry->execute();
	
	$arr = $qry->fetchAll(PDO::FETCH_ASSOC);
	
	$result = array();
	
	foreach($arr as $row){
		$obj = convertSQLRowToObject($row);
		$result[] = $obj;
	}
	
	return $result;
}


?>