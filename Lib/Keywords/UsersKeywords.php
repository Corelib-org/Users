<?php
interface DAO_UsersKeywords {
	public function getList(DatabaseListHelperFilter $filter);
	public function getListCount(DatabaseListHelperFilter $filter);
	public function removeAllKeywords($userid);
}

class UsersKeywords extends UserComponent implements Output  {
	
	/**
	 * @var DatabaseListHelperFilter
	 */
	protected $filter = null;
	
	/**
	 * @var DAO_UsersInformationList
	 */
	private $dao = null;
	
	private $keywords = array();
	
	public function __construct($item=null /*, [$items...] */){
		$this->filter = new DatabaseListHelperFilter();
		$items = func_get_args();
		if(count($items) > 0){
			$this->filter->set(UsersInformation::FIELD_INFOMATION_ID, $items);
		}		
	}
	
	public function addKeywords($string, $seperator = null){
		$keywords = KeywordTools::parseKeywordList($string, $seperator);
		foreach ($keywords as $keyword){
			$k = new UsersKeyword();
			$k->setKeyword($keyword);
			$this->addKeyword($k);
		}
	}
	
	public function addKeyword(UsersKeyword $keyword){
		$keyword->setParentComponent($this->parent);
		$this->keywords[$keyword->getKeyword()] = $keyword;
	}
	
	public function removeKeyword(UsersKeyword $keyword){
		unset($this->keywords[$keyword->getKeyword()]);
	}
	
	public function removeAllKeywords(){
		$this->_getDAO();
		$this->dao->removeAllKeywords($this->getUserID());
	}
	
	public function setParentComponent(UserComponent $component){
		$this->filter->set(UsersInformation::FIELD_USER_ID, $component->getID());
		return parent::setParentComponent($component);
	}
		
	public function read(){
		$this->_getDAO(false);
		$res = $this->dao->getList($this->filter);
		while ($out = $res->fetchArray()) {
			$this->keywords[] = new UsersKeyword($out[UsersKeyword::FIELD_KEYWORD_ID], $out);
		}		
	}
	
	public function commit(){
		foreach ($this->keywords as $val){
			$val->commit();
		}
	}
	
	public function getXML(DOMDocument $xml){
		if(is_null($this->keywords)){
			$this->_getDAO();
		}
		$list = $xml->createElement('keywords');
		foreach ($this->keywords as $keyword){
			$list->appendChild($keyword->getXML($xml));
		}
		return $list;
	}

	public function &getArray(){
		
	}
	
	private function _getDAO($read = true){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UsersKeywords');
			if($read){
				$this->read();
			}			
		}
		return true;
	}
}
?>