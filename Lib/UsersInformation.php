<?php
abstract class UsersInformationModify implements Event {
	/**
	 * @var UsersInformation
	 */
	protected $controller;
	
	function __construct(UsersInformation $controller){
		$this->controller = $controller;
	}
		
	public function getID(){
		return $this->controller->getID();
	}
	
	public function getObj(){
		return $this->controller;
	}
}
class UsersInformationModifyBeforeCommit extends UsersInformationModify  {
	
}
class UsersInformationModifyBeforeDelete extends UsersInformationModify  {
	
}
class UsersInformationModifyAfterCommit extends UsersInformationModify  {
	
}
class UsersInformationModifyAfterDelete extends UsersInformationModify  {
	
}

interface DAO_UsersInformation {
	/**
	 * @return boolean true on success, else return false
	 */	
	public function update($userid, $infoid, $value=null);
	/**
	 * @return array on success, else return false
	 */
	public function read($userid, $infoid);
	/**
	 * @return boolean true on success, else return false
	 */
	public function delete($userid, $infoid);
}

class UsersInformation extends UserComponent implements Output {
	/**
	 * @var Information
	 */
	private $information = null;
	private $value = null;
	
	private $dao = null;
	
	const FIELD_USER_ID = 'fk_users';
	const FIELD_INFOMATION_ID = 'fk_information';
	const FIELD_VALUE = 'value';
	
	const DAO = 'UsersInformation';

	public function __construct($id, $array = array()){
		$this->information = new Information($id);
		if(sizeof($array) > 0){
			$this->_setFromArray($array);
		}
	}
		
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}
	
	public function delete(){
		return $this->dao->delete($this->getID(), $this->information->getID());
	}
	public function read(){
		return $this->_read();
	}
		
	public function commit(){
		$event = EventHandler::getInstance();
		$this->_getDAO();
		try {
			$event->triggerEvent(new UsersInformationModifyBeforeCommit($this));
			$r = $this->_update();
			$event->triggerEvent(new UsersInformationModifyAfterCommit($this));
			return $r;
		} catch (BaseException $e){
			echo $e;
			exit;
		}
		return false;
	}

	public function getXML(DOMDocument $xml){
		$info = $this->information->getXML($xml);
		if(!is_null($this->value)){
			$info->appendChild($xml->createElement('value', $this->value));
		}
		return $info;
	}
	public function &getArray(){ 

	}

	protected function _getDAO($read=true){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::DAO);
			if($read){
				$this->_read();
			}
		}
		return true;
	}

	protected function _update(){
		if($this->dao->update($this->getID(), $this->information->getID(), $this->value)){
			return true;
		} else {
			return false;
		}
	}
	protected function _setFromArray($array){
		if(!is_null($array[self::FIELD_VALUE])){
			$this->value = $array[self::FIELD_VALUE];
		}
		if(!is_null($array[self::FIELD_INFOMATION_ID])){
			$this->information = new Information($array[self::FIELD_INFOMATION_ID]);
		}
	}
	protected function _read(){
		$this->_getDAO(false);
		if($array = $this->dao->read($this->getID(), $this->information->getID())){
			$this->_setFromArray($array);
			return true;
		} else {
			return false;
		}
	}	
}
?>