<?php
class MySQLi_UsersKeywords extends DatabaseDAO implements Singleton,DAO_UsersKeywords {
	private static $instance = null;
	
	/**
	 *	@return Database
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersKeywords();
		}
		return self::$instance;	
	}
	
	public function flushKeywords($uid){
		$query = 'DELETE FROM tbl_users_has_keywords WHERE fk_users=\''.$uid.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		return true;
	}
	
	public function setKeyword($uid, $keyword){
		$query = 'REPLACE INTO tbl_users_has_keywords(fk_users, fk_keywords)VALUES(\''.$uid.'\', \''.$keyword.'\')';
		$query = $this->masterQuery(new MySQLiQuery($query));
		return true;
	}
	
	public function getKeywords($uid){
		$query = 'SELECT keyword
		          FROM tbl_users_has_keywords
		          INNER JOIN tbl_keywords ON fk_keywords=pk_keywords
		          WHERE fk_users=\''.$uid.'\'';
		return $this->slaveQuery(new MySQLiQuery($query));
	}
}
?>
