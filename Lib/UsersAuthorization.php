<?php

class UsersAuthorizationConfirmEvent implements EventTypeHandler,Observer  {
	private $subject = null;
	
	public function getEventType(){
		return 'EventRequestStart';	
	}	
	
	public function register(ObserverSubject &$subject){
		$this->subject = $subject;
	}
	
	public function update($update){
		UsersAuthorization::getInstance();
	}
}

class UsersAuthorizationStoreEvent implements EventTypeHandler,Observer  {
	private $subject = null;
	
	public function getEventType(){
		return 'EventRequestEnd';	
	}	
	
	public function register(ObserverSubject &$subject){
		$this->subject = $subject;
	}
	
	public function update($update){
		$auth = UsersAuthorization::getInstance();
		$auth->store();
	}
}

class UsersAuthorizationPutSettingsXML implements EventTypeHandler,Observer  {
	private $subject = null;
	
	public function getEventType(){
		return 'EventApplyDefaultSettings';	
	}	
	
	public function register(ObserverSubject &$subject){
		$this->subject = $subject;
	}
	
	public function update($update){
		
		$DOMSettings = $update->getSettings();
		$XML = $update->getXML();
		
		$auth = UsersAuthorization::getInstance();
		if($auth->isAuthed()){
			$DOMSettings->appendChild($auth->getXML($XML));
		}
	}
}

class UsersAuthorization extends UserDecorator implements Singleton {
	private static $instance = null;
	
	private $auth = false;
	private $ip = null;
	private $permissions = array();
	
	/**
	 *	@return UsersAuthorization
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			$session = SessionHandler::getInstance();
			if($session->check(__CLASS__)){
				self::$instance	= unserialize($session->get(__CLASS__));
			} else {
				self::$instance = new UsersAuthorization();
			}
		}
		return self::$instance;	
	}
	
	private function __construct(){
		$this->confirm();
	}
	
	public function __sleep(){
		return array('decorator','auth','ip','permissions');	
	}

	public function __wakeup(){
		$this->confirm();
	}
	
	public function getXML(DOMDocument $xml){
		$DOMUser = $this->decorator->getXML($xml);
		$DOMPermissions = $DOMUser->appendChild($xml->createElement('permissions'));
		while(list($key, $val) = each($this->permissions)){
			$DOMPermission = $DOMPermissions->appendChild($xml->createElement('permission', $val));
			$DOMPermission->setAttribute('id', $key);
		}
		return $DOMUser;
	}
	
	public function confirm(){
		if($_SERVER['REMOTE_ADDR'] != $this->ip){
			$this->logout();	
		}
	}
	
	public function getUID(){
		return $this->decorator->getUID();	
	}
	
	public function reset(){
		$session = SessionHandler::getInstance();
		$session->remove(__CLASS__);
	}
	
	public function reloadPermissions(){
		$this->permissions = array('LOGGED_IN');
	}
	
	public function isAuthed(){
		return $this->auth;
	}
	
	public function login($password){
		$act = $this->decorator->getActivationString();
		if(sha1($password) == $this->decorator->getPassword() && empty($act)){
			$this->auth = true;
			$this->ip = $_SERVER['REMOTE_ADDR'];
			$this->reloadPermissions();
			$this->store();
			return true;
		} else {
			return false;	
		}
	}
	
	public function logout(){
		$this->decorator = null;
		$this->ip = null;
		$this->auth = false;
		$session = SessionHandler::getInstance();
		$session->remove(__CLASS__);
	}
	
	public function store(){
		$session = SessionHandler::getInstance();
		$session->set(__CLASS__, serialize($this));	
	}
}
?>