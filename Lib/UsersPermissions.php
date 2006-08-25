<?php
class Permission {
	private $dao = null;
	
	private $id = null;
	private $ident = null;
	
	public function __construct($id = null, $array=array()){
		$this->id = $id;
		if(!is_null($this->id) && sizeof($array) == 0){
			$this->_getById($this->id);
		} else if(!is_null($this->id)){
			$this->_setFromArray($array);
		}
	}
	
	public function setIdent($ident){
		try {
			
		} catch (Exception  $e){
			
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

class UsersPermissions extends UserDecorator implements Output {
	private $permission_list = array();
	private $role_list = array();
	private $group_list = array();

	protected $dao = null;
	
	public function revokePermission(Permission $permission){
		if(isset($this->permission_list[$permission->getID()])){
			if($this->dao->revokePermission($this->decorator->getUID(), $permission->getID())){
				unset($this->permission_list[$permission->getID()]);
				return true;
			}
		}
		return false;
	}	
	
	public function grantPermission(Permission $permission){
		if(!isset($this->permission_list[$permission->getID()])){
			if($this->dao->grantPermission($this->decorator->getUID(), $permission->getID())){
				$this->permission_list[$permission->getID()] = $permission;
				return true;
			}
		} else {
			return true;
		}
		return false;
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
}
?>