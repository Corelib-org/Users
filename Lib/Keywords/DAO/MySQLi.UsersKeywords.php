<?php
class MySQLi_UsersKeywords extends DatabaseDAO implements Singleton,DAO_UsersKeywords {
	private static $instance = null;

	
	/**
	 *	@return MySQLi_UsersKeywords
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersKeywords();
		}
		return self::$instance;	
	}
	
	public function removeAllKeywords($userid){
		$query = 'DELETE FROM tbl_users_has_keywords WHERE '.UsersKeyword::FIELD_USER_ID.' = \''.$userid.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function getList(DatabaseListHelperFilter $filter){
		$columns = MySQLi_UsersKeyword::SELECT_COLUMNS;
		
		
		$query = 'SELECT '.$columns.'
		          FROM tbl_users_has_keywords
		          INNER JOIN tbl_keywords ON fk_keywords=pk_keywords
		          '.$this->_prepareWhereStatement($filter);
		return $this->query(new MySQLiQuery($query));
	}
	
	public function getListCount(DatabaseListHelperFilter $filter){
		$query = 'SELECT COUNT('.UsersKeywords::FIELD_ID.') AS count
		          FROM 
		          '.$this->_prepareWhereStatement($filter);
		$query = $this->query(new MySQLiQuery($query));
		$query = $query->fetchArray();
		return $query['count'];
	}	
	
	private function _prepareWhereStatement(DatabaseListHelperFilter $filter){
		$where = 'WHERE 1 ';
		if($filter->count() > 0){
			if($userid = $filter->get(UsersKeyword::FIELD_USER_ID)){
				$where .= 'AND '.UsersKeyword::FIELD_USER_ID.' = \''.$userid.'\' ';
			}
		}
		return $where;
	}
}