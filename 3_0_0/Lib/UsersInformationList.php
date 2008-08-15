<?php
interface DAO_UsersInformationList {
	public function getList(DatabaseListHelperFilter $filter);
}

class UsersInformationList extends UserComponent implements Output  {
	
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
		$list = $xml->createElement('informationlist');
		$res = $this->dao->getList($this->filter);
		while ($out = $res->fetchArray()) {
			$info = new UsersInformation($out[UsersInformation::FIELD_INFOMATION_ID], $out);
			$list->appendChild($info->getXML($xml));
			
			// print_r($out);
/*			$user = new User($out[User::FIELD_ID], $out);
			if(!is_null($this->last_timestamp_converter)){
				$user->setLastTimestampConverter($this->last_timestamp_converter);
			}
			if(!is_null($this->create_timestamp_converter)){
				$user->setCreateTimestampConverter($this->create_timestamp_converter);
			}
			$users->appendChild($user->getXML($xml)); */
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