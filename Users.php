<?php
abstract class UserModifyEvent implements Event {
	/**
	 * @var User
	 */
	protected $user;
	
	function __construct(UserComponent $user){		
		$this->user = $user;
	}
	
	public function getXML(DOMDocument $xml){
		return $this->user->getXML($xml);
	}
	
	public function getID(){
		return $this->user->getID();
	}
	
	public function getObj(){
		return $this->user;
	}
}
class UserModifyBeforeCommit extends UserModifyEvent {
	
}
class UserModifyBeforeDelete extends UserModifyEvent {
	
}
class UserModifyAfterCommit extends UserModifyEvent {
	
}
class UserModifyAfterDelete extends UserModifyEvent {
	
}

abstract class UserComponent implements Output  {
	/**
	 * Child UserComponents
	 * 
	 * @var Array instantiated components
	 */
	protected $components = array();
	/**
	 * Parent UserComponent
	 * 
	 * @var UserComponent parent component
	 */
	protected $parent = null;
		
	public function getID(){
		return $this->parent->getID();
	}
	public function getPassword(){
		return $this->parent->getPassword();
	}
		
	public function getComponentsXML(DOMDocument $xml, DOMElement $DOMnode){
		while(list(,$val) = each($this->components)){
			$DOMnode->appendChild($val->getXML($xml));
		}
		reset($this->components);
	}
	
	public function addComponent(UserComponent $component){
		$this->components[] = $component;
	}
}

interface DAO_User {
	/**
	 * @return on success, return new user id, else return false
	 */
	public function create($username, $email, $password, $activation_string);
	public function update($id, $username, $email, $password, $activated, $last_timestamp);
	/**
	 * Read userdata into user object
	 * 
	 * @return array on success, else return false
	 */
	public function read($id);
	public function delete($id);
	public function isUsernameAvailable($id, $username);
	public function isEmailAvailable($id, $email);
	public function getByUsername($username, $checkvalid=true);
	public function getByEmail($email, $checkvalid=true);	
}


class User extends UserComponent {
	/**
	 * @var integer user id
	 */
	private $id = null;
	private $username = null;
	private $email = null;
	private $password = null;	
	
	private $activated = false;
	private $activation_string = null;	
	
	private $last_timestamp = null;
	private $create_timestamp = null;
	
	/**
	 * @var DAO_User
	 */
	private $dao = null;
	
	public function __construct($id = null, $array=array()){
		$this->id = $id;
		if(sizeof($array) > 0){
			$this->_setFromArray($array);
		}
	}	
	
	public function getByUsername($username, $checkvalid=true){
		$this->_getDAO(false);
		$this->_setFromArray($this->dao->getByUsername($username, $checkvalid));
		if(is_null($this->id)){
			return false;	
		} else {
			return true;	
		}
	}
	public function getByEmail($email, $checkvalid=true){
		$this->_getDAO(false);
		$this->_setFromArray($this->dao->getByEmail($email, $checkvalid));
		if(is_null($this->id)){
			return false;	
		} else {
			return true;	
		}
	}	
	
	public function getID(){
		if(!is_null($this->id)){
			return $this->id;
		} else {
			return false;
		}
	}
	public function getPassword(){
		if(!is_null($this->password)){
			return $this->password;
		} else {
			return false;
		}
	}
	public function getUsername(){
		if(!is_null($this->username)){
			return $this->username;
		} else {
			return false;
		}
	}
	public function getEmail(){
		if(!is_null($this->email)){
			return $this->email;
		} else {
			return false;
		}
	}
	public function getLastTimestamp(){
		if(!is_null($this->last_timestamp)){
			return $this->last_timestamp;
		} else {
			return false;
		}
	}
	public function getCreateDate(){
		if(!is_null($this->create_timestamp)){
			return $this->create_timestamp;
		} else {
			return false;
		}
	}
		
