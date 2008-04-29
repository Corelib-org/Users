<?php
class MySQLi_UsersList extends DatabaseDAO implements Singleton,DAO_UsersList {
	private static $instance = null;
	
	/**
	 *	@return MySQLi_UsersList
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersList();
		}
		return self::$instance;	
	}
	
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order, $offset=null, $limit=null){
		$order = MySQLiTools::prepareOrderStatement($order, User::FIELD_USERNAME,
		                                                    User::FIELD_EMAIL, 
		                                                    User::FIELD_CREATE_TIMESTAMP, 
		                                                    User::FIELD_LAST_TIMESTAMP);
		if(!$order){
			$order = '';
		} else {
			$order = 'ORDER BY '.$order;
		}
		
		if($filter->count() > 0){
			$where = 'WHERE 1 ';
			if($username = $filter->get(User::FIELD_USERNAME)){
				$where .= 'AND '.User::FIELD_USERNAME.' LIKE \''.MySQLiTools::parseWildcards($username).'\' ';
			}
			if($email = $filter->get(User::FIELD_EMAIL)){
				$where .= 'AND '.User::FIELD_EMAIL.' LIKE \''.MySQLiTools::parseWildcards($email).'\' ';
			}
			if($activated = $filter->get(User::FIELD_ACTIVATED)){
				$where .= 'AND '.User::FIELD_ACTIVATED.'='.MySQLiTools::parseBooleanValue($activated).' ';
			}
		} else {
			$where = '';
		}
		
		if(!$limit = MySQLiTools::prepareLimitStatement($offset, $limit)){
			$limit = '';
		}
		
		$query = 'SELECT '.MySQLi_User::SELECT_COLUMNS.'
		          FROM tbl_users
		          '.$where.'
		          '.$order.'
		          '.$limit;
		return $this->query(new MySQLiQuery($query));
	}
}
?>