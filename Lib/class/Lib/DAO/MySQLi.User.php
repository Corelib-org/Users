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
 *
 * @link http://www.corelib.org/
 * @version 1.1.0 ($Id$)
 */

/**
 * User MySQLi DAO Class.
 *
 * @category corelib
 * @package Users
 */
class MySQLi_User extends DatabaseDAO implements Singleton,DAO_User {
	/**
	 * @var MySQLi_User
	 */
	private static $instance = null;

	/**
	 * @return MySQLi_User
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_User();
		}
		return self::$instance;
	}


	//*****************************************************************//
	//************************ Utility methods ************************//
	//*****************************************************************//
	/**
	 * Get select columns.
	 *
	 * return a list of columns to select, this method replaces the
	 * previously constant SELECT_COLUMNS.
	 *
	 * @return string select columns
	 */
	public static function getSelectColumns(){
		$columns = '';
		/* getSelectColumns */
		$columns .= '  `'.User::FIELD_ID.'`';
		$columns .= ', `'.User::FIELD_USERNAME.'`';
		$columns .= ', `'.User::FIELD_PASSWORD.'`';
		$columns .= ', `'.User::FIELD_EMAIL.'`';
		$columns .= ', IF(`'.User::FIELD_ACTIVATED.'`=\'TRUE\', true, false) AS `'.User::FIELD_ACTIVATED.'`';
		$columns .= ', `'.User::FIELD_ACTIVATION_STRING.'`';
		$columns .= ', IF(`'.User::FIELD_DELETED.'`=\'TRUE\', true, false) AS `'.User::FIELD_DELETED.'`';
		$columns .= ', UNIX_TIMESTAMP(`'.User::FIELD_CREATE_TIMESTAMP.'`) AS `'.User::FIELD_CREATE_TIMESTAMP.'`';
		$columns .= ', UNIX_TIMESTAMP(`'.User::FIELD_LAST_TIMESTAMP.'`) AS `'.User::FIELD_LAST_TIMESTAMP.'`';
		/* getSelectColumns end */
		return $columns;
	}
	/* Utility methods */
	/**
	 * Get row by username.
	 *
	 * @param string $username
	 * @return mixed array similar of read method, else return false
	 */
	public function getByUsername($username){
		$query = 'SELECT '.$this->getSelectColumns().'
		          FROM `tbl_users`
		          WHERE `'.User::FIELD_USERNAME.'` = \''.$this->escapeString($username).'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return $query->fetchArray();
		} else {
			return true;
		}
	}

	/**
	 * Check if username is available..
	 *
	 * @param integer $id
	 * @param string $username
	 * @return boolean true on success, else return false
	 */
	public function isUsernameAvailable($id, $username){
		$query = 'SELECT `'.User::FIELD_USERNAME.'`
		          FROM `tbl_users`
		          WHERE `'.User::FIELD_USERNAME.'` = \''.$this->escapeString($username).'\'';
		if(!is_null($id)){
			$query .= ' AND `pk_users` != \''.$this->escapeString($id).'\'';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get row by email.
	 *
	 * @param string $email
	 * @return mixed array similar of read method, else return false
	 */
	public function getByEmail($email){
		$query = 'SELECT '.$this->getSelectColumns().'
		          FROM `tbl_users`
		          WHERE `'.User::FIELD_EMAIL.'` = \''.$this->escapeString($email).'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return $query->fetchArray();
		} else {
			return true;
		}
	}

	/**
	 * Check if email is available..
	 *
	 * @param integer $id
	 * @param string $email
	 * @return boolean true on success, else return false
	 */
	public function isEmailAvailable($id, $email){
		$query = 'SELECT `'.User::FIELD_EMAIL.'`
		          FROM `tbl_users`
		          WHERE `'.User::FIELD_EMAIL.'` = \''.$this->escapeString($email).'\'';
		if(!is_null($id)){
			$query .= ' AND `pk_users` != \''.$this->escapeString($id).'\'';
		}
		$query = $this->slaveQuery(new MySQLiQuery($query));
		if($query->getNumRows() > 0){
			return false;
		} else {
			return true;
		}
	}

	/* Utility methods end */


	//*****************************************************************//
	//******************* Data modification methods *******************//
	//*****************************************************************//
	/**
	 * @see DAO_User::create()
	 */
	public function create(DatabaseDataHandler $data){
		/* Special create fields */
		$data->setSpecialValue(User::FIELD_CREATE_TIMESTAMP, 'NOW()');
		if($data->isChanged(User::FIELD_LAST_TIMESTAMP)){
			$data->setSpecialValue(User::FIELD_LAST_TIMESTAMP, 'FROM_UNIXTIME(?)');
		}
		if(!$this->isUsernameAvailable(null, $data->get(User::FIELD_USERNAME))){
			return false;
		}
		if(!$this->isEmailAvailable(null, $data->get(User::FIELD_EMAIL))){
			return false;
		}
		/* Special create fields end */

		$columns = $data->getUpdatedColumns();
		$values = $data->getUpdatedColumnValues();

		$query = MySQLiTools::makeInsertStatement('tbl_users', $columns);
		$query = $this->masterQuery(new MySQLiQueryStatement($query, $values));

		if($id = (int) $query->getInsertID()){
			/* After create actions */
			/* After create actions end */
			return $id;
		} else {
			return false;
		}
	}

	/**
	 * @see DAO_User::update()
	 */
	public function update($id, DatabaseDataHandler $data){
		/* Special update fields */
		if($data->isChanged(User::FIELD_LAST_TIMESTAMP)){
			$data->setSpecialValue(User::FIELD_LAST_TIMESTAMP, 'FROM_UNIXTIME(?)');
		}
		if(($data->isChanged(User::FIELD_USERNAME)) && !$this->isUsernameAvailable($id, $data->get(User::FIELD_USERNAME))){
			return false;
		}
		if(($data->isChanged(User::FIELD_EMAIL)) && !$this->isEmailAvailable($id, $data->get(User::FIELD_EMAIL))){
			return false;
		}
		/* Special update fields end */

		$columns = $data->getUpdatedColumns();
		$values = $data->getUpdatedColumnValues();

		$query = MySQLiTools::makeUpdateStatement('tbl_users', $columns, 'WHERE `'.User::FIELD_ID.'`=?');

		$query = $this->masterQuery(new MySQLiQueryStatement($query, $values, $id));

		/* After edit actions */
		/* After edit actions end */

		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @see DAO_User::read()
	 */
	public function read($id){
		$query = 'SELECT '.self::getSelectColumns().'
		          FROM `tbl_users`
		          WHERE `'.User::FIELD_ID.'`=\''.$id.'\'';
		$query = $this->slaveQuery(new MySQLiQuery($query));

		return $query->fetchArray();
	}

	/**
	 * @see DAO_User::delete()
	 */
	public function delete($id){
		/* Delete actions */
		/* Delete actions end */

		$query = 'DELETE FROM `tbl_users`
		          WHERE `'.User::FIELD_ID.'`=\''.$id.'\'';
		$query = $this->masterQuery(new MySQLiQuery($query));

		if($query->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}
}
?>