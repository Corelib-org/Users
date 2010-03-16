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
 * @package AutoGenerated
 * @subpackage user_setting
 * @link http://www.corelib.org/
 * @version 1.1.0 ($Id$)
 * @filesource
 */

//*****************************************************************//
//************************* Event Classes *************************//
//*****************************************************************//
/**
 * UserSetting modified base event.
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
abstract class UserSettingModify implements Event {
	/**
	 * @var UserSetting
	 */
	protected $model;

	/**
	 * Create event and set modified object instance.
	 *
	 * @param UserSetting $model
	 */
	function __construct(UserSetting $model){
		$this->model = $model;
	}

	/**
	 * Get modified object instance.
	 *
	 * @return UserSetting
	 */
	public function getModel(){
		return $this->model;
	}
}

/**
 * Before commit event.
 *
 * This event i triggered each time {@link UserSetting::commit()} is called
 * but before any changes are made to the database
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
class UserSettingModifyBeforeCommit extends UserSettingModify { }

/**
 * Before delete event.
 *
 * This event i triggered each time {@link UserSetting::delete()} is called
 * but before any changes are made to the database
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
class UserSettingModifyBeforeDelete extends UserSettingModify { }

/**
 * After commit event.
 *
 * This event i triggered each time {@link UserSetting::commit()} is called
 * but after changes have been made to the database
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
class UserSettingModifyAfterCommit extends UserSettingModify implements CacheUpdateEvent { }

/**
 * After delete event.
 *
 * This event i triggered each time {@link UserSetting::delete()} is called
 * but after changes have been made to the database
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
class UserSettingModifyAfterDelete extends UserSettingModify implements CacheUpdateEvent { }


//*****************************************************************//
//************************* DAO Interface *************************//
//*****************************************************************//
/**
 * DAO interface for UserSetting.
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
interface DAO_UserSetting {
	/**
	 * Update object data in database.
	 *
	 * @param integer database reference ID
	 * @param DatabaseDataHandler $data
	 * @return boolean true on success, else return false
	 */
	public function update($user, $ident, DatabaseDataHandler $data);
	/**
	 * Get object data from database
	 *
	 * @param integer database reference ID
	 * @return array on success, else return false
	 */
	public function read($user, $ident);
	/**
	 * Remove data from database.
	 *
	 * @param integer database reference ID
	 * @return boolean true on success, else return false
	 */
	public function delete($user, $ident);
}

//*****************************************************************//
//******************** Abstract View classes **********************//
//*****************************************************************//
/**
 * Simple class for handling database cached xml views.
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
abstract class UserSettingView extends View { }

/**
 * Simple class for listing database cached xml views.
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
abstract class UserSettingViewList implements ViewList { }


//*****************************************************************//
//************************** Model class **************************//
//*****************************************************************//
/**
 * UserSetting model.
 *
 * @package AutoGenerated
 * @subpackage user_setting
 */
class UserSetting implements Output,CacheableOutput {
	/* Properties */
	private $user = null;
	private $ident = null;
	private $value = null;
	/* Properties end */

	/* Converter properties */
	/* Converter properties end */

	/* Field constants */
	const FIELD_USER = 'fk_users';
	const FIELD_IDENT = 'ident';
	const FIELD_VALUE = 'value';
	/* Field constants end */

	/* Enum constants */
	/* Enum constants end */

	/**
	 * @var DAO_UserSetting
	 */
	private $dao = null;

	/**
	 * @var DatabaseDataHandler
	 */
	private $datahandler = null;

	/**
	 * @var CacheManagerOutput
	 */
	private $cache = null;

	/**
	 * DAO Class name reference.
	 */
	const DAO = 'UserSetting';


	//*****************************************************************//
	//*************************** Constructor *************************//
	//*****************************************************************//
	/**
	 * Create model UserSetting instance.
	 *
	 * @uses UserSetting::$id
	 * @uses UserSetting::_setFromArray()
	 * @uses UserSetting::$datahandler
	 * @param integer $id object id
	 * @param array $array object data from another data source
	 * @return void
	 */
	public function __construct($user = null, $ident = null, $array = array()){
		$this->user = new User($this->user);
		$this->ident = $ident;
		if(sizeof($array) > 0){
			$this->_setFromArray($array);
		}
		$this->datahandler = new DatabaseDataHandler();
	}


	//*****************************************************************//
	//*************************** Get methods *************************//
	//*****************************************************************//
	/* Get methods */
	/**
	 * Get user.
	 *
	 * @return User user
	 */
	public function getUser(){
		return $this->user;
	}

	/**
	 * Get ident.
	 *
	 * @return string ident
	 */
	public function getIdent(){
		return $this->ident;
	}

	/**
	 * Get value.
	 *
	 * @return string value
	 */
	public function getValue(){
		return $this->value;
	}

	/* Get methods end */


	//*****************************************************************//
	//*************************** Set methods *************************//
	//*****************************************************************//
	/* Set methods */
	/**
	 * Set user.
	 *
	 * @param integer $user
	 * @return boolean true on success, else return false
	 */
	public function setUser(User $user){
		$this->user = $user;
		$this->datahandler->set(self::FIELD_USER, $user->getID());
		return true;
	}

