<?php
/* vim: set tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * This script is part of the corelib project. The corelib project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 *
 * @author Steffen Soerensen <ss@corelib.org>
 * @copyright Copyright (c) 2005-2008 Steffen Soerensen
 * @license http://www.gnu.org/copyleft/gpl.html
 *
 * @category corelib
 * @package Users
 * @package Authorization
 *
 * @link http://www.corelib.org/
 * @version 1.1.0 ($Id$)
 * @since Version 4.0
 */


//*****************************************************************//
//************** DAO_UserPermissionManager interface **************//
//*****************************************************************//
/**
 * DAO interface for UserPermissionManager.
 *
 * @category corelib
 * @package Users
 * @package Authorization
 */
interface DAO_UserPermissionManager {

	/**
	 * Grant user a permission.
	 *
	 * @param integer $user user id
	 * @param integer $permission permission id
	 * @param string $comment
	 * @param integer $expire expiration timestamp
	 * @return boolean true on success, else return false
	 */
	public function grant($user, $permission, $comment=null, $expire=null);

	/**
	 * Revoke user permission.
	 *
	 * @param integer $user user id
	 * @param integer $permission permission id
	 * @return boolean true on success, else return false
	 */
	public function revoke($user, $permission);

	/**
	 * Iterate over each result.
	 *
	 * @param integer $user database user reference ID
	 * @return Query on success, else return false
	 */
	public function getList($user);
}


//*****************************************************************//
//****************** UserPermissionManager class ******************//
//*****************************************************************//
/**
 * User permission manager class.
 *
 * @category corelib
 * @package Users
 * @package Authorization
 */
class UserPermissionManager extends CompositeUser {


	//*****************************************************************//
	//*********** UserPermissionManager class properties **************//
	//*****************************************************************//
	/**
	 * List of permissions.
	 *
	 * @var array list of UserPermissions
	 */
	private $permissions = array();

	/**
	 * Permission ident reference.
	 *
	 * @var array ident reference
	 */
	private $reference = array();

	/**
	 * @var DAO_UserPermissionManager
	 */
	private $dao = null;


	//*****************************************************************//
	//************ UserPermissionManager class constants **************//
	//*****************************************************************//
	/**
	 * DAO Class name reference.
	 *
	 * @var string
	 */
	const DAO = 'UserPermissionManager';


	//*****************************************************************//
	//************* UserPermissionManager class methods ***************//
	//*****************************************************************//
	/**
	 * Get permission.
	 *
	 * Get valid permission by {@link UserPermission}, if permission is not
	 * set or if permissions is expired return false, else return {@link UserPermission}
	 *
	 * @param UserPermission $permission
	 * @return UserPermission on success, else return false
	 * @internal
	 */
	public function getPermission(UserPermission $permission){
		if(isset($this->permissions[$permission->getID()])){
			if(!is_null($this->permissions[$permission->getID()]['expire'])){
				if($this->permissions[$permission->getID()]['expire'] > time()){
					return $this->permissions[$permission->getID()]['permission'];
				} else {
					return false;
				}
			} else {
				return $this->permissions[$permission->getID()]['permission'];
			}
		} else {
			return false;
		}
	}

	/**
	 * Get permission by ident.
	 *
	 * Get valid permission by ident, if permission is not
	 * set or if permissions is expired return false, else
	 * return {@link UserPermission}
	 *
	 * @param string $ident permission ident
	 * @return UserPermission on success, else return false
	 * @internal
	 */
	public function getPermissionByIdent($ident){
		assert('is_string($ident)');
		if(isset($this->reference[$ident])){
			if(!is_null($this->reference[$ident]['expire'])){
				if($this->reference[$ident]['expire'] > time()){
					return $this->reference[$ident]['permission'];
				} else {
					return false;
				}
			} else {
				return $this->reference[$ident]['permission'];
			}
		} else {
			return false;
		}
	}



	/**
	 * Grant permission.
	 *
	 * @param UserPermission $permission
	 * @param string $comment
	 * @param integer $expire expiration timestamp
	 * @return boolean true on success, else return false
	 */
	public function grant(UserPermission $permission, $comment=null, $expire=null){
		assert('is_integer($expire) || is_null($expire)');
		assert('is_string($comment) || is_null($comment)');

		$id = $permission->getID();
		if(!is_null($id)){
			$this->permissions[$id] = array('permission' => $permission,
			                                'comment' => $comment,
			                                'expire' => $expire);
			if(!is_null($permission->getIdent())){
				$this->reference[$permission->getIdent()] = &$this->permissions[$id];
			}
			return true;
		} else {
			trigger_error('Can\'t grant a permission without permission id. please call commit or read before granting a permission', E_USER_ERROR);
			return false;
		}
	}

	/**
	 * Revoke permission.
	 *
	 * @param UserPermission $permission
	 * @return boolean true on success, else return false
	 */
	public function revoke(UserPermission $permission){
		$id = $permission->getID();
		if(!is_null($id)){
			$this->permissions[$id] = array('permission' => $permission,
			                                'revoke' => true);
			unset($this->reference[$permission->getIdent()]);
		} else {
			trigger_error('Can\'t revoke a permission without permission id. please call commit or read before revoking a permission', E_USER_ERROR);
			return false;
		}
	}

	/**
	 * Reload permissions.
	 *
	 * @return boolean true on success, else return false
	 */
	public function reload(){
		if(!is_null($this->getUser()->getID())){
			$this->_getDAO();
			$res = $this->dao->getList($this->getUser()->getID());
			while ($out = $res->fetchArray()) {
				if(!is_null($out['expire_timestamp'])){
					$out['expire_timestamp'] = (int) $out['expire_timestamp'];
				}
				$this->grant(new UserPermission($out['pk_user_permissions'], $out), $out['comment'], $out['expire_timestamp']);
			}
		}
		return true;
	}

	/**
	 * Commit permission changes.
	 *
	 * @return boolean true on success, else return false
	 */
	public function commit(){
		$this->_getDAO();
		foreach ($this->permissions as $permission){
			if(isset($permission['revoke']) && $permission['revoke']){
				$this->dao->revoke($this->getUser()->getID(), $permission['permission']->getID());
			} else {
				$this->dao->grant($this->getUser()->getID(), $permission['permission']->getID(), $permission['comment'], $permission['expire']);
			}
		}
		$this->reload();
	}

	/**
	 * Get content XML.
	 *
	 * @see Output::getXML()
	 * @return DOMElement
	 */
	public function getXML(DOMDocument $xml){
		$manager = $xml->createElement('user-permission-manager');
		foreach ($this->permissions as $permission){
			$permissionXML = $manager->appendChild($permission['permission']->getXML($xml));
			if(isset($permission['expire'])){
				$permissionXML->setAttribute('expire-timestamp', $permission['expire']);
				if(time() > $permission['expire']){
					$permissionXML->setAttribute('expired', 'true');
				}
			}
			if(isset($permission['comment'])){
				$permissionXML->appendChild($xml->createElement('comment', $permission['comment']));
			}
		}
		return $manager;
	}

	//*****************************************************************//
	//************************ Private methods ************************//
	//*****************************************************************//
	/**
	 * Get Current DAO object instance.
	 *
	 * @uses UserPermissionManager::$dao
	 * @uses UserPermissionManager::DAO
	 * @uses Database::getDAO()
	 * @param boolean $read if true, then read data from database
	 * @return boolean true
	 * @internal
	 */
	private function _getDAO(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::DAO);
		}
		return true;
	}
}
?>