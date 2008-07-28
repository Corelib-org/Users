<?php
class MySQLi_UsersPermissions extends DatabaseDAO implements Singleton,DAO_UserPermissions {
	/**
	 * @var MySQLi_UsersPermissions
	 */
	private static $instance = null;
	
	/**
	 *	@return MySQLi_UsersPermissions
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersPermissions();
		}
		return self::$instance;	
	}
	
	public function getByIdent($ident){
		$query = 'SELECT '.self::SELECT_COLUMNS.'
		          FROM tbl_information
		          WHERE ident LIKE \''.$ident.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}			
	
	public function createPermission($ident, $name=null){
		if(is_null($name)){
			$name = 'NULL';
		} else {
			$name = '\''.$name.'\'';
		}
		$query = 'INSERT INTO tbl_users_permissions(permission_ident, permission_name)
		          VALUES(\''.$ident.'\', '.$name.')';
		$query = $this->masterQuery(new MySQLiQuery($query));
		return $query->getInsertID();
	}
	public function setPermissionInformation($id, $ident, $name=null){
		if(is_null($name)){
			$name = 'NULL';
		} else {
			$name = '\''.$name.'\'';
		}
		$query = 'UPDATE tbl_users_permissions
		          SET permission_ident=\''.$ident.'\',
		              permission_name='.$name.'
		          WHERE pk_users_permission='.$id;
		$query = $this->masterQuery(new MySQLiQuery($query));
		return true;
	}
	public function getPermissionById($id){
		$query = 'SELECT pk_users_permissions, permission_ident, permission_name
		          FROM tbl_users_permissions
		          WHERE pk_users_permissions='.$id;
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}
	
	public function grantPermission($userid, $id, $expire=null, $comment=null){
		if(is_null($expire)){
			$expire = 'NULL';
		} else {
			$expire = '\''.$expire.'\'';
		}		
		if(is_null($comment)){
			$comment = 'NULL';
		}		
		$query = 'REPLACE INTO tbl_users_has_permissions(fk_users, fk_users_permissions, expire, comment)
		          VALUES('.$userid.', '.$id.', FROM_UNIXTIME('.$expire.'), '.$comment.')';
		$query = $this->masterQuery(new MySQLiQuery($query));
		return true;
	}
	
	public function getPermissionExpirationTimestamp($userid, $id){
		$query = 'SELECT UNIX_TIMESTAMP(expire) as expire
		          FROM tbl_users_has_permissions
		          WHERE fk_users=\''.$userid.'\' AND fk_users_permissions=\''.$id.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		$query = $query->fetchArray();
		return $query['expire'];
	}
}
?>