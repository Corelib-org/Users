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
 * @since Version 4.0
 */


/**
 *	Define default salt for salting passwords if salt not defined.
 */
if(!defined('USER_PASSWORD_SALT')){
	define('USER_PASSWORD_SALT', '6468408f07e495af87fa32cd5b7b876c5d567a20');
}


//*****************************************************************//
//************************* Event Classes *************************//
//*****************************************************************//
/**
 * User modified base event.
 *
 * @category corelib
 * @package Users
 */
abstract class UserModify implements Event {
	/**
	 * @var User
	 */
	protected $model;

	/**
	 * Create event and set modified object instance.
	 *
	 * @param User $model
	 */
	function __construct(User $model){
		$this->model = $model;
	}

	/**
	 * Get modified object instance.
	 *
	 * @return User
	 */
	public function getModel(){
		return $this->model;
	}
}

/**
 * Before commit event.
 *
 * This event i triggered each time {@link User::commit()} is called
 * but before any changes are made to the database
 *
 * @category corelib
 * @package Users
 */
class UserModifyBeforeCommit extends UserModify { }

/**
 * Before delete event.
 *
 * This event i triggered each time {@link User::delete()} is called
 * but before any changes are made to the database
 *
 * @category corelib
 * @package Users
 */
class UserModifyBeforeDelete extends UserModify { }

/**
 * After commit event.
 *
 * This event i triggered each time {@link User::commit()} is called
 * but after changes have been made to the database
 *
 * @category corelib
 * @package Users
 */
class UserModifyAfterCommit extends UserModify implements CacheUpdateEvent { }

/**
 * After delete event.
 *
 * This event i triggered each time {@link User::delete()} is called
 * but after changes have been made to the database
 *
 * @category corelib
 * @package Users
 */
class UserModifyAfterDelete extends UserModify implements CacheUpdateEvent { }


//*****************************************************************//
//************************** Exceptions ***************************//
//*****************************************************************//
/**
 * Invalid username exception
 *
 * @package AutoGenerated
 * @subpackage user
 */
class UserExceptionInvalidUsername extends BaseException { }

/**
 * Invalid email exception
 *
 * @category corelib
 * @package Users
 */
class UserExceptionInvalidEmail extends BaseException { }


//*****************************************************************//
//************************* DAO Interface *************************//
//*****************************************************************//
/**
 * DAO interface for User.
 *
 * @category corelib
 * @package Users
 */
interface DAO_User {
	/**
	 * Save object data in database.
	 *
	 * @param DatabaseDataHandler $data
 	 * @return integer id on success, else return false
 	 */
	public function create(DatabaseDataHandler $data);
	/**
	 * Update object data in database.
	 *
	 * @param integer database reference ID
	 * @param DatabaseDataHandler $data
	 * @return boolean true on success, else return false
	 */
	public function update($id, DatabaseDataHandler $data);
	/**
	 * Get object data from database
	 *
	 * @param integer database reference ID
	 * @return array on success, else return false
	 */
	public function read($id);
	/**
	 * Remove data from database.
	 *
	 * @param integer database reference ID
	 * @return boolean true on success, else return false
	 */
	public function delete($id);
}


//*****************************************************************//
//******************** Abstract View classes **********************//
//*****************************************************************//
/**
 * Simple class for handling database cached xml views.
 *
 * @category corelib
 * @package Users
 */
abstract class UserView extends View { }


//*****************************************************************//
//********************* CompositeUser class ***********************//
//*****************************************************************//
/**
 * User compatible composite.
 *
 * @category corelib
 * @package Users
 */
abstract class CompositeUser extends CompositeOutput {


	//*****************************************************************//
	//*************** CompositeUser class properties ******************//
	//*****************************************************************//
	/**
	 * Instance owner.
	 *
	 * @var User
	 */
	private $user = null;


	//*****************************************************************//
	//***************** CompositeUser class methods *******************//
	//*****************************************************************//
	/**
	 * Get owner instance.
	 *
	 * @return User
	 */
	public function getUser(){
		return $this->user;
	}

	/**
	 * Add component.
	 *
	 * @see CompositeOutput::addComponent()
	 * @param CompositeUser $component
	 */
	public function addComponent(Composite $component, $reference=null){
		assert('$component instanceof CompositeUser');
		$component->_setUser($this->getUser());
		return parent::addComponent($component, $reference);
	}

	/**
	 * Set user.
	 *
	 * @param $user
	 * @return User
	 */
	private function _setUser(User $user){
		$this->user = $user;
		return $user;
	}
}


