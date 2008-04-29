<?php
interface DAO_UsersList {
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order);
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
	
	public function setEmailFilter($email){
		$this->filter->set(User::FIELD_EMAIL, $email);
	}
	public function setEmailOrderDESC(){
		$this->order->set(User::FIELD_EMAIL, DATABASE_ORDER_DESC);
	}
	public function setEmailOrderASC(){
		$this->order->set(User::FIELD_EMAIL, DATABASE_ORDER_ASC);
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
	
	public function getXML(DOMDocument $xml){
		$this->_getDAO();
		$users = $xml->createElement('users');
		$res = $this->dao->getList($this->filter, $this->order);
		while ($out = $res->fetchArray()) {
			$user = new User($out[User::FIELD_ID], $out);
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