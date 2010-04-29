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
 * DAO interface for UserList.
 *
 * @category corelib
 * @package Users
 */
interface DAO_UserList {
	/**
	 * Get the list according to the current criteria.
	 *
	 * @param DatabaseListHelperFilter $filter
	 * @param DatabaseListHelperOrder $order
	 * @param integer $offset
	 * @param integer $limit
	 * @param UserList $view
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
 * User model list.
 *
 * @category corelib
 * @package Users
 */
class UserList implements Output,CacheableOutput {

	//*****************************************************************//
	//**************************** Properties *************************//
	//*****************************************************************//
	/**
	 * @var DAO_UserList
	 * @internal
	 */
	private $dao = null;

	/* Converter properties */
	private $create_timestamp_converter = null;
	private $last_timestamp_converter = null;
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
	 * @var UserList
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
	 * Create new UserList instance.
	 *
	 * @uses UserList::$order
	 * @uses UserList::$filter
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
	 * Set filter for username.
	 *
	 * @param string $username
	 * @return boolean true on success else return false
	 */
	public function setUsernameFilter($username){
		$this->filter->set(User::FIELD_USERNAME, $username);
	}

	/**
	 * Set filter for email.
	 *
	 * @param string $email
	 * @return boolean true on success else return false
	 */
	public function setEmailFilter($email){
		$this->filter->set(User::FIELD_EMAIL, $email);
	}

	/**
	 * Set filter for activated.
	 *
	 * @param bool $activated
	 * @return boolean true on success else return false
	 */
	public function setActivatedFilter($activated){
		$this->filter->set(User::FIELD_ACTIVATED, $activated);
	}

	/**
	 * Set filter for deleted.
	 *
	 * @param bool $deleted
	 * @return boolean true on success else return false
	 */
	public function setDeletedFilter($deleted){
		$this->filter->set(User::FIELD_DELETED, $deleted);
	}

	/* Filter methods end */


	//*****************************************************************//
	//************************** Order methods ************************//
	//*****************************************************************//
	/* Order methods */
	/**
	 * Set username sort order descending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setUsernameOrderDesc(){
		$this->order->set(User::FIELD_USERNAME, DATABASE_ORDER_DESC);
	}

	/**
	 * Set username sort order ascending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setUsernameOrderAsc(){
		$this->order->set(User::FIELD_USERNAME, DATABASE_ORDER_ASC);
	}

	/**
	 * Set email sort order descending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setEmailOrderDesc(){
		$this->order->set(User::FIELD_EMAIL, DATABASE_ORDER_DESC);
	}

	/**
	 * Set email sort order ascending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setEmailOrderAsc(){
		$this->order->set(User::FIELD_EMAIL, DATABASE_ORDER_ASC);
	}

	/**
	 * Set activated sort order descending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setActivatedOrderDesc(){
		$this->order->set(User::FIELD_ACTIVATED, DATABASE_ORDER_DESC);
	}

	/**
	 * Set activated sort order ascending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setActivatedOrderAsc(){
		$this->order->set(User::FIELD_ACTIVATED, DATABASE_ORDER_ASC);
	}

	/**
	 * Set deleted sort order descending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setDeletedOrderDesc(){
		$this->order->set(User::FIELD_DELETED, DATABASE_ORDER_DESC);
	}

	/**
	 * Set deleted sort order ascending.
	 *
	 * @return boolean true on success, else return false
	 */
	public function setDeletedOrderAsc(){
		$this->order->set(User::FIELD_DELETED, DATABASE_ORDER_ASC);
	}

	/* Order methods end */


	//*****************************************************************//
	//********************* Converter set methods *********************//
	//*****************************************************************//
	/* Converter methods */
	/**
	 * Set converter for create_timestamp.
	 *
	 * @param Converter $converter
	 * @return Converter
	 */
	public function setCreateTimestampConverter(Converter $converter){
		return $this->create_timestamp_converter = $converter;
	}

	/**
	 * Set converter for last_timestamp.
	 *
	 * @param Converter $converter
	 * @return Converter
	 */
	public function setLastTimestampConverter(Converter $converter){
		return $this->last_timestamp_converter = $converter;
	}

	/* Converter methods end */


	//*****************************************************************//
	//************************** View handling ************************//
	//*****************************************************************//
	/**
	 * Set model view list.
	 *
	 * @uses UserList::$view
	 * @param UserViewList $view
	 */
	public function setView(UserViewList $view){
		$this->view = $view;
	}


	//*****************************************************************//
	//************************ Utility methods ************************//
	//*****************************************************************//
	/**
	 * Set cache manager.
	 *
	 * @uses UserList::$cache
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
	 * @uses UserList::$paging
	 * @uses UserList::$paging_page
	 * @param integer $page current page
	 */
	public function setPage($page=1){
		$this->paging = true;
		$this->paging_page = $page;
	}

	/**
	 * Set limit.
	 *
	 * If paging is enabled with {@link UserList::setPage()}
	 * the limit is set of the amount of objects on one page. If not
	 * enabled normal limit is used
	 *
	 * @uses UserList::$limit
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
	 * @uses UserList::$offset
	 * @param integer $offset
	 */
	public function setOffset($offset){
		$this->offset = $offset;
	}

	/**
	 * Count the number of object according to the current listing criteria.
	 *
	 * @uses UserList::_getDao()
	 * @uses UserList::$dao
	 * @uses UserList::$filter
	 * @uses DAO_UserList::getListCount()
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
	 * Get Output XML.
	 *
	 * @see Output::getXML()
	 * @uses UserList::$filter
	 * @uses UserList::$order
	 * @uses UserList::$offset
	 * @uses UserList::$limit
	 * @uses UserList::$paging_page
	 * @uses UserList::$view
	 * @uses UserList::$dao
	 * @uses User::getXML()
	 * @uses Query::getXML()
	 * @uses DAO_UserList::getList()
	 * @uses UserViewList::getListHelper()
	 * @uses UserViewList::getViewXML()
	 * @uses XMLTools::makePagerXML()
	 * @param DOMDocument $xml
	 * @return DOMElement XML output
	 */
	public function getXML(DOMDocument $xml){
		$this->_getDAO();
		$list = $xml->createElement('user-list');

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
				$item = new User($out[User::FIELD_ID], $out);

				/* Set converters */
				if(!is_null($this->create_timestamp_converter)){
					return $item->setCreateTimestampConverter($this->create_timestamp_converter);
				}
				if(!is_null($this->last_timestamp_converter)){
					return $item->setLastTimestampConverter($this->last_timestamp_converter);
				}
				/* Set converters end */

				$list->appendChild($item->getXML($xml));
			} else {
				$list->appendChild($this->view->getViewXML($out[User::FIELD_ID], $out, $xml));
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
	 * @uses UserList::_getDAO()
	 * @uses UserList::$dao
	 * @uses DAO_UserList::getList()
	 * @uses UserList::$filter
	 * @uses UserList::$order
	 * @uses UserList::$offset
	 * @uses UserList::$limit
	 * @uses Query::dataSeek()
	 * @return User
	 */
	public function each(){
		static $res = null;
		if(is_null($res)){
			$this->_getDAO();
			$res = $this->dao->getList($this->filter, $this->order, $this->offset, $this->limit);
		}
		if($out = $res->fetchArray()){
			$item = new User($out[User::FIELD_ID], $out);
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
	 * @uses UserList::$dao
	 * @uses Database::getDAO()
	 * @return boolean true
	 * @internal
	 */
	private function _getDAO(){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO('UserList');
		}
		return true;
	}
}
?>