<?php
interface DAO_UserPermissionList {
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order, $offset=null, $limit=null);
	public function getListCount(DatabaseListHelperFilter $filter);

	public function removePermissions($userid);
	public function grantPermission($userid, $permission, $comment=null, $expire=null);
	public function revokePermission($userid, $permission);
}

class UserPermissionList extends UserComponent implements Output  {
	/**
	 * @var DAO_UserPermissionList
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
	 * @var UserPermissionViewList
	 */		
	private $view = null;
	
	private $paging = false;
	private $paging_page = 1;

	private $limit = null;
	private $offset = null;
	
	const FIELD_USER_FILTER = 'user_filter';
	const FIELD_USER_STRICT_FILTER = 'user_strict_filter';
	
	public function __construct(){
		$this->order = new DatabaseListHelperOrder();
		$this->filter = new DatabaseListHelperFilter();
	}
		
	public function removePermissions(){
		if($userid = $this->getUserID()){
			$this->_getDAO();
			$this->dao->removePermissions($userid);
		}
	}
	public function grantPermission(UserPermission $permission){
		if($userid = $this->getUserID()){
			$this->_getDAO();
			$this->dao->grantPermission($userid, $permission->getID());
			return true;
		} else {
			return false;
		}
	}
	public function revokePermission(){
		if($userid = $this->getUserID()){
			$this->_getDAO();
			$this->dao->revokePermission($userid, $permission->getID());
			return true;
		} else {
			return false;
		}
	}
	
	
	public function setParentComponent(UserComponent $component){
		$this->setUserFilter($component);
		return parent::setParentComponent($component);
	}	
	
	public function setTitleFilter($title){
		$this->filter->set(UserPermission::FIELD_TITLE, $title);
	}
	public function setTitleOrderDESC(){
		$this->order->set(UserPermission::FIELD_TITLE, DATABASE_ORDER_DESC);
	}
	public function setTitleOrderASC(){
		$this->order->set(UserPermission::FIELD_TITLE, DATABASE_ORDER_ASC);
	}
	
	public function setUserFilter(UserComponent $user, $strict=false){
		$this->filter->set(self::FIELD_USER_FILTER, $user->getUserID());
		$this->setUserFilterStrict($strict);
	}
	
	public function setUserFilterStrict($strict=true){
		$this->filter->set(self::FIELD_USER_STRICT_FILTER, $strict);
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
		$list = $xml->createElement('permissions');
		
		if($this->paging){
			$count = $this->getCount();
			$list->setAttribute('count', $count);
			$this->setOffset($this->limit * ($this->paging_page - 1));
			$list->appendChild(XMLTools::makePagerXML($xml, $count, $this->limit, $this->paging_page));
		}
		
		$res = $this->dao->getList($this->filter, $this->order, null, $this->offset, $this->limit);	
		
		while ($out = $res->fetchArray()) {
			$item = new UserPermission($out[UserPermission::FIELD_ID], $out);
			$item = $list->appendChild($item->getXML($xml));
			if(isset($out[User::FIELD_ID])){
				$item->setAttribute('selected', 'true');
			}
		}
		return $list;
	}
	
	public function &getArray(){
		
	}
	
	private function _getDAO(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UserPermissionList');
		}
		return true;
	}
}
?>