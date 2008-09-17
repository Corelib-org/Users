<?php
class MySQLi_UsersAuthorization extends DatabaseDAO implements Singleton,DAO_UserAuthorization {
	private static $instance = null;
	
	/**
	 *	@return MySQLi_UsersAuthorization
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersAuthorization();
		}
		return self::$instance;	
	}
	
	public function getUserPermissions($userid){
		$query = 'SELECT pk_users_permissions, ident
		          FROM tbl_users_has_permissions
		          INNER JOIN tbl_users_permissions ON fk_users_permissions=pk_users_permissions
		          WHERE fk_users=\''.$userid.'\' AND (expire > NOW() OR expire IS NULL)';
		return $this->slaveQuery(new MySQLiQuery($query));
	}
	/*
	public function getUserReversePermissions($userid){
		$res = false;
		try {
			if(is_integer($userid)){
				$query = 'SELECT pk_users_permissions, permission_ident
				          FROM tbl_users_has_reverse_permissions
				          INNER JOIN tbl_users_permissions ON fk_users_permissions=pk_users_permissions
				          WHERE fk_users=\''.$userid.'\'';
				$res = $this->slaveQuery(new MySQLiQuery($query));
			} else {
				throw new BaseException('$userid is of invalid datatype');
			}
		} catch (Exception $e){
			echo $e;
		}
		return $res;
	}
	
	public function getUserRolesPermissions($userid, $roleid=null){
		$res = false;
		try {
			if(!is_null($roleid) && !is_int($roleid)){
				throw new BaseException('$roleid, must be if type interger og null');
			} else if(!is_integer($userid)){
				throw new BaseException('$userid is of invalid datatype');
			} else {
				$subquery = 'SELECT GROUP_CONCAT(fk_users_permissions SEPARATOR \',\')
				             FROM tbl_users_has_roles
				             INNER JOIN tbl_users_roles ON tbl_users_has_roles.fk_users_roles=pk_users_roles
				             INNER JOIN tbl_users_roles_has_permissions ON tbl_users_roles_has_permissions.fk_users_roles=pk_users_roles
				             WHERE fk_users='.$userid;
				$query = 'SELECT pk_users_permissions, permission_ident
				          FROM tbl_users_permissions
					      WHERE pk_users_permissions IN ('.$subquery.')';
				$res = $this->slaveQuery(new MySQLiQuery($query));
			}
		} catch (Exception $e){
			echo $e;
		}
		return $res;
	}
	
	public function getUserGroupsPermissions($userid, $groupid=null){
		$res = false;
		try {
			if(!is_null($groupid) && !is_int($groupid)){
				throw new BaseException('$groupid, must be if type interger og null, '.gettype($groupid).' was given');
			} else if(!is_integer($userid)){
				throw new BaseException('$userid is of invalid datatype');
			} else {
				$subquery = 'SELECT GROUP_CONCAT(fk_users_permissions SEPARATOR \',\')
				             FROM tbl_users_has_groups
				             INNER JOIN tbl_users_groups ON tbl_users_has_groups.fk_users_groups=pk_users_groups
				             INNER JOIN tbl_users_groups_has_permissions ON tbl_users_groups_has_permissions.fk_users_groups=pk_users_groups
				             WHERE fk_users='.$userid;
				$query = 'SELECT pk_users_permissions, permission_ident
				          FROM tbl_users_permissions
				          WHERE pk_users_permissions IN ('.$subquery.')';
				$res = $this->slaveQuery(new MySQLiQuery($query));
			}
		} catch (Exception $e){
			echo $e;
		}
		return $res;		
	}
	*/
}
?>