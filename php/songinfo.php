<?php

class SongInfo {
	public $name = "";
	public $songID = 0;
	public $userID = 0;
	public $isOnRelease = false;
	
	public $createdDate = 0;
	public $lastUpdatedDate = 0;
	public $releasedDate = 0;
	
	function __construct($p_name, $p_songID, $p_userID, $p_isOnRelease, $p_createdDate, $p_lastUpdatedDate, $p_releasedDate){
		$this->name = $p_name;
		$this->songID = $p_songID;
		$this->userID = $p_userID;
		$this->isOnRelease = (boolean) $p_isOnRelease;
		$this->createdDate = $p_createdDate;
		$this->lastUpdatedDate = $p_lastUpdatedDate;
		$this->releasedDate = $p_releasedDate;
	}
	
}

?>