<?php
class UserSettingsStoreEvent implements EventTypeHandler,Observer {
	private $subject = null;
	
	public function getEventType(){
		return 'EventRequestEnd';	
	}	
	public function register(ObserverSubject $subject){
		$this->subject = $subject;
	}
	public function update($update){
		$settings = UserSettings::getInstance();
		$settings->store();
	}
}

class UserSettingsPutSettingsXML implements EventTypeHandler,Observer {
	private $subject = null;
	
	public function getEventType(){
		return 'EventApplyDefaultSettings';	
	}	
	public function register(ObserverSubject $subject){
		$this->subject = $subject;
	}
	/**
	 * @param EventApplyDefaultSettings $update
	 */
	public function update($update){
		$update->getPage()->addSettings(UserSettings::getInstance());
	}
}

class UserSettings implements Singleton,Output {
	private static $instance = null;
	
	private $settings = array();
	private $changed = array();
	/**
	 * @var User
	 */
	private $user = null; 
	
	/**
	 *	@return UserSettings
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			$session = SessionHandler::getInstance();
			if($session->check(__CLASS__)){
				self::$instance	= unserialize($session->get(__CLASS__));
			} else {
				self::$instance = new UserSettings();
			}
		}
		return self::$instance;	
	}
	private function __construct(){
		$this->user = UsersAuthorization::getInstance()->getUser();
	}

	public function __wakeup(){
		if($this->user->getUserID() != UsersAuthorization::getInstance()->getUID()){
			$this->reset();
		}
		$this->user = UsersAuthorization::getInstance()->getUser();
	}
	
	public function reset(){
		$session = SessionHandler::getInstance();
		$session->remove(__CLASS__);
		$this->settings = array();
		$this->changed = array();		
	}

	public function store(){
		if($this->user){
			$this->user->removeComponents();
			
			foreach ($this->changed as $change){
				$change->commit();
			}
			$this->changed = array();	
			
			$session = SessionHandler::getInstance();
			$session->set(__CLASS__, serialize($this));
		}
	}
	
	public function delete($ident){
		if(isset($this->settings[$ident])){
			$this->settings[$ident]->delete();
			unset($this->settings[$ident]);
		}
		return true;
	}
	
	public function get($ident, $default=null){
		if(isset($this->settings[$ident])){
			return $this->settings[$ident]->getValue();
		} else if($this->user) {
			$this->settings[$ident] = new UserSetting($this->user->getID(), $ident);
			if(!$this->settings[$ident]->read()){
				$this->settings[$ident]->setValue($default);	
			}
			return $this->settings[$ident]->getValue();
		} else {
			return $default;
		}
	}

	public function set($ident, $value){
		if(!isset($this->settings[$ident]) || $this->settings[$ident]->getValue() != $value){
			$this->settings[$ident] = new UserSetting($this->user->getID(), $ident);
			$this->settings[$ident]->setValue($value);
			$this->changed[$ident] = $this->settings[$ident];
		}
		return true;
	}

	public function getXML(DOMDocument $xml){
		$settings = $xml->createElement('usersettings');
		foreach ($this->settings as $setting){
			$settings->appendChild($setting->getXML($xml));
		}
		return $settings;
	}

}

$eventHandler = EventHandler::getInstance();
$eventHandler->registerObserver(new UserSettingsStoreEvent());
$eventHandler->registerObserver(new UserSettingsPutSettingsXML());
?>