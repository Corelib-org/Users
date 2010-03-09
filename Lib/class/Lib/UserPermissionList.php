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
 */


//*****************************************************************//
//************************* DAO Interface *************************//
//*****************************************************************//
/**
 * DAO interface for UserPermissionList.
 *
 * @category corelib
 * @package Users
 * @package Authorization
 */
interface DAO_UserPermissionList {
	/**
	 * Get the list according to the current criteria.
	 *
	 * @param DatabaseListHelperFilter $filter
	 * @param DatabaseListHelperOrder $order
	 * @param integer $offset
	 * @param integer $limit
	 * @param UserPermissionList $view
	 * @return Query
	 */
	public function getList(DatabaseListHelperFilter $filter, DatabaseListHelperOrder $order, $offset=null, $limit=null, $view=null);

	/**
	 * Count the number of objects according to the current listing criteria.
	 *
	 * @param DatabaseListHelperFilter $filter
	 * @return integer number of objects in the list
	 */
	public function getListCount(DatabaseListHelperFilter $filter);
}


//*****************************************************************//
//************************ Model list class ***********************//
//*****************************************************************//
/**
 * UserPermission model list.
 *
 * @category corelib
 * @package Users
 * @package Authorization
 */
class UserPermissionList implements Output,CacheableOutput {

	//*****************************************************************//
	//**************************** Properties *************************//
	//*****************************************************************//
	/**
	 * @var DAO_UserPermissionList
	 * @internal
	 */
	private $dao = null;

	/* Converter properties */
	/* Converter properties end */

	/**
	 * @var DatabaseListHelperOrder
	 */
	protected $order = null;

	/**
	 * @var DatabaseListHelperFilter
	 */
	protected $filter = null;

	/**
	 * @var CacheManagerOutput
	 */
	protected $cache = null;

	/**
	 * @var UserPermissionList
	 * @internal
	 */
	private $view = null;

	/**
	 * @var boolean
	 * @internal
	 */
	private $paging = false;
	/**
	 * @var integer current page
	 */
	private $paging_page = 1;

	/**
	 * @var integer
	 */
	private $limit = null;
	/**
	 * @var integer
	 * @internal
	 */
	private $offset = null;


	//*****************************************************************//
	//*************************** Constructor *************************//
	//*****************************************************************//
	/**
	 * Create new UserPermissionList instance.
	 *
	 * @uses UserPermissionList::$order
	 * @uses UserPermissionList::$filter
	 * @return void
	 */
	public function __construct(){
		$this->order = new DatabaseListHelperOrder();
		$this->filter = new DatabaseListHelperFilter();
	}

	//*****************************************************************//
	//************************* Filter methods ************************//
	//*****************************************************************//
	/* Filter methods */
	/**
	 * Set filter for ident.
	 *
	 * @param string $ident
	 * @return boolean true on success else return false
	 */
	public function setIdentFilter($ident){
		$this->filter->set(UserPermission::FIELD_IDENT, $ident);
	}

	/* Filter methods end */


	//*****************************************************************//
	//************************** Order methods ************************//
	//*****************************************************************//
	/* Order methods */
	/**
	 * Set ident sort order descending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setIdentOrderDesc(){
		$this->order->set(UserPermission::FIELD_IDENT, DATABASE_ORDER_DESC);
	}

	/**
	 * Set ident sort order ascending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setIdentOrderAsc(){
		$this->order->set(UserPermission::FIELD_IDENT, DATABASE_ORDER_ASC);
	}

	/* Order methods end */


	//*****************************************************************//
	//********************* Converter set methods *********************//
	//*****************************************************************//
	/* Converter methods */
	/* Converter methods end */


	//*****************************************************************//
	//************************** View handling ************************//
	//*****************************************************************//
	/**
	 * Set model view list.
	 *
	 * @uses UserPermissionList::$view
	 * @param UserPermissionViewList $view
	 */
	public function setView(UserPermissionViewList $view){
		$this->view = $view;
	}


	//*****************************************************************//
	//************************ Utility methods ************************//
	//*****************************************************************//
	/**
	 * Set cache manager.
	 *
	 * @uses UserPermissionList::$cache
	 * @param CacheManagerOutput $output
	 * @return void
	 */
	public function setCacheManagerOutput(CacheManagerOutput $cache){
		$this->cache = $cache;
	}

