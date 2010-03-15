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

//*****************************************************************//
//************************* Event Classes *************************//
//*****************************************************************//
/**
 * UserSetting modified base event
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
abstract class UserSettingModify implements Event {
	/**
	 * @var UserSetting
	 */
	protected $model;

	/**
	 * Create event and set modified object instance
	 *
	 * @param UserSetting $model
	 */
	function __construct(UserSetting $model){
		$this->model = $model;
	}

	/**
	 * Get modified object instance
	 *
	 * @return UserSetting
	 */
	public function getModel(){
		return $this->model;
	}
}

/**
 * Before commit event
 *
 * This event i triggered each time {@link UserSetting::commit()} is called
 * but before any changes are made to the database
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
class UserSettingModifyBeforeCommit extends UserSettingModify { }

/**
 * Before delete event
 *
 * This event i triggered each time {@link UserSetting::delete()} is called
 * but before any changes are made to the database
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
class UserSettingModifyBeforeDelete extends UserSettingModify { }

/**
 * After commit event
 *
 * This event i triggered each time {@link UserSetting::commit()} is called
 * but after changes have been made to the database
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
class UserSettingModifyAfterCommit extends UserSettingModify { }

/**
 * After delete event
 *
 * This event i triggered each time {@link UserSetting::delete()} is called
 * but after changes have been made to the database
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
class UserSettingModifyAfterDelete extends UserSettingModify { }


//*****************************************************************//
//************************* DAO Interface *************************//
//*****************************************************************//
/**
 * DAO interface for UserSetting
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
interface DAO_UserSetting {
	/**
	 * Update object data in database
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
	 * Remove data from database
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
 * Simple class for handling database cached xml views
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
abstract class UserSettingView extends View { }

/**
 * Simple class for listing database cached xml views
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
abstract class UserSettingViewList implements ViewList { }


//*****************************************************************//
//************************** Model class **************************//
//*****************************************************************//
/**
 * UserSetting model
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 */
class UserSetting implements Output {
	/* Properties */
	private $user = null;
	private $value = null;
	/* Properties end */

	/* Converter properties */
	/* Converter properties end */

	/* Field constants */
	const FIELD_USER_ID = 'fk_users';
	const FIELD_IDENT = 'ident';
	const FIELD_VALUE = 'value';
	/* Field constants end */

	/* Enum constants */
	/* Enum constants end */

	private $dao = null;

	/**
	 * @var DatabaseDataHandler
	 */
	private $datahandler = null;

	const DAO = 'UserSetting';

	/**
	 * Create model UserSetting instance
	 *
	 * @param integer $id object id
	 * @param array $array object data from another data source
	 */
	public function __construct($user = null, $ident = null, $array = array()){
		$this->user = new User($user);
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
	 * Get user
	 *
	 * @return User
	 */
	public function getUser(){
		return $this->user;
	}
	/**
	 * Get ident
	 *
	 * @return
	 */
	public function getIdent(){
		return $this->ident;
	}
	/**
	 * Get value
	 *
	 * @return
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
	 * Set user
	 *
	 * @param integer user
	 * @return boolean true on success, else return false
	 */
	public function setUser(User $user){
		$this->user = $user;
		$this->datahandler->set(self::FIELD_USER_ID, $user->getID());
		return true;
	}
	/**
	 * Set ident
	 *
	 * @param string ident
	 * @return boolean true on success, else return false
	 */
	public function setIdent($ident){
		$this->ident = $ident;
		$this->datahandler->set(self::FIELD_IDENT, $ident);
		return true;
	}
	/**
	 * Set value
	 *
	 * @param string value
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


	//*****************************************************************//
	//********************** Data change methods **********************//
	//*****************************************************************//
	/**
	 * Delete data from database
	 *
	 * @return boolean return true on success, else return false
	 */
	public function delete(){
		return $this->dao->delete($this->user->getID(), $this->ident);
	}

	/**
	 * Read data from database
	 *
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
	 * Commit changes to database
	 *
	 * @return boolean return true on success, else return false
	 */
	public function commit(){
		$event = EventHandler::getInstance();
		$this->_getDAO();
		$event->trigger(new UserSettingModifyBeforeCommit($this));
		$r = $this->_update();
		$event->trigger(new UserSettingModifyAfterCommit($this));
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
		$usersetting = $xml->createElement('user-setting');
		/* Get XML method */
		if(!is_null($this->user)){
			$usersetting->setAttribute('user', $this->user->getID());
		}
		if(!is_null($this->ident)){
			$usersetting->setAttribute('ident', $this->getIdent());
		}
		if(!is_null($this->value)){
			$usersetting->setAttribute('value', $this->getValue());
		}
		/* Get XML method end */
		return $usersetting;
	}

	//*****************************************************************//
	//************************ Private methods ************************//
	//*****************************************************************//
	/**
	 * Get Current DAO object instance
	 *
	 * @param boolean $read if true, then read data from database
	 * @return boolean true
	 */
	protected function _getDAO($read=true){
		if(is_null($this->dao)){
			$this->dao = Database::getDAO(self::DAO);
			if($read){
				$this->read();
			}
		}
		return true;
	}
	/**
	 * Update object in database
	 *
	 * @return boolean true on success, else return false
	 */
	protected function _update(){
		if($this->dao->update($this->user->getID(), $this->ident, $this->datahandler)){
			$this->read();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Populate model using an array as data source
	 *
	 * @param array $data Data
	 */
	protected function _setFromArray($array){
		/* setFromArray method content */
		if(isset($array[self::FIELD_USER_ID])){
			$this->user = new User((integer) $array[self::FIELD_USER_ID]);
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