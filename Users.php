<?php
class UserModifiedEvent implements Event {
	private $user;
	
	function __construct(UserDecorator $user){		
		$this->user = $user;
	}
	
	public function getXML(DOMDocument $xml){
		return $this->user->getXML($xml);
	}
	
	public function getUID(){
		return $this->user->getUID();
	}
	
	public function getUserObj(){
		return $this->user;
	}
}

abstract class UserDecorator {
	protected $decorator = null;
	/**
	 *	@var DAO_User
	 */
	protected $dao = null;
	
	abstract public function getXML(DOMDocument $xml);
	// abstract public function getUID();
	
	public function getUID(){
		return $this->decorator->getUID();
	}
	
	public function getDecorator(){
		return $this->decorator;
	}
	
	public function getUsername(){
		return $this->decorator->getUsername();
	}
	
	protected function buildXML(DOMDocument $xml, DOMElement $DOMNode){
		if(!is_null($this->decorator)){
			$DOMElement = $this->decorator->getXML($xml);
			$DOMElement->appendChild($DOMNode);
			return $DOMElement;
		} else {
			return $DOMNode;
		}
	}
	
	public function decorate(UserDecorator $decorator){
		$this->decorator = $decorator;
	}
	
	public function __sleep(){
		return array('decorator');
	}
}

interface DAO_User {
	/**
	 *	Create User
	 *
	 *	@param string $username
	 *	@param string $password
	 *	@param string $email
	 *	@param integer $created Unix timestamp for the time the user is created
	 *	@param string $activation_string Unique activation string for the user
	 */
	public function create($username, $password, $email, $created, $activation_string=null);
	public function ifEmailUsed($email, $id=null);
	public function ifUsernameUsed($username, $id=null);
	public function getUserByName($username);
	public function getUserByEmail($email);
	public function getUserById($id);
	public function validate($string);
	public function update($id, $username, $password, $email);
	public function updateLastTimestamp($id, $timestamp);
}

class User extends UserDecorator {
	private $action = null;
	
	private $id = null;
	private $username = null;
	private $email = null;
	private $email_old = null;
	private $password = null;
	private $lastlogin = null;
	private $created = null;
	private $activation_string = null;
	
	const ACTION_CREATE = 1;
	const ACTION_UPDATE = 2;
	
	const LIBRARY = 'Users';
	
	public function __sleep(){
		return array('decorator',
		             'id',
		             'action',
		             'username',
		             'email',
		             'password',
		             'lastlogin',
		             'created',
		             'activation_string');
	}
	
	public function __construct($id = null, $array=array()){
		$this->id = $id;
		if(!is_null($this->id) && sizeof($array) == 0){
			$this->_getById($this->id);
		} else if(!is_null($this->id)){
			$this->_setFromArray($array);
		}
	}
	
	public function updateLastTimestamp($timestamp=null){
		if(is_null($timestamp)){
			$timestamp = time();
		}
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}		
		return $this->dao->updateLastTimestamp($this->id, $timestamp);
	}
	
	
	public function create(){
		try {
			if(!is_null($this->id)){
				throw new BaseException('User ID is allready loaded, unable to create user');	
			} else {
				$this->action = self::ACTION_CREATE;
				return $this->commit();
			}
		} catch (BaseException $e) {
			echo $e;
		}
	}
	
	public function commit(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}
		switch ($this->action){
			case self::ACTION_CREATE:
				$this->created = time();
				return $this->_create();
				break;	
			case self::ACTION_UPDATE:
				return $this->_update();
				break;	
		}
	}
	
	private function _create(){
		$this->activation_string = sha1(time().serialize($this));
		$id = $this->dao->create($this->username, $this->password, $this->email, $this->created, $this->activation_string);
		if($id !== false){
			$this->id = $id;
		}
		return $id;
	}
	
	private function _update(){
		$this->dao->update($this->id, $this->username, $this->password, $this->email);
	}
	
	final private function _checkID(){
		try {
			if(is_null($this->id)){
				throw new BaseException('User ID not loaded, unable to get user informations');
			}
		} catch (BaseException $e){
			echo $e;
		}
	}
	
	private function _setUpdate(){
		$this->action = self::ACTION_UPDATE;
	}
	
	public function setUsername($username){
		$this->_setUpdate();
		$this->username = $username;
	}
	
	public function setEmail($email){
		$this->_setUpdate();
		if(!is_null($this->email)){
			$this->email_old = $this->email;
		}
		$this->email = $email;
	}
	
	public function setPassword($password){
		$this->_setUpdate();
		$this->password = sha1($password);
	}
	
	public function setLastLogin($timestamp){
		$this->_setUpdate();
		$this->lastlogin = $timestamp;
	}
	
	public function getUsername(){
		$this->_checkID();
		return $this->username;
	}
	
	public function getEmail(){
		$this->_checkID();
		return $this->email;
	}
	
	public function getLastLogin(){
		$this->_checkID();
		return $this->lastlogin;
	}

	public function getCreateDate(){
		$this->_checkID();
		return $this->created;
	}
	
	public function getPassword(){
		$this->_checkID();
		return $this->password;
	}
	
	public function getActivationString(){
		$this->_checkID();
		return $this->activation_string;
	}
	
	public function isEmailUsed(){
		if($this->email != $this->email_old){
			if(is_null($this->dao)){
				$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
			}
			return $this->dao->ifEmailUsed($this->email, $this->id);
		}
	}
	
	public function isUsernameUsed(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}
		return $this->dao->ifUsernameUsed($this->username, $this->id);
	}
	
	public function getXML(DOMDocument $xml){
		$user = $xml->createElement('user');
		$user->setAttribute('id', $this->getUID());
		if(!is_null($this->getUsername())){
			$user->setAttribute('username', $this->getUsername());
		}
		if(!is_null($this->getEmail())){
			$user->setAttribute('email', $this->getEmail());
		}
		if(!is_null($this->getLastLogin())){
			$user->setAttribute('lastlogin', $this->getLastLogin());
		}
		if(!is_null($this->getCreateDate())){
			$user->setAttribute('created', $this->getCreateDate());
		}
		return $user;
	}
	
	public function decorate(UserDecorator $decorator){
		try {
			throw new BaseException('User Object can\'t be decorated');
		} catch (BaseException $e){
			echo $e;	
		}
	}
	
	public function getUID(){
		return $this->id;
	}
	
	private function _setFromArray($array){
		if(!is_null($array['pk_users'])){
			$this->id = (int) $array['pk_users'];
		} else {
			$this->id = $array['pk_users'];
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
			$this->lastlogin = $array['last_timestamp'];
		}
		if(isset($array['activation_string'])){
			$this->activation_string = $array['activation_string'];
		}
		if(isset($array['create_timestamp'])){
			$this->created = $array['create_timestamp'];
		}
		
	}
	
	public function getByUsername($username){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}	
		$this->_setFromArray($this->dao->getUserByName($username));
		if(is_null($this->id)){
			return false;	
		} else {
			return true;	
		}
	}
	
	public function getByEmail($email){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}			
		$this->_setFromArray($this->dao->getUserByEmail($email));
		if(is_null($this->id)){
			return false;	
		} else {
			return true;	
		}
	}
	
	private function _getById(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}			
		$this->_setFromArray($this->dao->getUserById($this->id));
		if(is_null($this->id)){
			return false;	
		} else {
			return true;	
		}
	}
	
	public function validate($string){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}
		$this->activation_string = null;
		return $this->dao->validate($string);
	}
	
	public function delete(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, __CLASS__);
		}
		return $this->dao->delete($this->id);
	}
}
?>