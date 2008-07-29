<?php
interface DAO_UsersKeywordsList {
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order, $offset=null, $limit=null);
}

class UsersKeywordsList implements Output  {
	/**
	 * @var DAO_UsersKeywordsList
	 */
	private $dao = null;
	
	/**
	 * @var DatabaseListHelperOrder
	 */
	protected $order = null;
	/**
	 * @var DatabaseListHelperFilter
	 */
	protected $filter = null;

	private $limit = null;
	private $offset = null;
	
	public function __construct(){
		$this->order = new DatabaseListHelperOrder();
		$this->filter = new DatabaseListHelperFilter();
	}
	
	
	public function setTitleFilter($title){
		$this->filter->set(UsersKeywords::FIELD_TITLE, $title);
	}
	public function setTitleOrderDESC(){
		$this->order->set(UsersKeywords::FIELD_TITLE, DATABASE_ORDER_DESC);
	}
	public function setTitleOrderASC(){
		$this->order->set(UsersKeywords::FIELD_TITLE, DATABASE_ORDER_ASC);
	}

	public function setLimit($limit){
		$this->limit = $limit;
	}

	public function setOffset($offset){
		$this->offset = $offset;
	}
	
	public function getXML(DOMDocument $xml){
		$this->_getDAO();
		$res = $this->dao->getList($this->filter, $this->order, $this->offset, $this->limit);
		$list = $xml->createElement('');
		
		while ($out = $res->fetchArray()) {
			$item = new UsersKeywords($out[UsersKeywords::FIELD_ID], $out);
			$list->appendChild($item->getXML($xml));
		}

		return $list;
	}
	
	public function &getArray(){
		
	}
	
	private function _getDAO(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UsersKeywordsList');
		}
		return true;
	}
}
?>