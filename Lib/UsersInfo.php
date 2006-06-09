<?php

interface DAO_UsersInfo {
	public function getUsersInfoIN($in, $uid=null);
	public function getInfoItemsFromParent($id);
	public function getInfoFromParent($id);
	public function ifInfoItemExcists($item, $value=null);
	public function setUserInfoItem($uid, $id, $item = null, $value=null);
}

class UserItem extends UserDecorator {
	private $deep = null;
	private $maxdepth = null;
	private $depth = null;
	private $selected = null;
	private $select_id = null;

	private $id = null;
	private $title = null;
	private $parent = null;
	private $is_selected = null;
	private $has_items = false;
	
	const LIBRARY = 'UsersInfo';
	
	public function __construct($id=null, $deep=false, $maxdepth=null, $array=null, $selected=false, $uid=null){
		$this->deep = $deep;
		$this->maxdepth = $maxdepth;
		$this->id = $id;
		$this->selected = $selected;
		$this->select_id = $uid;
		
		if(isset($array['fk_users_info'])){
			$this->parent =	$array['fk_users_info'];
		}
		if(isset($array['item_title'])){
			$this->title =	$array['item_title'];
		}		
		if(isset($array['pk_users'])){
			$this->is_selected = true;
		}
		if(isset($array['has_items'])){
			$this->has_items = ($array['has_items'] == 'TRUE');
		}		
	}
	
	public function getXML(DOMDocument $xml, $jsEscape=false){
		$DOMInfo = $xml->createElement('item');
		$DOMInfo->setAttribute('id', $this->id);
		if($jsEscape){
			$this->title = addslashes($this->title);
		}
		if(!is_null($this->parent)){
			$DOMInfo->setAttribute('parent', $this->parent);
		}
		if(!is_null($this->title)){
			$DOMInfo->setAttribute('title', $this->title);
		}
		if(!is_null($this->is_selected)){
			$DOMInfo->setAttribute('selected', 'true');
		}
		if($this->has_items){
			if((is_null($this->depth) || ($this->depth <= $this->maxdepth || is_null($this->maxdepth))) && $this->deep !== false){
				$query = $this->_getInfo();
				while($out = $query->fetchArray()){
					$info = new UserInfo($out['pk_users_info'], $this->deep, $this->maxdepth, $out, $this->selected, $this->select_id);
					$info->_setDepth($this->depth);
					$DOMInfo->appendChild($info->getXML($xml, $jsEscape));
				}
			}
		}
		return $this->buildXML($xml, $DOMInfo);	
	}
	
	private function _getInfo(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		return $this->dao->getInfoFromParent($this->id);
	}
	
	public function _setDepth($depth){
		$this->depth = $depth;	
	}
	
	public function getUID(){
		return $this->decorator->getUID();
	}
	
	public function getID(){
		return $this->id;	
	}
}

class UserInfo extends UserDecorator {
	private $deep = null;
	private $maxdepth = null;
	private $depth = null;
	private $selected = null;
	private $select_id = null;

	private $id = null;
	private $title = null;
	private $parent = null;
	private $ignore_title = null;
	private $value = null;
	private $item_id = null;
	private $item_title = null;

	private static $content_formatter = array();
	
	const LIBRARY = 'UsersInfo';
	
	public function __construct($id=null, $deep=false, $maxdepth=null, $array=null, $selected=false, $uid=null){
		$this->deep = $deep;
		$this->maxdepth = $maxdepth;
		$this->id = $id;
		$this->selected = $selected;
		$this->select_id = $uid;
		$this->has_items = false;
		
		if(isset($array['fk_users_info_items'])){
			$this->parent =	$array['fk_users_info_items'];
		}
		if(isset($array['info_title'])){
			$this->title =	$array['info_title'];
		}
		if(isset($array['ignore_title'])){
			$this->ignore_title = $array['ignore_title'];
		}
		if(isset($array['value'])){
			$this->value = $array['value'];
		}
		if(isset($array['pk_users_info_items'])){
			$this->item_id = $array['pk_users_info_items'];
		}
		if(isset($array['item_title'])){
			$this->item_title = $array['item_title'];
		}
		if(isset($array['has_items'])){
			$this->has_items = ($array['has_items'] == 'TRUE');
		}
	}

