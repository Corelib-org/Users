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


//*****************************************************************//
//************************* DAO Interface *************************//
//*****************************************************************//
/**
 * UserSetting MySQLi DAO list Class.
 *
 * @category corelib
 * @package Users
 */
class MySQLi_UserSettingList extends DatabaseDAO implements Singleton,DAO_UserSettingList {
	/**
	 * @var MySQLi_UserSettingList
	 * @internal
	 */
	private static $instance = null;

	/**
	 * @see Singleton::getInstance()
	 * @return MySQLi_UserSettingList
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new MySQLi_UserSettingList();
		}
		return self::$instance;
	}


	//*****************************************************************//
	//******************** Data retrieval methods *********************//
	//*****************************************************************//
	/**
	 * @see DAO_UserSettingList::getList()
	 * @return MySQLiQuery
	 */
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order, $offset=null, $limit=null, $view=null){
		/* Order statement */
		$order = MySQLiTools::prepareOrderStatement($order);
		/* Order statement end */
		if(!$order){
			$order = '';
		} else {
			$order = 'ORDER BY '.$order;
		}

		$filters = $this->_prepareFilterStatements($filter);
		$join = $filters['join'];
		$where = $filters['where'];
		$columns = MySQLi_UserSetting::getSelectColumns();

		if(!$limit = MySQLiTools::prepareLimitStatement($offset, $limit)){
			$limit = '';
		}

		if($view instanceof DatabaseViewHelper){
			$join .= ' LEFT JOIN `'.$view->get(DATABASE_MYSQLI_VIEW_JOIN_TABLE).'`
			                  ON `'.UserSetting::FIELD_ID.'`=`'.$view->get(DATABASE_MYSQLI_VIEW_JOIN_TABLE).'`.`'.$view->get(DATABASE_MYSQLI_VIEW_JOIN_KEY).'` ';
			$columns .= ', `'.$view->get(DATABASE_VIEW_XML_FIELD).'`';
		}

		$query = 'SELECT '.$columns.'
		          FROM `tbl_user_settings`
		          '.$join.'
		          '.$where.'
		          '.$order.'
		          '.$limit;
		return $this->slaveQuery(new MySQLiQuery($query));
	}

	/**
	 * @see DAO_UserSettingList::getListCount()
	 */
	public function getListCount(DatabaseListHelperFilter $filter){
		$filters = $this->_prepareFilterStatements($filter);
		$join = $filters['join'];
		$where = $filters['where'];

		$query = 'SELECT COUNT(*) AS `count`
		          FROM `tbl_user_settings`
		          '.$join.'
		          '.$where;
		$query = $this->slaveQuery(new MySQLiQuery($query));
		$query = $query->fetchArray();
		return $query['count'];
	}


	//*****************************************************************//
	//************************ Private methods ************************//
	//*****************************************************************//
	/**
	 * Create filter statement based on defined filters.
	 *
	 * @param DatabaseListHelperFilter $filter
	 * @return string filter statement
	 * @internal
	 */
	private function _prepareFilterStatements(DatabaseListHelperFilter $filter){
		$filters['where'] = 'WHERE 1 ';
		$filters['join'] = '';

		if($filter->count() > 0){
			/* Filter statement */
			if($user = $filter->get(UserSetting::FIELD_USER)){
				$filters['where'] .= 'AND `'.UserSetting::FIELD_USER.'` = \''.$this->escapeString($user).'\'';
			}
			/* Filter statement end */
		}
		return $filters;
	}
}
?>