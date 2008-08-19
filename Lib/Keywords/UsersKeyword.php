<?php
abstract class UsersKeywordModify implements Event {
	/**
	 * @var UsersKeyword
	 */
	protected $controller;
	
	function __construct(UsersKeyword $controller){
		$this->controller = $controller;
	}
		
	public function getID(){
		return $this->controller->getID();
	}
	
	public function getObj(){
		return $this->controller;
	}
}
class UsersKeywordModifyBeforeCommit extends UsersKeywordModify  {
	
}
class UsersKeywordModifyBeforeDelete extends UsersKeywordModify  {
	
}
class UsersKeywordModifyAfterCommit extends UsersKeywordModify  {
	
}
class UsersKeywordModifyAfterDelete extends UsersKeywordModify  {
	
}

interface DAO_UsersKeyword {
	/**
	 * @return boolean true on success, else return false
	 */	
	public function update($userid, $keywordid);
	/**
	 * @return array on success, else return false
	 */
	public function read($userid, $keywordid);
	/**
	 * @return boolean true on success, else return false
	 */
	public function delete($userid, $keywordid);
}

class UsersKeyword extends UserComponent implements Output {
	/**
	 * @var Keyword
	 */
	private $keyword = null;
	
	private $dao = null;
	
	const FIELD_USER_ID = 'fk_users';
	const FIELD_KEYWORD_ID = 'fk_keywords';
	
	const DAO = 'UsersKeyword';

	public function __construct($id=null, $array = array()){
		$this->keyword = new Keyword($id, $array);
		if(sizeof($array) > 0){
			$this->_setFromArray($array);
		}
	}
		
	public function getByKeyword($keyword){
		return $this->keyword->getByKeyword($keyword);
	}
	
	public function setKeyword($keyword){
		return $this->keyword->setKeyword($keyword);
	}
	
	public function getKeyword(){
		return $this->keyword->getKeyword();
	}
	
	public function getID(){
		return $this->keyword->getID();
	}
	
	public function delete(){
		return $this->dao->delete($this->getUserID(), $this->keyword->getID());
	}
	public function read(){
		return $this->_read();
	}
		
	public function commit(){
		$event = EventHandler::getInstance();
		$this->_getDAO();
		try {
			if(is_null($this->keyword->getID())){
				$this->keyword->commit();
			} 
			$event->triggerEvent(new UsersKeywordModifyBeforeCommit($this));
			$r = $this->_update();
			if($r){
				$event->triggerEvent(new UsersKeywordModifyAfterCommit($this));
			}
			return $r;
		} catch (BaseException $e){
			echo $e;
			exit;
		}
		return false;
	}

	public function getXML(DOMDocument $xml){
		return $this->keyword->getXML($xml);
	}
	public function &getArray(){ 
		return $this->keyword->getArray();
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
		if($this->dao->update($this->getUserID(), $this->keyword->getID())){
			return true;
		} else {
			return false;
		}
	}
	protected function _setFromArray($array){
		if(!is_null($array[self::FIELD_KEYWORD_ID])){
			$this->keyword = new Keyword($array[self::FIELD_KEYWORD_ID], $array);
		}
	}
	protected function _read(){
		$this->_getDAO(false);
		if($array = $this->dao->read($this->getID(), $this->keyword->getID())){
			$this->_setFromArray($array);
			return true;
		} else {
			return false;
		}
	}	
}
?>