	public function setUsername($username){
		$this->_getDAO();
		if($this->dao->isUsernameAvailable($this->id, $username)){
			$this->username = $username;
			return true;
		} else {
			return false;
		}
	}
	public function setEmail($email){
		$this->_getDAO();
		if($this->dao->isEmailAvailable($this->id, $email)){
			$this->email = $email;
			return true;
		} else {
			return false;
		}
	}	
	public function setPassword($password){
		$this->password = sha1($password);
	}
	public function setLastTimestamp($timestamp=null){
		if(is_null($timestamp)){
			$this->last_timestamp = time();	
		} else {
			$this->last_timestamp = $timestamp;
		}
	}
	
	public function commit(){
		$event = EventHandler::getInstance();
		$this->_getDAO();
		try {
			if(is_null($this->username) || is_null($this->email)){
				throw new BaseException('unable to modify user, username or email is null', E_USER_ERROR);		
			} else {
				$event->triggerEvent(new UserModifyBeforeCommit($this));
				if(is_null($this->id)){
					$r = $this->_create();
				} else {
					$r = $this->_update();
				}
				$event->triggerEvent(new UserModifyAfterCommit($this));
				return $r;
			}
		} catch (BaseException $e){
			echo $e;
			exit;
		}
		return false;
	}
	public function delete($permanent=false){
		// TODO make permanent deletion method
		return $this->dao->delete($this->id);
	}
	
	public function getXML(DOMDocument $xml){
		$user = $xml->createElement('user');
		$user->setAttribute('id', $this->getID());
		if(!is_null($this->getUsername())){
			$user->setAttribute('username', $this->getUsername());
		}
		if(!is_null($this->getEmail())){
			$user->setAttribute('email', $this->getEmail());
		}
		if(!is_null($this->getLastTimestamp())){
			$user->setAttribute('lastlogin', $this->getLastTimestamp());
		}
		if(!is_null($this->getCreateDate())){
			$user->setAttribute('create_timestamp', $this->getCreateDate());
		}
		return $user;
	}	
	public function &getArray(){ 
		$user = array();
		$user['id'] = $this->getID();
		if(!is_null($this->getUsername())){
			$user['username'] = $this->getUsername();
		}
		if(!is_null($this->getEmail())){
			$user['email'] = $this->getEmail();
		}
		if(!is_null($this->getLastLogin())){
			$user['lastlogin'] = $this->getLastLogin();
		}
		if(!is_null($this->getCreateDate())){
			$user['created'] = $this->getCreateDate();
		}
		$user = array('user'=>&$user);
		return $user;		
	}	
	
	private function _setFromArray($array){
		if(!is_null($array['pk_users'])){
			$this->id = (int) $array['pk_users'];
		}
		if(isset($array['email'])){
			$this->email = $array['email'];
		}
		if(isset($array['username'])){
			$this->username = $array['username'];
		}
		if(isset($array['password'])){
			$this->password = $array['password'];
		}
		if(isset($array['last_timestamp'])){
			$this->last_timestamp = $array['last_timestamp'];
		}
		if(isset($array['activation_string'])){
			$this->activation_string = $array['activation_string'];
		}
		if(isset($array['activated'])){
			$this->activated = (bool) $array['activated'];
		}
		if(isset($array['create_timestamp'])){
			$this->create_timestamp = $array['create_timestamp'];
		}
	}
	private function _getDAO($read=true){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('User');
			if($read){
				$this->_read();
			}
		}
		return true;
	}
	private function _read(){
		$this->_getDAO(false);
		if($array = $this->dao->read($this->id)){
			$this->_setFromArray($array);
		}
	}
	private function _create(){
		if($this->id = $this->dao->create($this->username, $this->email, $this->password, md5($this->username.$this->email))){
			$this->_read();
			return true;
		} else {
			return false;
		}		
	}
	private function _update(){
		if($this->dao->update($this->id, $this->username, $this->email, $this->password, $this->activated, $this->last_timestamp)){
			return true;
		} else {
			return false;
		}
	}
}
?>