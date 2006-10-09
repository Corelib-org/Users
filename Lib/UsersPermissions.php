<?php
class Permission implements Output {
	private $dao = null;
	
	private $id = null;
	private $ident = null;
	private $name = null;
	private $updated = false;
	
	public function __construct($id = null, $array=array()){
		try {
			if(!is_null($id) && !is_integer($id)){
				throw new BaseException('$id is not of type integer: '.gettype($id));
			} else if(!is_null($array) && !is_array($array)){
				throw new BaseException('$array is not of type array: '.gettype($array));
			} else {
				$this->id = $id;
				if(!is_null($this->id) && sizeof($array) == 0){
					$this->_getById($this->id);
				} else if(!is_null($this->id)){
					$this->_setFromArray($array);
				}
			}
		} catch (Exception $e){
			echo $e;
		}
	}
	public function setIdent($ident){
		try {
			if(is_string($ident) && strlen($ident) <= 255){
				$this->ident = $ident;
				$this->updated = true;
			} else {
				throw new Exception('$ident is not of string, or is more then 255 bytes long: '.gettype($ident).':'.strlen($ident));
			}
		} catch (Exception $e){
			echo $e;
			return false;
		}
		return true;
	}
	public function setName($name=null){
		try {
			if(is_null($name)){
				$this->name = null;
				$this->updated = true;
			} else if(is_string($name) && strlen($name) <= 255){
				$this->name = $name;
				$this->updated = true;
			} else {
				throw new Exception('$ident is not of string, or is more then 255 bytes long: '.gettype($ident).':'.strlen($ident));
			}
		} catch (Exception $e){
			echo $e;
			return false;
		}
		return true;
	}
	public function save(){
		if($this->updated){
			if(is_null($this->dao)){
				$this->dao = Database::getDAO('UsersPermissions','UsersPermissions');
			}			
			if(is_null($this->id)){
				try {
					if(is_null($this->ident)){
						throw new BaseException('permission ident not set, unable to create permission');
					} else {
						return $this->dao->createPermission($ident);
					}
				} catch (Exception $e){
					echo $e;
					return false;
				}
			} else {
				try {
					if(is_null($this->ident)){
						throw new BaseException('permission ident not set, unable to save permission');
					} else {
						$this->dao->setPermissionInformation($this->id, $this->ident, $this->name);
					} 
				} catch (Exception $e){
					echo $e;
					return false;
				}
				return $this->id;
			}
		}
	}
	public function getID(){
		return $this->id;
	}
	
	public function getXML(DOMDocument $xml){
		$permission = $xml->createElement('permission', $this->name);
		$permission->setAttribute('id', $this->id);
		$permission->setAttribute('ident', $this->ident);
		return $permission;
	}
	public function &getArray(){
		return array('id'=>$this->id, 'ident'=>$this->ident, 'name'=>$this->name);
	}
	public function getString($format = '%1$s'){
		return sprintf($format, $this->id, $this->ident, $this->name);
	}
	
	private function _getById($id){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UsersPermissions','UsersPermissions');
		}
		if($data = $this->dao->getPermissionById($id)){
			$this->_setFromArray($data);
		}
	}
	private function _setFromArray($array){
		if(isset($array['permission_ident'])){
			$this->ident = $array['permission_ident'];
		}
		if(isset($array['permission_name'])){
			$this->name = $array['permission_name'];
		}
	}
}

class PermissionRole {
	private $id = null;
}

class PermissionGroup {
	private $id = null;
	private $permission_list = array();
}

class UsersPermissions extends UserDecorator {
	protected $dao = null;

	private $permission_list = array();
	private $role_list = array();
	private $group_list = array();
	
	public function revokePermission(Permission $permission){
		if(isset($this->permission_list[$permission->getID()])){
			if($this->dao->revokePermission($this->decorator->getUID(), $permission->getID())){
				unset($this->permission_list[$permission->getID()]);
				return true;
			}
		}
		return false;
	}	
	public function grantPermission(Permission $permission, $expire=null, $comment=null){
		if(!isset($this->permission_list[$permission->getID()])){
			try {
				if(!is_null($expire) && !StrictTypes::isInteger($expire)){
					throw new BaseException('$expire, is not integer or null: '.gettype($expire).':'.strlen($expire));
				} else {
					if(is_null($this->dao)){
						$this->dao = Database::getDAO('UsersPermissions','UsersPermissions');
					}
					if($this->dao->grantPermission($this->decorator->getUID(), $permission->getID(), $expire, $comment)){
						$this->permission_list[$permission->getID()] = $permission;
						return true;
					}
				}
			} catch (BaseException $e){
				echo $e;
				return false;
			}
		} else {
			return true;
		}
		return false;
	}
	public function grantPermissionAddExpireInSeconds(Permission $permission, $expire, $comment=null){
		try {
			StrictTypes::toString($expire);
		} catch (Exception $e){
			echo $e;
			return false;
		}
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UsersPermissions','UsersPermissions');
		}
		$timestamp = $this->getPermissionExpirationTimestamp($permission);
		if(is_null($timestamp)){
			$timestamp = time();
		}
		$timestamp = $timestamp + $expire;
		return $this->grantPermission($permission, $timestamp, $comment);
	} 
	
	public function getPermissionExpirationTimestamp(Permission $permission){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UsersPermissions','UsersPermissions');
		}
		return $this->dao->getPermissionExpirationTimestamp($this->decorator->getUID(), $permission->getID());
	}
	
	public function addRoleMembership(PermissionRole $role, $expire=null, $comment=null){
		if(!isset($this->role_list[$role->getID()])){
			if($this->dao->addRoleMembership($this->decorator->getUID(), $role->getID())){
				$this->role_list[$role->getID()] = $role;
				return true;
			}
		} else {
			return true;
		}
		return false;
	}
	public function removeRoleMembership(PermissionRole $role){
		if(isset($this->role_list[$role->getID()])){
			if($this->dao->removeRoleMembership($this->decorator->getUID(), $role->getID())){
				unset($this->role_list[$role->getID()]);
				return true;
			}
		}
		return false;
	}
	public function addGroupMembership(PermissionGroup $group, $expire=null, $comment=null){
		if(!isset($this->group_list[$group->getID()])){
			if($this->dao->grantPermission($this->decorator->getUID(), $group->getID())){
				$this->group_list[$group->getID()] = $group;
				return true;
			}
		} else {
			return true;
		}
		return false;
	}
	public function removeGroupMembership(PermissionGroup $group){
		if(isset($this->group_list[$permission->getID()])){
			if($this->dao->removeGroupMembership($this->decorator->getUID(), $permission->getID())){
				unset($this->group_list[$permission->getID()]);
				return true;
			}
		}
		return false;
	}
	
	public function getXML(DOMDocument $xml){
		$permissions = $xml->createElement('permissions');
		
		$permissionlist = $xml->createElement('permissions');
		while(list($key, $val) = each($this->permission_list)){
			$permissionlist->appendChild($val->getXML());
		}
		reset($this->permission_list);
		return $this->buildXML($xml, $permissions);
	}
	public function &getArray(){
	}
	public function getString($format = '%1$s'){
	}	
}

interface DAO_UserPermissions {
	public function createPermission($ident, $name=null);
	public function setPermissionInformation($id, $ident, $name=null);
	public function getPermissionById($id);
	public function grantPermission($userid, $id, $expire=null, $comment=null);
	public function getPermissionExpirationTimestamp($userid, $id);
}
?>