	public static function addContentFormater($item, Converter $converter){
		self::$content_formatter[$item] = $converter;
	}	
	
	public function getXML(DOMDocument $xml, $jsEscape=false){
		if($jsEscape){
			$this->item_title = addslashes($this->item_title);
		}
		if(isset(self::$content_formatter[$this->id]) && !is_null($this->value)){
			$DOMInfo = $xml->createElement('userinfo');
			$res = self::$content_formatter[$this->id]->convert($this->value);
			$DOMInfo->appendChild($res->getXML($xml));
		} else {
			$DOMInfo = $xml->createElement('userinfo', htmlspecialchars($this->value));
		}
		$DOMInfo->setAttribute('id', $this->id);
		if(!is_null($this->parent)){
			$DOMInfo->setAttribute('parent', $this->parent);
		}
		if(!is_null($this->title)){
			$DOMInfo->setAttribute('title', $this->title);
		}
		if(!is_null($this->title) && $this->ignore_title){
			$DOMInfo->setAttribute('ignore_title', 'true');
		}
		if(!is_null($this->item_id) && $this->item_id){
			$DOMInfo->setAttribute('item_id', $this->item_id);
		}
		if(!is_null($this->item_title) && $this->item_title){
			$DOMInfo->setAttribute('item_title', $this->item_title);
		}
		if($this->has_items){
			$query = $this->_getItems();
			while($out = $query->fetchArray()){
				$info = new UserItem($out['pk_users_info_items'], $this->deep, $this->maxdepth, $out, $this->selected, $this->select_id);
				$info->_setDepth($this->depth + 1);
				$DOMInfo->appendChild($info->getXML($xml, $jsEscape));
			}
		}
		return $this->buildXML($xml, $DOMInfo);	
	}
	
	private function _getItems(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		if(!is_null($this->decorator)){
			return $this->dao->getInfoItemsFromParent($this->id, $this->getUID(), $this->selected);
		} else {
			return $this->dao->getInfoItemsFromParent($this->id, $this->select_id, $this->selected);
		}
	}
	
	public function _setDepth($depth){
		$this->depth = $depth;	
	}
	
	public function setItem(UserItem $item){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		try {
			if(!is_null($this->getUID())){
				$this->dao->setUserInfoItem($this->getUID(), $this->id, $item->getID());
			} else {
				throw new BaseException('Object not decorated with a valid user object');	
			}
		} catch (BaseException $e){
			echo $e;	
		}
	}
	
	public function setValue($value){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		try {
			if(!is_null($this->getUID())){
				$this->dao->setUserInfoItem($this->getUID(), $this->id, null, $value);
			} else {
				throw new BaseException('Object not decorated with a valid user object');	
			}
		} catch (BaseException $e){
			echo $e;	
		}
	}
	
	public function getUID(){
		return $this->decorator->getUID();
	}
}

class UserInfoList extends UserDecorator {
	private $deep = null;
	private $maxdepth = null;
	private $list = null;
	private $selected = null;
		
	const LIBRARY = 'UsersInfo';
	
	public function __construct($list, $deep=false, $maxdepth=null, $selected=false){
		$this->list = $list;
		$this->deep = $deep;
		$this->maxdepth = $maxdepth;
		$this->selected = $selected;
	}
	
	private function getUserInfo(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::LIBRARY, self::LIBRARY);
		}
		if(!is_null($this->decorator)){
			return $this->dao->getUsersInfoIN($this->list, $this->getUID(), $this->selected);
		} else {
			return $this->dao->getUsersInfoIN($this->list);
		}
	}
	
	public function getXML(DOMDocument $xml, $jsEscape=false){
		$DOMInfo = $xml->createElement('userinfolist');
		
		$query = $this->getUserInfo();
		while($out = $query->fetchArray()){
			$info = new UserInfo($out['pk_users_info'], $this->deep, $this->maxdepth, $out, $this->selected, $this->getUID());
			$DOMInfo->appendChild($info->getXML($xml, $jsEscape));
		}	
		return $this->buildXML($xml, $DOMInfo);	
	}
	
	public function getUID(){
		if(!is_null($this->decorator)){
			return $this->decorator->getUID();
		} else {
			return null;
		}
	}
}
?>