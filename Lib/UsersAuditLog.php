<?php
abstract class UsersAuditLogger implements Singleton {
	abstract public function storeEvent(UsersAuditLogEvent $event, UserDecorator $user);
}

class UsersAuditLoggerDatabase extends UsersAuditLogger {
	private static $instance = null;
	
	private function __construct(){
		
	}	
	
	/**
	 *	@return UsersAuditLoggerDatabase
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new UsersAuditLoggerDatabase();
		}
		return self::$instance;	
	}	

	public function storeEvent(UsersAuditLogEvent $event, UserDecorator $user){
		
		
	}
}

class UsersAuditLoggerFile extends UsersAuditLogger {
	private static $instance = null;
	
	/**
	 * @var FileLog
	 */
	private $logfile = null;
	
	const UID_MAX_LEN = 11;
	const EVT_MAX_LEN = 11;
	
	private function __construct(){
		if(!defined('USERS_AUDIT_LOGGER_FILE')){
			define('USERS_AUDIT_LOGGER_FILE', 'var/log/auditlog');
		}
		$this->logfile = new FileLog(USERS_AUDIT_LOGGER_FILE);
	}	
	
	/**
	 *	@return UsersAuditLoggerFile
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new UsersAuditLoggerFile();
		}
		return self::$instance;	
	}
	
	public function storeEvent(UsersAuditLogEvent $event, UserDecorator $user){
		$uid_len = self::UID_MAX_LEN - strlen($user->getUID());
		if($uid_len > 0){
			$uid = str_repeat(0, $uid_len).$user->getUID();
		} else {
			$uid = $user->getUID();
		}
		$evt_len = self::EVT_MAX_LEN - strlen($event->getID());
		if($evt_len > 0){
			$evt = str_repeat(0, $evt_len).$event->getID();
		} else {
			$evt = $event->getID();
		}
		$this->logfile->putLogLine('[U'.$uid.'] [E'.$evt.'] '.$event->getDescription().'');
		return true;
	}
}

class UsersAuditLoggerUserFile extends UsersAuditLogger {
	private static $instance = null;
	
	private $logfile = null;
	
	const UID_MAX_LEN = 11;
	const EVT_MAX_LEN = 11;

	private function __construct(){
		if(!defined('USERS_AUDIT_LOGGER_DIR')){
			define('USERS_AUDIT_LOGGER_DIR', 'var/log/auditlogs');
		}
		if(!defined('USERS_AUDIT_LOGGER_DIR_FILENAME')){
			define('USERS_AUDIT_LOGGER_DIR_FILENAME', 'auditlog');
		}
	}
	
	/**
	 *	@return UsersAuditLoggerUserFile
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new UsersAuditLoggerUserFile();
		}
		return self::$instance;	
	}	
	public function storeEvent(UsersAuditLogEvent $event, UserDecorator $user){
		$uid_len = self::UID_MAX_LEN - strlen($user->getUID());
		if($uid_len > 0){
			$uid = str_repeat(0, $uid_len).$user->getUID();
		} else {
			$uid = $user->getUID();
		}		
		$evt_len = self::EVT_MAX_LEN - strlen($event->getID());
		if($evt_len > 0){
			$evt = str_repeat(0, $evt_len).$event->getID();
		} else {
			$evt = $event->getID();
		}
		if(!is_dir(USERS_AUDIT_LOGGER_DIR.'/'.$uid.'/')){
			mkdir(USERS_AUDIT_LOGGER_DIR.'/'.$uid.'/');
		}
		$this->logfile = new FileLog(USERS_AUDIT_LOGGER_DIR.'/'.$uid.'/'.USERS_AUDIT_LOGGER_DIR_FILENAME);
		$this->logfile->putLogLine('[E'.$evt.'] '.$event->getDescription().'');
		return true;
	}
}

class UsersAuditLog implements Singleton {
	private static $instance = null;
	
	/**
	 * @var UsersAuditLogger
	 */
	private $logobj = null;
	
	/**
	 * @var UsersAuthorization
	 */
	private $user = null;
	
	private function __construct(){
		if(!defined('USERS_AUDIT_LOGGER')){
			define('USERS_AUDIT_LOGGER', 'UsersAuditLoggerFile');
		}
		eval('$this->logobj = '.USERS_AUDIT_LOGGER.'::getInstance();');
		try {
			if(!$this->logobj instanceof UsersAuditLogger){
				throw new BaseException('Auditlogger object, is not a instance of UsersAuditLogger.');
			}
		} catch (BaseException $e){
			echo $e;
			exit;
		}
		$this->user = UsersAuthorization::getInstance();
	}
	
	public function attach(UsersAuditLogEvent $event){
		if($this->user->isAuthed()){
			return $this->logobj->storeEvent($event, $this->user);
		} else {
			return false;
		}
	}
	
	/**
	 *	@return UsersAuditLog
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new UsersAuditLog();
		}
		return self::$instance;	
	}
}

class UsersAuditLogEvent {
	private $id;
	private $desc;
	private $time;
	
	public function __construct($eventID, $eventDesc){
		$this->id = $eventID;
		$this->desc = $eventDesc;
		$this->time = time();
	}
	
	public function getDescription(){
		return $this->desc;
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function getTime(){
		return $this->time;
	}
}
?>