<?php
class MySQLi_User extends DatabaseDAO implements Singleton,DAO_User {
	private static $instance = null;
	
	/**
	 *	@return Database
	 */
	public static function &getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_User();
		}
		return self::$instance;	
	}
	
	
	public function ifEmailUsed($email, $id=null){
		$query = 'SELECT pk_users 
		          FROM tbl_users
		          WHERE email LIKE \''.$email.'\' ';
		if(!is_null($id)){
			$query .= ' AND pk_users !=\''.$id.'\'';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return true;	
		} else {
			return false;	
		}
	}
	
	public function ifUsernameUsed($username, $id=null){
		$query = 'SELECT pk_users 
		          FROM tbl_users
		          WHERE username LIKE \''.$username.'\' ';
		if(!is_null($id)){
			$query .= ' AND pk_users !=\''.$id.'\'';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return true;	
		} else {
			return false;	
		}
	}
	
	public function create($username, $password, $email, $created, $activation_string=null){
		if($this->ifUsernameUsed($username) || $this->ifEmailUsed($email)){
			return false;
		} else {
			if(is_null($activation_string)){
				$activation_string = 'NULL';	
			} else {
				$activation_string = '\''.$activation_string.'\'';	
			}
			$query = 'INSERT INTO tbl_users(username, password, email, create_timestamp, activation_string)
			          VALUES(\''.$username.'\', \''.$password.'\', \''.$email.'\', \''.$created.'\', '.$activation_string.')';
			$query = $this->masterQuery(new MySQLiQuery($query));
			return $query->getInsertID();
		}
	}
	
	public function getUserByName($username){
		$query = 'SELECT pk_users, username, password, email, activation_string, create_timestamp, last_timestamp
		          FROM tbl_users
		          WHERE username LIKE \''.$username.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}
	public function getUserByEmail($email){
		$query = 'SELECT pk_users, username, password, email, activation_string, create_timestamp, last_timestamp
		          FROM tbl_users
		          WHERE email=\''.$email.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}
	public function getUserById($id){
		$query = 'SELECT pk_users, username, password, email, activation_string, create_timestamp, last_timestamp
		          FROM tbl_users
		          WHERE pk_users=\''.$id.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		return $query->fetchArray();
	}
	
	public function validate($string){
		$query = 'UPDATE tbl_users SET activation_string = NULL
		          WHERE activation_string=\''.$string.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function update($id, $username, $password, $email){
		$query = 'UPDATE tbl_users
		          SET username=\''.$username.'\',
		              password=\''.$password.'\',
		              email=\''.$email.'\'
		          WHERE pk_users=\''.$id.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {        
			return false;
			
		}
	}
	
	public function delete($id){
		$query = "UPDATE tbl_users SET activation_string = 'deleted' WHERE pk_users = ".$id;

		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {        
			return false;
		}
	}
}
?>