	//*****************************************************************//
	//************************ List parameters ************************//
	//*****************************************************************//
	/**
	 * Enable paging and set current page.
	 *
	 * @uses UserPermissionList::$paging
	 * @uses UserPermissionList::$paging_page
	 * @param integer $page current page
	 */
	public function setPage($page=1){
		$this->paging = true;
		$this->paging_page = $page;
	}

	/**
	 * Set limit.
	 *
	 * If paging is enabled with {@link UserPermissionList::setPage()}
	 * the limit is set of the amount of objects on one page. If not
	 * enabled normal limit is used
	 *
	 * @uses UserPermissionList::$limit
	 * @param integer $limit
	 */
	public function setLimit($limit){
		$this->limit = $limit;
	}

	/**
	 * Set offset.
	 *
	 * If paging is enabled this has no effect and will be overwritten,
	 * otherwise this set the normal offset
	 *
	 * @uses UserPermissionList::$offset
	 * @param integer $offset
	 */
	public function setOffset($offset){
		$this->offset = $offset;
	}

	/**
	 * Count the number of object according to the current listing criteria.
	 *
	 * @uses UserPermissionList::_getDao()
	 * @uses UserPermissionList::$dao
	 * @uses UserPermissionList::$filter
	 * @uses DAO_UserPermissionList::getListCount()
	 * @return integer number of objects in the list
	 */
	public function getCount() {
		$this->_getDAO();
		return $this->dao->getListCount($this->filter);
	}


	//*****************************************************************//
	//************************* Output methods ************************//
	//*****************************************************************//
	/**
	 * @see Output::getXML()
	 * @uses UserPermissionList::$filter
	 * @uses UserPermissionList::$order
	 * @uses UserPermissionList::$offset
	 * @uses UserPermissionList::$limit
	 * @uses UserPermissionList::$paging_page
	 * @uses UserPermissionList::$view
	 * @uses UserPermissionList::$dao
	 * @uses UserPermission::getXML()
	 * @uses Query::getXML()
	 * @uses DAO_UserPermissionList::getList()
	 * @uses UserPermissionViewList::getListHelper()
	 * @uses UserPermissionViewList::getViewXML()
	 * @uses XMLTools::makePagerXML()
	 * @param DOMDocument $xml
	 * @return DOMElement XML output
	 */
	public function getXML(DOMDocument $xml){
		$this->_getDAO();
		$list = $xml->createElement('user-permission-list');

		if($this->paging){
			$count = $this->getCount();
			$list->setAttribute('count', $count);
			$this->setOffset($this->limit * ($this->paging_page - 1));
			$list->appendChild(XMLTools::makePagerXML($xml, $count, $this->limit, $this->paging_page));
		}

		if(!is_null($this->view)){
			$res = $this->dao->getList($this->filter, $this->order, $this->offset, $this->limit, $this->view->getListHelper());
		} else {
			$res = $this->dao->getList($this->filter, $this->order, $this->offset, $this->limit);
		}

		while ($out = $res->fetchArray()) {
			if(is_null($this->view)){
				$item = new UserPermission($out[UserPermission::FIELD_ID], $out);

				/* Set converters */
				/* Set converters end */

				$list->appendChild($item->getXML($xml));
			} else {
				$list->appendChild($this->view->getViewXML($out[UserPermission::FIELD_ID], $out, $xml));
			}
			if(!is_null($this->cache)){
				$this->cache->getCacheManagerOutput($item);
			}
		}
		return $list;
	}

	/**
	 * Iterate over each result.
	 *
	 * @uses UserPermissionList::_getDAO()
	 * @uses UserPermissionList::$dao
	 * @uses DAO_UserPermissionList::getList()
	 * @uses UserPermissionList::$filter
	 * @uses UserPermissionList::$order
	 * @uses UserPermissionList::$offset
	 * @uses UserPermissionList::$limit
	 * @uses Query::dataSeek()
	 * @return UserPermission
	 */
	public function each(){
		static $res = null;
		if(is_null($res)){
			$this->_getDAO();
			$res = $this->dao->getList($this->filter, $this->order, $this->offset, $this->limit);
		}
		if($out = $res->fetchArray()){
			$item = new UserPermission($out[UserPermission::FIELD_ID], $out);
			return $item;
		} else {
			$res->dataSeek(0);
			return false;
		}
	}


	//*****************************************************************//
	//************************ Private methods ************************//
	//*****************************************************************//
	/**
	 * Get Current DAO object instance.
	 *
	 * @uses UserPermissionList::$dao
	 * @uses Database::getDAO()
	 * @return boolean true
	 * @internal
	 */
	private function _getDAO(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UserPermissionList');
		}
		return true;
	}
}
?>