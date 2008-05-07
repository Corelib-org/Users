<?php
class MySQLi_UsersInformation extends DatabaseDAO implements Singleton,DAO_UsersInformation {
	private static $instance = null;
	
	const SELECT_COLUMNS = 'fk_users,
	                        fk_information,
	                        value';
	
	/**
	 *	@return MySQLi_UsersInformation
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UsersInformation();
		}
		return self::$instance;	
	}
	
	public function update($userid, $infoid, $value=null){
		$this->startTransaction();
		
		$query = 'SELECT '.UsersInformation::FIELD_VALUE.'
		          FROM tbl_users_has_information
		          WHERE '.UsersInformation::FIELD_USER_ID.'=\''.$userid.'\' 
		            AND '.UsersInformation::FIELD_INFOMATION_ID.'=\''.$infoid.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		
		if($query->getNumRows() > 0){
			$query = 'UPDATE tbl_users_has_information
			          SET '.UsersInformation::FIELD_VALUE.'=\''.$value.'\'
			          WHERE '.UsersInformation::FIELD_USER_ID.'=\''.$userid.'\' 
			            AND '.UsersInformation::FIELD_INFOMATION_ID.'=\''.$infoid.'\'';
		} else {
			$query = 'INSERT INTO tbl_users_has_information ('.UsersInformation::FIELD_USER_ID.', 
			                                                 '.UsersInformation::FIELD_INFOMATION_ID.', 
			                                                 '.UsersInformation::FIELD_VALUE.')
			          VALUES(\''.$userid.'\',
			                 \''.$infoid.'\',
			                 '.MySQLiTools::parseNullValue($value).')';
		}
		$query = $this->masterQuery(new MySQLiQuery($query));
			
		if($query->getAffectedRows() > 0){
			$this->commit();
			return true;
		} else {
			$this->rollback();	
			return false;	 
		}
	}
	
	public function read($userid, $infoid){
		$query = 'SELECT '.MySQLi_UsersInformation::SELECT_COLUMNS.'
		          FROM tbl_users_has_information
		          WHERE '.UsersInformation::FIELD_USER_ID.'=\''.$userid.'\' 
		            AND '.UsersInformation::FIELD_INFOMATION_ID.'=\''.$infoid.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		
		return $query->fetchArray();
	}
	
	public function delete($userid, $infoid){
		$query = 'DELETE FROM tbl_users_has_information
		          WHERE '.UsersInformation::FIELD_USER_ID.'=\''.$userid.'\' 
		            AND '.UsersInformation::FIELD_INFOMATION_ID.'=\''.$infoid.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
			
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;	 
		}
	}
}
?>