//*****************************************************************//
//************************** Model class **************************//
//*****************************************************************//
/**
 * User model.
 *
 * @category corelib
 * @package Users
 */
class User extends CompositeUser implements CacheableOutput {
	/* Properties */
	private $id = null;
	private $username = null;
	private $password = null;
	private $email = null;
	private $activated = false;
	private $activation_string = null;
	private $deleted = null;
	private $create_timestamp = null;
	private $last_timestamp = null;
	/* Properties end */

	/* Converter properties */
	private $create_timestamp_converter = null;
	private $last_timestamp_converter = null;
	/* Converter properties end */

	/* Field constants */
	const FIELD_ID = 'pk_users';
	const FIELD_USERNAME = 'username';
	const FIELD_PASSWORD = 'password';
	const FIELD_EMAIL = 'email';
	const FIELD_ACTIVATED = 'activated';
	const FIELD_ACTIVATION_STRING = 'activation_string';
	const FIELD_DELETED = 'deleted';
	const FIELD_CREATE_TIMESTAMP = 'create_timestamp';
	const FIELD_LAST_TIMESTAMP = 'last_timestamp';
	/* Field constants end */

	/* Enum constants */
	/* Enum constants end */

	/**
	 * @var DAO_User
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
	const DAO = 'User';


	//*****************************************************************//
	//*************************** Constructor *************************//
	//*****************************************************************//
	/**
	 * Create model User instance.
	 *
	 * @uses User::$id
	 * @uses User::_setFromArray()
	 * @uses User::$datahandler
	 * @param integer $id object id
	 * @param array $array object data from another data source
	 * @return void
	 */
	public function __construct($id = null, $array = array()){
		$this->id = $id;
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
	 * Get id.
	 *
	 * @return integer id
	 */
	public function getID(){
		return $this->id;
	}

	/**
	 * Get username.
	 *
	 * @return string username
	 */
	public function getUsername(){
		return $this->username;
	}

	/**
	 * Get email.
	 *
	 * @return string email
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Get activated.
	 *
	 * @return boolean activated
	 */
	public function getActivated(){
		return $this->activated;
	}

	/**
	 * Get activation_string.
	 *
	 * @return string activation_string
	 */
	public function getActivationString(){
		return $this->activation_string;
	}

	/**
	 * Get deleted.
	 *
	 * @return boolean deleted
	 */
	public function getDeleted(){
		return $this->deleted;
	}

	/**
	 * Get create_timestamp.
	 *
	 * @return mixed create_timestamp
	 */
	public function getCreateTimestamp(){
		if(!is_null($this->create_timestamp_converter)){
			return $this->create_timestamp_converter->convert($this->create_timestamp);
		} else {
			return $this->create_timestamp;
		}
	}

	/**
	 * Get last_timestamp.
	 *
	 * @return mixed last_timestamp
	 */
	public function getLastTimestamp(){
		if(!is_null($this->last_timestamp_converter)){
			return $this->last_timestamp_converter->convert($this->last_timestamp);
		} else {
			return $this->last_timestamp;
		}
	}

	/* Get methods end */


	//*****************************************************************//
	//*************************** Set methods *************************//
	//*****************************************************************//
	/* Set methods */
	/**
	 * Set password.
	 *
	 * @param string $password
	 * @return boolean true on success, else return false
	 */
	public function setPassword($password){
		
		$this->password = sha1(USER_PASSWORD_SALT.$password);
		$this->datahandler->set(self::FIELD_PASSWORD, $this->password);
		return true;
	}

	/**
	 * Set activated.
	 *
	 * @param bool $activated
	 * @return boolean true on success, else return false
	 */
	public function setActivated($activated){
		$this->activated = $activated;
		$this->datahandler->set(self::FIELD_ACTIVATED, $activated);
		return true;
	}

	/**
	 * Set deleted.
	 *
	 * @param bool $deleted
	 * @return boolean true on success, else return false
	 */
	public function setDeleted($deleted){
		$this->deleted = $deleted;
		$this->datahandler->set(self::FIELD_DELETED, $deleted);
		return true;
	}

	/**
	 * Set last_timestamp.
	 *
	 * @param integer $last_timestamp
	 * @return boolean true on success, else return false
	 */
	public function setLastTimestamp($last_timestamp){
		$this->last_timestamp = $last_timestamp;
		$this->datahandler->set(self::FIELD_LAST_TIMESTAMP, $last_timestamp);
		return true;
	}
	/**
	 * Set username.
	 *
	 * @param string $username
	 * @return boolean true on success, else return false
	 */
	public function setUsername($username){
		if($this->isUsernameAvailable($username)){
			$this->datahandler->set(self::FIELD_USERNAME, $username);
			$this->username = $username;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set email.
	 *
	 * @param string $email
	 * @return boolean true on success, else return false
	 */
	public function setEmail($email){
		if($this->isEmailAvailable($email)){
			$this->datahandler->set(self::FIELD_EMAIL, $email);
			$this->email = $email;
			return true;
		} else {
			return false;
		}
	}

	/* Set methods end */


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
	//************************ Utility methods ************************//
	//*****************************************************************//
	/* Utility methods */
	/**
	 * Get data by username.
	 *
	 * @param string username
	 * @return boolean true on success, else return false
	 */
	public function getByUsername($username){
		$this->_getDAO(false);
		$this->_setFromArray($this->dao->getByUsername($username));
		if(is_null($this->id)){
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get data by email.
	 *
	 * @param string email
	 * @return boolean true on success, else return false
	 */
	public function getByEmail($email){
		$this->_getDAO(false);
		$this->_setFromArray($this->dao->getByEmail($email));
		if(is_null($this->id)){
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Is username combination available.
	 *
	 * @param string $username
	 * @return boolean true on success, else return false
	 */
	public function isUsernameAvailable($username){
		$this->_getDAO(false);
		return $this->dao->isUsernameAvailable($this->id, $username);
	}

	/**
	 * Is email combination available.
	 *
	 * @param string $email
	 * @return boolean true on success, else return false
	 */
	public function isEmailAvailable($email){
		$this->_getDAO(false);
		return $this->dao->isEmailAvailable($this->id, $email);
	}

	/* Utility methods end */
	/**
	 * Set cache manager.
	 *
	 * @uses User::$cache
	 * @param CacheManagerOutput $cache
	 * @return void
	 * @internal
	 */
	public function setCacheManagerOutput(CacheManagerOutput $cache){
		/* Cache references */
		/* Cache references end */
		$this->cache = $cache;
	}

	/**
	 * Compare plaintext password with stored password.
	 *
	 * @param string $password
	 * @return boolean true if passwords match, else return false
	 */
	public function checkPassword($password){
		return ($this->password == sha1(USER_PASSWORD_SALT.$password));
	}

	/**
	 * Activate user.
	 *
	 * If a activations string is provided it will be checked
	 * before activating the user, and if the activation string
	 * does not match the user will not be activated and the method
	 * will return false. if activation string is omitted the user
	 * will be activated without checking the activations string.
	 *
	 * @param string $activation_string
	 * @return return boolean true on success, else return false
	 */
	public function activate($activation_string = null){
		if(!is_null($activation_string) && $activation_string != $this->activation_string){
			return false;
		}
		$this->setActivationString(null);
		$this->setActivated(true);
		return true;
	}

	/**
	 * Get user.
	 *
	 * @see CompositeUser::getUser()
	 */
	public function getUser(){
		return $this;
	}

	/**
	 * Get composite.
	 *
	 * @return User
	 */
	public function getComposite(){
		return $this;
	}


	//*****************************************************************//
	//********************** Data change methods **********************//
	//*****************************************************************//
	/**
	 * Delete data from database.
	 *
	 * @uses User::$dao
	 * @uses User::_getDAO()
	 * @uses DAO_User::delete);
	 * @return boolean return true on success, else return false
	 */
	public function delete(){
		$this->_getDAO(false);
		return $this->dao->delete($this->id);
	}

	/**
	 * Read data from database.
	 *
	 * @uses User::$id
	 * @uses User::$dao
	 * @uses User::_getDAO()
	 * @uses User::_setFromArrray()
	 * @return boolean return true on success, else return false
	 */
	public function read(){
		$this->_getDAO(false);
		if($array = $this->dao->read($this->id)){
			$this->_setFromArray($array);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Commit changes to database.
	 *
	 * @uses User::$id
	 * @uses User::_getDAO()
	 * @uses User::_create()
	 * @uses User::_update()
	 * @uses EventHandler::trigger()
	 * @uses UserModifyBeforeCommit
	 * @uses UserModifyAfterCommit
	 * @return boolean return true on success, else return false
	 */
	public function commit(){
		$event = EventHandler::getInstance();
		$this->_getDAO();
		$event->trigger(new UserModifyBeforeCommit($this));
		if(is_null($this->id)){
			$r = $this->_create();
		} else {
			$r = $this->_update();
		}
		if($r !== false){
			$event->trigger(new UserModifyAfterCommit($this));
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
		$user = $xml->createElement('user');
		/* Get XML method */
		if(!is_null($this->id)){
			$user->setAttribute('id', $this->getID());
		}
		if(!is_null($this->username)){
			$user->setAttribute('username', $this->getUsername());
		}
		if(!is_null($this->email)){
			$user->setAttribute('email', $this->getEmail());
		}
		if(!is_null($this->activated)){
			if(!$this->activated){
				$user->setAttribute('activated', 'false');
			} else {
				$user->setAttribute('activated', 'true');
			}
		}
		if(!is_null($this->activation_string)){
			$user->setAttribute('activation-string', $this->getActivationString());
		}
		if(!is_null($this->deleted)){
			if(!$this->deleted){
				$user->setAttribute('deleted', 'false');
			} else {
				$user->setAttribute('deleted', 'true');
			}
		}
		if(!is_null($this->create_timestamp)){
			$user->setAttribute('create-timestamp', $this->getCreateTimestamp());
		}
		if(!is_null($this->last_timestamp)){
			$user->setAttribute('last-timestamp', $this->getLastTimestamp());
		}

		$this->getComponentsXML($user);

		/* Get XML method end */
		return $user;
	}

	//*****************************************************************//
	//************************ Private methods ************************//
	//*****************************************************************//
	/**
	 * Get Current DAO object instance.
	 *
	 * @uses User::$dao
	 * @uses User::read()
	 * @uses User::DAO
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
	 * @uses User::$id
	 * @uses User::$dao
	 * @uses User::$datahandler
	 * @uses User::read()
	 * @uses DAO_User::create()
	 * @return boolean true on success, else return false
	 * @throws UserExceptionInvalidEmail
	 * @throws UserExceptionInvalidUsername
	 * @internal
	 */
	private function _create(){
		if(!$this->getActivated()){
			$this->activation_string = sha1($this->email.$this->username.$this->password);
			$this->datahandler->set(self::FIELD_ACTIVATION_STRING, $this->activation_string);
		}
		if(is_null($this->password)){
			$this->setPassword(PasswordGenerator::create(10));
		}
		if(!empty($this->email) && !empty($this->username) && $this->id = $this->dao->create($this->datahandler)){
			$this->read();
			return true;
		} else {
			if(empty($this->email)){
				throw new UserExceptionInvalidEmail('Can\'t create user with empty email address.', E_USER_WARNING);
			}
			if(empty($this->username)){
				throw new UserExceptionInvalidUsername('Can\'t create user with empty email username.', E_USER_WARNING);
			}
			return false;
		}
	}
	/**
	 * Update object in database.
	 *
	 * @uses User::$dao
	 * @uses DAO_User::update();
	 * @return boolean true on success, else return false
	 * @internal
	 */
	private function _update(){
		if($this->dao->update($this->id, $this->datahandler)){
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
		if(isset($array[self::FIELD_ID])){
			$this->id = (integer) $array[self::FIELD_ID];
		}
		if(isset($array[self::FIELD_USERNAME])){
			$this->username = (string) $array[self::FIELD_USERNAME];
		}
		if(isset($array[self::FIELD_PASSWORD])){
			$this->password = (string) $array[self::FIELD_PASSWORD];
		}
		if(isset($array[self::FIELD_EMAIL])){
			$this->email = (string) $array[self::FIELD_EMAIL];
		}
		if(isset($array[self::FIELD_ACTIVATED])){
			$this->activated = (bool) $array[self::FIELD_ACTIVATED];
		}
		if(isset($array[self::FIELD_ACTIVATION_STRING])){
			$this->activation_string = (string) $array[self::FIELD_ACTIVATION_STRING];
		}
		if(isset($array[self::FIELD_DELETED])){
			$this->deleted = (bool) $array[self::FIELD_DELETED];
		}
		if(isset($array[self::FIELD_CREATE_TIMESTAMP])){
			$this->create_timestamp = (integer) $array[self::FIELD_CREATE_TIMESTAMP];
		}
		if(isset($array[self::FIELD_LAST_TIMESTAMP])){
			$this->last_timestamp = (integer) $array[self::FIELD_LAST_TIMESTAMP];
		}
		/* setFromArray method content end */
	}

}
?>