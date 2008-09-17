<?php
class MySQLi_UserPermission extends DatabaseDAO implements Singleton,DAO_UserPermission {
	private static $instance = null;
	
	const SELECT_COLUMNS = 'pk_users_permissions,
	                        ident,
	                        title';
	
	/**
	 *	@return MySQLi_UserPermission
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UserPermission();
		}
		return self::$instance;	
	}

	public function getByIdent($ident){
		$query = 'SELECT '.self::SELECT_COLUMNS.'
		          FROM tbl_users_permissions
		          WHERE ident LIKE \''.$ident.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}		
	
	public function isIdentAvailable($id, $ident){
		$query = 'SELECT pk_users_permissions 
		          FROM tbl_users_permissions
		          WHERE ident LIKE \''.$ident.'\' ';
		if(!is_null($id)){
			$query .= ' AND pk_users_permissions !=\''.$id.'\'';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return false;	
		} else {
			return true;	
		}
	}		
	
	public function create($ident, $title){
		$this->startTransaction();
		if(!$this->isIdentAvailable(null, $ident)){
			$this->rollback();
			return false;
		} else {		
			$query = MySQLiTools::makeInsertStatement('tbl_users_permissions', array(UserPermission::FIELD_IDENT, UserPermission::FIELD_TITLE));
			$query = $this->masterQuery(new MySQLiQueryStatement($query, $ident, $title));
				
			if($id = (int) $query->getInsertID()){
				$this->commit();
				return $id;
			} else {
				$this->rollback();	
				return false;	 
			}
		}
	}
	
	public function update($id, $ident, $title){
		$this->startTransaction();
		if(!$this->isIdentAvailable($id, $ident)){
			$this->rollback();
			return false;
		} else {
			

			$query = MySQLiTools::makeUpdateStatement('tbl_users_permissions', 
			                                          array(UserPermission::FIELD_IDENT, UserPermission::FIELD_TITLE),
			                                          'WHERE '.UserPermission::FIELD_ID.'=?');
			
			$query = $this->masterQuery(new MySQLiQueryStatement($query, $ident, $title, $id));
	
			if($query->getAffectedRows() > 0){
				$this->commit();
				return true;
			} else {
				$this->rollback();	
				return false;	 
			}
		}
	}
	public function read($id){
	
		$query = 'SELECT '.self::SELECT_COLUMNS.'
		          FROM tbl_users_permissions
		          WHERE '.UserPermission::FIELD_ID.'=\''.$id.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		
		return $query->fetchArray();
	}	
	public function delete($id){
	
		$query = 'DELETE FROM tbl_users_permissions
		          WHERE '.UserPermission::FIELD_ID.'=\''.$id.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
			
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;	 
		}
	}
}
?>