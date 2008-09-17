<?php
abstract class UserPermissionModify implements Event {
	/**
	 * @var UserPermission
	 */
	protected $controller;
	
	function __construct(UserPermission $controller){
		$this->controller = $controller;
	}
		
	public function getID(){
		return $this->controller->getID();
	}
	
	public function getObj(){
		return $this->controller;
	}
}
class UserPermissionModifyBeforeCommit extends UserPermissionModify  {
	
}
class UserPermissionModifyBeforeDelete extends UserPermissionModify  {
	
}
class UserPermissionModifyAfterCommit extends UserPermissionModify  {
	
}
class UserPermissionModifyAfterDelete extends UserPermissionModify  {
	
}

interface DAO_UserPermission {
	public function isIdentAvailable($id, $ident);
	public function getByIdent($ident);
	/**
 	* @return integer id on success, else return false
 	*/
	public function create($ident, $title);
	/**
	 * @return boolean true on success, else return false
	 */	
	public function update($id, $ident, $title);
	/**
	 * @return array on success, else return false
	 */
	public function read($id);
	/**
	 * @return boolean true on success, else return false
	 */
	public function delete($id);
}

class UserPermission extends UserComponent implements Output {
	private $id = null;
	private $ident = null;
	private $title = null;
	
	private $dao = null;
	
	const FIELD_ID = 'pk_users_permissions';
	const FIELD_IDENT = 'ident';
	const FIELD_TITLE = 'title';
	
	const DAO = 'UserPermission';

	public function __construct($id = null, $array = array()){
		$this->id = $id;
		if(sizeof($array) > 0){
			$this->_setFromArray($array);
		}
	}
	
	public function getByIdent($ident){
		$this->_getDAO(false);
		$this->_setFromArray($this->dao->getByIdent($ident));
		if(is_null($this->id)){
			return false;	
		} else {
			return true;	
		}
	}		
	
	public function getID(){
		return $this->id;
	}
		
	public function setTitle($title){
		$this->title = $title;
	}
	
	public function setIdent($ident){
		$this->_getDAO();
		if($this->dao->isIdentAvailable($this->id, $ident)){
			$this->ident = $ident;
			return true;
		} else {
			return false;
		}
	}	
	
	public function delete(){
		return $this->dao->delete($this->id);
	}
	
	public function read(){
		$this->_getDAO(false);
		if($array = $this->dao->read($this->id)){
			$this->_setFromArray($array);
			return true;
		} else {
			return false;
		}
	}
		
	public function commit(){
		$event = EventHandler::getInstance();
		$this->_getDAO();
		try {
			$event->triggerEvent(new UserPermissionModifyBeforeCommit($this));
			if(is_null($this->id)){
				$r = $this->_create();
			} else {
				$this->_update();
				$r = true;
			}
			if($r){
				$event->triggerEvent(new UserPermissionModifyAfterCommit($this));
			}
			return $r;
		} catch (BaseException $e){
			echo $e;
			exit;
		}
		return false;
	}

	public function getXML(DOMDocument $xml){
		$permission = $xml->createElement('permission', $this->title);
		$permission->setAttribute('id', $this->id);
		$permission->setAttribute('ident', $this->ident);
		return $permission;
	}
	public function &getArray(){ 
		$permission = array('permission' => array('id'=>$this->id, 'ident'=>$this->ident, 'title'=>$this->title));
		return $permission;
	}

	protected function _getDAO($read=true){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::DAO);
			if($read && !is_null($this->id)){
				$this->read();
			}
		}
		return true;
	}
	protected function _create(){
		if($this->id = $this->dao->create($this->ident, $this->title)){
			$this->read();
			return true;
		} else {
			return false;
		}		
	}
	protected function _update(){
		if($this->dao->update($this->id, $this->ident, $this->title)){
			$this->read();
			return true;
		} else {
			return false;
		}
	}
	
	protected function _setFromArray($array){
		if(!is_null($array[self::FIELD_ID])){
			$this->id = (int) $array[self::FIELD_ID];
		}
		if(isset($array[self::FIELD_IDENT])){
			$this->ident = $array[self::FIELD_IDENT];
		}
		if(isset($array[self::FIELD_TITLE])){
			$this->title = $array[self::FIELD_TITLE];
		}
	}
}
?>