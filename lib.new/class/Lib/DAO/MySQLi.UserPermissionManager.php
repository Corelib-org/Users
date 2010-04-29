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
 * @version 1.0.0 ($Id$)
 */

/**
 * UserPermissionManager MySQLi DAO Class.
 *
 * @category corelib
 * @package Users
 * @package Authorization
 */
class MySQLi_UserPermissionManager extends DatabaseDAO implements Singleton,DAO_UserPermissionManager {

	/**
	 * @var MySQLi_UserPermissionManager
	 */
	private static $instance = null;

	/**
	 * @return MySQLi_UserPermissionManager
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UserPermissionManager();
		}
		return self::$instance;
	}

	/**
	 * Grant permission.
	 *
	 * @see DAO_UserPermissionManager::grant()
	 */
	public function grant($user, $permission, $comment=null, $expire=null){
		$query = MySQLiTools::makeReplaceStatement('tbl_users_has_permissions', array('fk_users', 'fk_user_permissions', 'comment', 'expire_timestamp' => 'FROM_UNIXTIME(?)'));
		$query = $this->masterQuery(new MySQLiQueryStatement($query, $user, $permission, $comment, $expire));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Revoke permission.
	 *
	 * @see DAO_UserPermissionManager::revoke()
	 */
	public function revoke($user, $permission){
		$query = 'DELETE FROM `tbl_users_has_permissions`
		          WHERE `fk_users`=\''.$this->escapeString($user).'\'
		            AND `fk_user_permissions`=\''.$this->escapeString($permission).'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));
		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get permission list.
	 *
	 * @param integer database user reference ID
	 * @return MySQLiQuery
	 * @see DAO_UserPermissionManager::getList()
	 */
	public function getList($user){
		$query = 'SELECT `comment`, `expire_timestamp`, '.MySQLi_UserPermission::getSelectColumns().'
		          FROM `tbl_users_has_permissions`
		          INNER JOIN `tbl_user_permissions` ON `'.UserPermission::FIELD_ID.'`=`fk_user_permissions`
		          WHERE `fk_users`=\''.$this->escapeString($user).'\'';
		return $this->slaveQuery(new MySQLiQuery($query));
	}
}
?>