<?php
class MySQLi_UserPermissionList extends DatabaseDAO implements Singleton,DAO_UserPermissionList {
	private static $instance = null;

	
	/**
	 *	@return MySQLi_UserPermissionList
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UserPermissionList();
		}
		return self::$instance;	
	}
	
	public function removePermissions($userid){
		$query = 'DELETE FROM tbl_users_has_permissions WHERE fk_users=\''.$userid.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}
	public function grantPermission($userid, $permission, $comment=null, $expire=null){
		$query = MySQLiTools::makeReplaceStatement('tbl_users_has_permissions', array('fk_users','fk_users_permissions','comment','expire'=>'FROM_UNIXTIME(?)'));
		$query = $this->masterQuery(new MySQLiQueryStatement($query, $userid, $permission, $comment, $expire));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}
	public function revokePermission($userid, $permission){
		$query = 'DELETE FROM tbl_users_has_permissions WHERE fk_users=\''.$userid.'\' AND fk_users_permissions=\''.$permission.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
		
	}
	
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order, $offset=null, $limit=null){
		$order = MySQLiTools::prepareOrderStatement($order, UserPermission::FIELD_TITLE);
		
		$columns = MySQLi_UserPermission::SELECT_COLUMNS;
		
		if(!$limit = MySQLiTools::prepareLimitStatement($offset, $limit)){
			$limit = '';
		}
		
		$where = $this->_prepareWhereStatement($filter);
		
		$query = 'SELECT '.$columns.' '.$where['columns'].'
		          FROM tbl_users_permissions
		          '.$where['join'].'
		          '.$where['where'].'
		          '.$order.'
		          '.$limit;
		return $this->query(new MySQLiQuery($query));
	}
	
	public function getListCount(DatabaseListHelperFilter $filter){
		$where = $this->_prepareWhereStatement($filter);
		$query = 'SELECT COUNT('.UserPermission::FIELD_ID.') AS count
		          FROM tbl_users_permissions
		          '.$where['join'].'
		          '.$where['where'];
		$query = $this->query(new MySQLiQuery($query));
		$query = $query->fetchArray();
		return $query['count'];
	}	
	
	private function _prepareWhereStatement(DatabaseListHelperFilter $filter){
		$where = 'WHERE 1 ';
		$join = '';
		$columns = '';
		if($filter->count() > 0){
			if($title = $filter->get(UserPermission::FIELD_TITLE)){
				$where .= 'AND '.UserPermission::FIELD_HEADLINE.' LIKE \''.MySQLiTools::parseWildcards($title).'\' ';
			}
			if($userid = $filter->get(UserPermissionList::FIELD_USER_FILTER)){
				if($filter->get(UserPermissionList::FIELD_USER_STRICT_FILTER)){
					$join .= ' INNER ';
				} else {
					$join .= ' LEFT ';
				}
				$columns .= ', fk_users AS '.User::FIELD_ID.' ';
				$join .= ' JOIN tbl_users_has_permissions ON pk_users_permissions=fk_users_permissions AND fk_users=\''.$userid.'\'';
			}
		}
		return array('where' => $where, 'join' => $join, 'columns' => $columns);
	}
}
?>