	/**
	 * Set value.
	 *
	 * @param string $value
	 * @return boolean true on success, else return false
	 */
	public function setValue($value){
		$this->value = $value;
		$this->datahandler->set(self::FIELD_VALUE, $value);
		return true;
	}

	/* Set methods end */


	//*****************************************************************//
	//********************* Converter set methods *********************//
	//*****************************************************************//
	/* Converter methods */
	/* Converter methods end */


	//*****************************************************************//
	//************************ Utility methods ************************//
	//*****************************************************************//
	/* Utility methods */
	/* Utility methods end */
	/**
	 * Set cache manager.
	 *
	 * @uses UserSetting::$cache
	 * @param CacheManagerOutput $cache
	 * @return void
	 * @internal
	 */
	public function setCacheManagerOutput(CacheManagerOutput $cache){
		/* Cache references */
		$cache->addObjectReferenceMethod('getUser');
		/* Cache references end */
		$this->cache = $cache;
	}


	//*****************************************************************//
	//********************** Data change methods **********************//
	//*****************************************************************//
	/**
	 * Delete data from database.
	 *
	 * @uses UserSetting::$dao
	 * @uses UserSetting::_getDAO()
	 * @uses DAO_UserSetting::delete);
	 * @return boolean return true on success, else return false
	 */
	public function delete(){
		$this->_getDAO(false);
		return $this->dao->delete($this->user->getID(), $this->ident);
	}

	/**
	 * Read data from database.
	 *
	 * @uses UserSetting::$id
	 * @uses UserSetting::$dao
	 * @uses UserSetting::_getDAO()
	 * @uses UserSetting::_setFromArrray()
	 * @return boolean return true on success, else return false
	 */
	public function read(){
		$this->_getDAO(false);
		if($array = $this->dao->read($this->user->getID(), $this->ident)){
			$this->_setFromArray($array);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Commit changes to database.
	 *
	 * @uses UserSetting::$id
	 * @uses UserSetting::_getDAO()
	 * @uses UserSetting::_create()
	 * @uses UserSetting::_update()
	 * @uses EventHandler::trigger()
	 * @uses UserSettingModifyBeforeCommit
	 * @uses UserSettingModifyAfterCommit
	 * @return boolean return true on success, else return false
	 */
	public function commit(){
		$event = EventHandler::getInstance();
		$this->_getDAO();
		$event->trigger(new UserSettingModifyBeforeCommit($this));
		$r = $this->_update();
		if($r !== false){
			$event->trigger(new UserSettingModifyAfterCommit($this));
		}
		return $r;
	}


	//*****************************************************************//
	//************************* Output methods ************************//
	//*****************************************************************//
	/**
	 * @see Output::getXML()
	 * @param DOMDocument $xml
	 * @return DOMElement XML output
	 */
	public function getXML(DOMDocument $xml){
		$user_setting = $xml->createElement('user-setting');
		/* Get XML method */
		if(!is_null($this->user)){
			$user_setting->setAttribute('user', $this->user->getID());
		}
		if(!is_null($this->ident)){
			$user_setting->setAttribute('ident', $this->getIdent());
		}
		if(!is_null($this->value)){
			$user_setting->setAttribute('value', $this->getValue());
		}
		/* Get XML method end */
		return $user_setting;
	}

	//*****************************************************************//
	//************************ Private methods ************************//
	//*****************************************************************//
	/**
	 * Get Current DAO object instance.
	 *
	 * @uses UserSetting::$dao
	 * @uses UserSetting::read()
	 * @uses UserSetting::DAO
	 * @uses Database::getDAO()
	 * @param boolean $read if true, then read data from database
	 * @return boolean true
	 * @internal
	 */
	private function _getDAO($read=true){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::DAO);
			if($read){
				$this->read();
			}
		}
		return true;
	}
	/**
	 * Create object in database.
	 *
	 * @uses UserSetting::$id
	 * @uses UserSetting::$dao
	 * @uses UserSetting::$datahandler
	 * @uses UserSetting::read()
	 * @uses DAO_UserSetting::create()
	 * @return boolean true on success, else return false
	 * @internal
	 */
	private function _create(){
		if($this->id = $this->dao->create($this->datahandler)){
			$this->read();
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Update object in database.
	 *
	 * @uses UserSetting::$dao
	 * @uses DAO_UserSetting::update();
	 * @return boolean true on success, else return false
	 * @internal
	 */
	private function _update(){
		if($this->dao->update($this->user->getID(), $this->ident, $this->datahandler)){
			$this->read();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Populate model using an array as data source.
	 *
	 * @param array $data Data
	 * @internal
	 */
	protected function _setFromArray($array){
		/* setFromArray method content */
		if(isset($array[self::FIELD_USER])){
			$this->user = new User((integer) $array[self::FIELD_USER]);
		}
		if(isset($array[self::FIELD_IDENT])){
			$this->ident = (string) $array[self::FIELD_IDENT];
		}
		if(isset($array[self::FIELD_VALUE])){
			$this->value = (string) $array[self::FIELD_VALUE];
		}
		/* setFromArray method content end */
	}

}
?>