<?php
class MySQLi_User extends DatabaseDAO implements Singleton,DAO_User {
	private static $instance = null;
	
	const SELECT_COLUMNS = 'pk_users, 
	                        username, 
	                        password, 
	                        email, 
	                        IF(activated=\'TRUE\', true, false) as activated, 
	                        activation_string, 
	                        UNIX_TIMESTAMP(create_timestamp) AS create_timestamp, 
	                        UNIX_TIMESTAMP(last_timestamp) AS last_timestamp'; 
	
	/**
	 *	@return MySQLi_User
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_User();
		}
		return self::$instance;	
	}

	public function isEmailAvailable($id, $email){
		$query = 'SELECT pk_users 
		          FROM tbl_users
		          WHERE email LIKE \''.$email.'\' ';
		if(!is_null($id)){
			$query .= ' AND pk_users !=\''.$id.'\'';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return false;	
		} else {
			return true;	
		}
	}
	public function isUsernameAvailable($id, $username){
		$query = 'SELECT pk_users 
		          FROM tbl_users
		          WHERE username LIKE \''.$username.'\' ';
		if(!is_null($id)){
			$query .= ' AND pk_users !=\''.$id.'\'';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return false;	
		} else {
			return true;	
		}
	}	
	
	public function getByUsername($username, $checkvalid=true){
		$query = 'SELECT '.self::SELECT_COLUMNS.'
		          FROM tbl_users
		          WHERE username LIKE \''.$username.'\'';
		if($checkvalid){
			$query .= ' AND activated=\'TRUE\' ';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}	
	public function getByEmail($email, $checkvalid=true){
		$query = 'SELECT '.self::SELECT_COLUMNS.'
		          FROM tbl_users
		          WHERE email LIKE \''.$email.'\'';
		if($checkvalid){
			$query .= ' AND activated=\'TRUE\' ';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}	
	public function create($username, $email, $password, $activated, $activation_string){
		$this->startTransaction();
		if(!$this->isUsernameAvailable(null, $username) || !$this->isEmailAvailable(null, $email)){
			$this->rollback();
			return false;
		} else {
			$activation_string = MySQLiTools::parseNullValue($activation_string);
			$activated = MySQLiTools::parseBooleanValue($activated);
			$query = 'INSERT INTO tbl_users(username, password, email, activated, activation_string)
			          VALUES(\''.$username.'\', \''.$password.'\', \''.$email.'\', '.$activated.', '.$activation_string.')';
			$query = $this->masterQuery(new MySQLiQuery($query));
			if($id = (int) $query->getInsertID()){
				$this->commit();
				return $id;
			} else {
				$this->rollback();	
				return false;	 
			}
		}
	}
	public function update($id, $username, $email, $password, $activated, $activation_string, $last_timestamp){
		
		$this->startTransaction();
		if(!$this->isUsernameAvailable($id, $username) || !$this->isEmailAvailable($id, $email)){
			$this->rollback();
			return false;
		} else {
			$activated = MySQLiTools::parseBooleanValue($activated);
			$last_timestamp = MySQLiTools::parseNullValue($last_timestamp);
			$activation_string = MySQLiTools::parseNullValue($activation_string);
			$query = 'UPDATE tbl_users
			          SET username=\''.$username.'\',
			              email=\''.$email.'\',
			              password=\''.$password.'\',
			              activated='.$activated.',
			              activation_string='.$activation_string.',
			              last_timestamp=IF('.$last_timestamp.' IS NULL, NULL, FROM_UNIXTIME('.$last_timestamp.'))
			          WHERE pk_users=\''.$id.'\'';
			$query = $this->masterQuery(new MySQLiQuery($query));
			
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
		          FROM tbl_users
		          WHERE pk_users=\''.$id.'\'
		            AND deleted=\'FALSE\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}	
	public function delete($id, $permanent=false){
		if(!$permanent){
			$query = 'UPDATE tbl_users
			          SET deleted=\'TRUE\'
			          WHERE pk_users=\''.$id.'\'';
		} else {
			$query = 'DELETE FROM tbl_users
			          WHERE pk_users=\''.$id.'\'';
		}
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;	 
		}
	}
}
?>