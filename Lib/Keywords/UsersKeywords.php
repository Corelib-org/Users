<?php
interface DAO_UsersKeywordList {
	public function getList(DatabaseListHelperFilter $filter);
}

class UsersKeywordList extends UserComponent implements Output  {
	
	/**
	 * @var DatabaseListHelperFilter
	 */
	protected $filter = null;
	
	/**
	 * @var DAO_UsersInformationList
	 */
	private $dao = null;
	
	public function __construct($item=null /*, [$items...] */){
		$this->filter = new DatabaseListHelperFilter();
		$items = func_get_args();
		if(count($items) > 0){
			$this->filter->set(UsersInformation::FIELD_INFOMATION_ID, $items);
		}		
	}
	
	public function setParentComponent(UserComponent $component){
		$this->filter->set(UsersInformation::FIELD_USER_ID, $component->getID());
		return parent::setParentComponent($component);
	}
		
	public function getXML(DOMDocument $xml){
		$this->_getDAO();
		$list = $xml->createElement('keywords');
		$res = $this->dao->getList($this->filter);
		while ($out = $res->fetchArray()) {
			$keyword = new UsersKeyword($out[UsersKeyword::FIELD_KEYWORD_ID], $out);
			$list->appendChild($keyword->getXML($xml));
		}
		return $list;
	}

	public function &getArray(){
		
	}
	
	private function _getDAO(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UsersInformationList');
		}
		return true;
	}
}
?>