<?php
class MySQLi_UsersKeyword extends DatabaseDAO implements Singleton,DAO_UsersKeyword {
	private static $instance = null;
	
	const SELECT_COLUMS = 'fk_users,
	                       fk_keywords,
	                       pk_keywords,
	                       keyword';
	
	/**
	 *	@return MySQLi_UsersKeyword
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersKeyword();
		}
		return self::$instance;	
	}

	public function update($userid, $keywordid){
		$this->startTransaction();

		$query = 'REPLACE INTO tbl_users_has_keywords('.UsersKeyword::FIELD_USER_ID.', '.UsersKeyword::FIELD_KEYWORD_ID.') 
		          VALUES(\''.$userid.'\', \''.$keywordid.'\')';
		$query = $this->masterQuery(new MySQLiQuery($query));

		if($query->getAffectedRows() > 0){
			$this->commit();
			return true;
		} else {
			$this->rollback();	
			return false;	 
		}
	}
	public function read($userid, $keywordid){
		$query = 'SELECT '.self::SELECT_COLUMS.'
		          FROM tbl_users_has_keywords
		          INNER JOIN tbl_keywords ON '.UsersKeyword::FIELD_KEYWORD_ID.'='.Keyword::FIELD_ID.'
		          WHERE '.UsersKeyword::FIELD_USER_ID.'=\''.$userid.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}
		
	public function delete($userid, $keywordid){
		$query = 'DELETE FROM tbl_users_has_keywords
		          WHERE '.UsersKeyword::FIELD_USER_ID.'=\''.$userid.'\' AND '.UsersKeyword::FIELD_KEYWORD_ID.'=\''.$keywordid.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
			
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;	 
		}
	}
}
?>