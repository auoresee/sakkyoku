<?php

$pdo = null;
	
//PDOを初期化(SQLに接続)
function initPDO($db_name) {
    $SQL_IP = "localhost";
    $SQL_USER = "noverdi";
    $SQL_PASSWORD = "v7N2k8pG2";
    $GLOBALS['pdo'] = new PDO('mysql:host=' . $SQL_IP . ';dbname=' . $db_name . ';charset=utf8',$SQL_USER,$SQL_PASSWORD);
    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
	
//PDOを取得
function getPDO(){
    return $GLOBALS['pdo'];
}

function checkSongTableExistence(){
    $pdo = getPDO();

    $qry = $pdo->prepare("SHOW TABLES LIKE 'song_table'");
    $rows = $qry->execute();
    $result = array();

    $cur = $rows->fetchColumn();

    if($cur == false) return false;

    return true;
}

function timestamp2datetime($timestamp){
    //PHPのタイムスタンプをMySQLのdatetime型に変換。
    return date("Y-m-d H:i:s", $timestamp);
}

function datetime2timestamp($datetime){
    //MySQLのdatetime型をPHPのタイムスタンプに変換。
    return DateTime::createFromFormat("Y-m-d H:i:s", $datetime)->getTimestamp();
}

?>