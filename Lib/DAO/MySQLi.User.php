<?php
class MySQLi_User extends DatabaseDAO implements Singleton,DAO_User {
	private static $instance = null;
	
	private $select_columns = 'pk_users, 
	                           username, 
	                           password, 
	                           email, 
	                           IF(activated=\'TRUE\', true, false) as activated, 
	                           activation_string, 
	                           UNIX_TIMESTAMP(create_timestamp) AS create_timestamp, 
	                           UNIX_TIMESTAMP(last_timestamp) AS last_timestamp'; 
	
	/**
	 *	@return Database
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
		$query = 'SELECT '.$this->select_columns.'
		          FROM tbl_users
		          WHERE username LIKE \''.$username.'\'';
		if($checkvalid){
			$query .= ' AND activated=\'TRUE\' ';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}	
	public function getByEmail($email, $checkvalid=true){
		$query = 'SELECT '.$this->select_columns.'
		          FROM tbl_users
		          WHERE email LIKE \''.$email.'\'';
		if($checkvalid){
			$query .= ' AND activated=\'TRUE\' ';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}	
	public function create($username, $email, $password, $activation_string){
		$this->startTransaction();
		if(!$this->isUsernameAvailable(null, $username) || !$this->isEmailAvailable(null, $email)){
			$this->rollback();
			return false;
		} else {
			$activation_string = $this->_parseNullValue($activation_string);
			$query = 'INSERT INTO tbl_users(username, password, email, activation_string)
			          VALUES(\''.$username.'\', \''.$password.'\', \''.$email.'\', '.$activation_string.')';
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
	public function update($id, $username, $email, $password, $activated, $last_timestamp){
		$this->startTransaction();
		if(!$this->isUsernameAvailable($id, $username) || !$this->isEmailAvailable($id, $email)){
			$this->rollback();
			return false;
		} else {
			$activated = $this->_parseBooleanValue($activated);
			$query = 'UPDATE tbl_users
			          SET username=\''.$username.'\',
			              email=\''.$email.'\',
			              password=\''.$password.'\',
			              activated='.$activated.',
			              last_timestamp=FROM_UNIXTIME(\''.$last_timestamp.'\')
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
		$query = 'SELECT '.$this->select_columns.'
		          FROM tbl_users
		          WHERE pk_users=\''.$id.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}	
	public function delete($id){
		$query = 'UPDATE tbl_users
		          SET activated=\'FALSE\',
		              activation_string=\'DELETED\'
		          WHERE pk_users=\''.$id.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;	 
		}
	}
}
?>