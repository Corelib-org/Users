<?php
interface DAO_UserAuthorization {
	public function getUserPermissions($userid);
	public function getUserReversePermissions($userid);
	public function getUserRolesPermissions($userid, $roleid=null);
	public function getUserGroupsPermissions($userid, $groupid=null);
}

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
	/**
	 * @param EventApplyDefaultSettings $update
	 */
	public function update($update){
		$auth = UsersAuthorization::getInstance();
		if($auth->isAuthed()){
			$page = $update->getPage();
			$page->addSettings($auth);
		}
	}
}

class UsersAuthorization extends UserDecorator implements Singleton,Output {
	private static $instance = null;
	
	private $auth = false;
	private $ip = null;
	private $permissions = array();
	
	/**
	 * @var DAO_UserAuthorization
	 */
	protected $dao = null;
	
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
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(__CLASS__);
		}
		$this->permissions = array('LOGGED_IN');
		$res = $this->dao->getUserGroupsPermissions($this->decorator->getUID());
		while($out = $res->fetchArray()){
			$this->permissions[$out['pk_users_permissions']] = $out['permission_ident'];
		}
		$res = $this->dao->getUserPermissions($this->decorator->getUID());
		while($out = $res->fetchArray()){
			$this->permissions[$out['pk_users_permissions']] = $out['permission_ident'];
		}
/*		$res = $this->dao->getUserRolesPermissions($this->decorator->getUID());
		while($out = $res->fetchArray()){
			$this->permissions[$out['pk_users_permissions']] = $out['permission_ident'];
		} */
		$res = $this->dao->getUserReversePermissions($this->decorator->getUID());
		while($out = $res->fetchArray()){
			if(isset($this->permissions[$out['pk_users_permissions']])){
				unset($this->permissions[$out['pk_users_permissions']]);
			}
		}
	}
	public function isAuthed(){
		return $this->auth;
	}
	public function login($password, $hash=false){
		$act = $this->decorator->getActivationString();
		if(!$hash){
			$password = sha1($password);
		}
		if($password == $this->decorator->getPassword() && empty($act)){
			$this->auth = true;
			$this->ip = $_SERVER['REMOTE_ADDR'];
			$this->reloadPermissions();
			$this->store();
			$this->decorator->updateLastTimestamp();
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
	public function checkPermissions($item1=null, $item2=null, $item3=null){
		$array = func_get_args();
		while(list($key, $val) = each($array)){
			if(array_search($val, $this->permissions)){
				return true;
			}
		}
		return false;
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
	public function &getArray(){
		$user = $this->decorator->getArray();
		$user['user']['permissions'] = array();
		while(list($key, $val) = each($this->permissions)){
			$user['user']['permissions'][$key] = $val;
		}
		return $user;		
	}
	public function getString($format = '%1$s'){}
}

$eventHandler = EventHandler::getInstance();
$eventHandler->registerObserver(new UsersAuthorizationConfirmEvent());
$eventHandler->registerObserver(new UsersAuthorizationStoreEvent());
$eventHandler->registerObserver(new UsersAuthorizationPutSettingsXML());
?>