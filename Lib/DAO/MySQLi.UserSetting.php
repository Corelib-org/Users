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
 * @subpackage UserSettings
 *
 * @link http://www.corelib.org/
 * @version 1.0.0 ($Id$)
 * @filesource
 */

/**
 * UserSetting MySQLi DAO Class
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
class MySQLi_UserSetting extends DatabaseDAO implements Singleton,DAO_UserSetting {
	/**
	 * @var MySQLi_UserSetting
	 */
	private static $instance = null;

	/**
	 * @ignore
	 */
	const SELECT_COLUMNS = '`fk_users`,
	                        `ident`,
	                        `value`';

	/**
	 * @return MySQLi_UserSetting
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UserSetting();
		}
		return self::$instance;
	}


	//*****************************************************************//
	//************************ Utility methods ************************//
	//*****************************************************************//
	/* Utility methods */
	/* Utility methods end */


	//*****************************************************************//
	//******************* Data modification methods *******************//
	//*****************************************************************//

	/**
	 * @see DAO_UserSetting::update()
	 */
	public function update($user, $ident, DatabaseDataHandler $data){
		/* Special update fields */
		$data->set(UserSetting::FIELD_USER_ID, $user);
		$data->set(UserSetting::FIELD_IDENT, $ident);
		/* Special update fields end */

		$columns = $data->getUpdatedColumns();
		$values = $data->getUpdatedColumnValues();

		$query = MySQLiTools::makeReplaceStatement('tbl_user_settings', $columns);

		$query = $this->masterQuery(new MySQLiQueryStatement($query, $values));

		/* After edit actions */
		/* After edit actions end */

		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @see DAO_UserSetting::read()
	 */
	public function read($user, $ident){
		$query = 'SELECT '.self::SELECT_COLUMNS.'
		          FROM `tbl_user_settings`
		          WHERE `'.UserSetting::FIELD_USER_ID.'`=\''.mysql_escape_string($user).'\' AND `'.UserSetting::FIELD_IDENT.'`=\''.mysql_escape_string($ident).'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));

		return $query->fetchArray();
	}

	/**
	 * @see DAO_UserSetting::delete()
	 */
	public function delete($user, $ident){
		/* Delete actions */
		/* Delete actions end */

		$query = 'DELETE FROM `tbl_user_settings`
		          WHERE `'.UserSetting::FIELD_USER_ID.'`=\''.mysql_escape_string($user).'\' AND `'.UserSetting::FIELD_IDENT.'`=\''.mysql_escape_string($ident).'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));

		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}
}
?>