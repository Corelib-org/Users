<?php
interface DAO_UsersList {
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order, $offset=null, $limit=null);
	public function getListCount(DatabaseListHelperFilter $filter);
}

class UsersList implements Output  {
	/**
	 * @var DAO_UsersList
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
	/**
	 * @var Converter
	 */
	private $last_timestamp_converter = null;
	/**
	 * @var Converter
	 */
	private $create_timestamp_converter = null;
	
	private $username_converter = null;
	private $email_converter = null;
		
	protected $paging = false;
	protected $paging_page = 1;
	
	private $limit = null;
	private $offset = null;	
	
	public function __construct(){
		$this->order = new DatabaseListHelperOrder();
		$this->filter = new DatabaseListHelperFilter();
	}
	
	public function setUsernameFilter($username){
		$this->filter->set(User::FIELD_USERNAME, $username);
	}
	public function setUsernameOrderDESC(){
		$this->order->set(User::FIELD_USERNAME, DATABASE_ORDER_DESC);
	}
	public function setUsernameOrderASC(){
		$this->order->set(User::FIELD_USERNAME, DATABASE_ORDER_ASC);
	}
	public function setUsernameConverter(Converter $converter){
		$this->username_converter = $converter;
	}
	
	public function setEmailFilter($email){
		$this->filter->set(User::FIELD_EMAIL, $email);
	}
	public function setEmailOrderDESC(){
		$this->order->set(User::FIELD_EMAIL, DATABASE_ORDER_DESC);
	}
	public function setEmailOrderASC(){
		$this->order->set(User::FIELD_EMAIL, DATABASE_ORDER_ASC);
	}
	public function setEmailConverter(Converter $converter){
		$this->email_converter = $converter;
	}
	
	public function setActivatedFilter($activated=true){
		$this->filter->set(User::FIELD_ACTIVATED, $activated);
	}

	public function setCreateTimestampOrderDESC(){
		$this->order->set(User::FIELD_CREATE_TIMESTAMP, DATABASE_ORDER_DESC);
	}
	public function setCreateTimestampOrderASC(){
		$this->order->set(User::FIELD_CREATE_TIMESTAMP, DATABASE_ORDER_ASC);
	}
	
	public function setLastTimestampOrderDESC(){
		$this->order->set(User::FIELD_LAST_TIMESTAMP, DATABASE_ORDER_DESC);
	}
	public function setLastTimestampOrderASC(){
		$this->order->set(User::FIELD_LAST_TIMESTAMP, DATABASE_ORDER_ASC);
	}

	public function setLastTimestampConverter(Converter $converter){
		$this->last_timestamp_converter = $converter;
	}
	public function setCreateTimestampConverter(Converter $converter){
		$this->create_timestamp_converter = $converter;
	}
	
	public function getCount() {
		$this->_getDAO();
		return $this->dao->getListCount($this->filter);
	}	
	
	public function setPage($page=1){
		$this->paging = true;
		$this->paging_page = $page;
	}
	
	public function setLimit($limit){
		$this->limit = $limit;
	}

	public function setOffset($offset){
		$this->offset = $offset;
	}	
	
	public function getXML(DOMDocument $xml){
		$this->_getDAO();
		$users = $xml->createElement('users');
		
		if($this->paging){
			$count = $this->getCount();
			$users->setAttribute('count', $count);
			$this->setOffset($this->limit * ($this->paging_page - 1));
			$users->appendChild(XMLTools::makePagerXML($xml, $count, $this->limit, $this->paging_page));
		}		
		$res = $this->dao->getList($this->filter, $this->order, $this->offset, $this->limit);
		while ($out = $res->fetchArray()) {
			$user = new User($out[User::FIELD_ID], $out);
			if(!is_null($this->last_timestamp_converter)){
				$user->setLastTimestampConverter($this->last_timestamp_converter);
			}
			if(!is_null($this->create_timestamp_converter)){
				$user->setCreateTimestampConverter($this->create_timestamp_converter);
			}
			if(!is_null($this->username_converter)){
				$user->setUsernameConverter($this->username_converter);
			}
			if(!is_null($this->email_converter)){
				$user->setEmailConverter($this->email_converter);
			}
			$users->appendChild($user->getXML($xml));
		}
		return $users;
	}

	public function &getArray(){
		
	}
	
	private function _getDAO(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UsersList');
		}
		return true;
	}
}
?>