<?php
class MySQLi_UsersCategories extends DatabaseDAO implements Singleton,DAO_UsersCategories {
	private static $instance = null;
	
	/**
	 *	@return Database
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersCategories();
		}
		return self::$instance;	
	}
	public function deleteCategory($uid,$id) {
		$query = 'DELETE FROM tbl_users_has_categories WHERE fk_categories=\''.$id.'\'AND fk_users=\''.$uid.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		return true;
	}
	public function setCategory($uid, $id) {
		$query = 'REPLACE INTO tbl_users_has_categories(fk_users, fk_categories)VALUES(\''.$uid.'\', \''.$id.'\')';
		$query = $this->masterQuery(new MySQLiQuery($query));
		return true;		
		
	}
	public function getCategories($uid) {
		$query = 'SELECT category, pk_categories
		          FROM tbl_users_has_categories
		          LEFT JOIN tbl_categories ON fk_categories=pk_categories
		          WHERE fk_users=\''.$uid.'\'';
		return $this->slaveQuery(new MySQLiQuery($query));		
	}
	public function getCategoriesCount($uid) {
		$query = 'SELECT category, pk_categories, COUNT(tbl_weblog_has_categories.fk_categories) AS count
		          FROM tbl_users_has_categories
		          LEFT JOIN tbl_categories ON fk_categories=pk_categories
		          LEFT JOIN tbl_weblog_has_categories ON tbl_weblog_has_categories.fk_categories=tbl_users_has_categories.fk_categories
		          LEFT JOIN tbl_weblog ON fk_weblog=pk_weblog
		          WHERE tbl_users_has_categories.fk_users=\''.$uid.'\' AND tbl_weblog.state = \'PUBLIC\' AND closed = \'FALSE\'
				  GROUP BY pk_categories';
		return $this->slaveQuery(new MySQLiQuery($query));		
	}
}
?>
