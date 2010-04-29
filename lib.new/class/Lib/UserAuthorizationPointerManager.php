<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 *	User authorization pointer manager classes.
 *
 *	<i>No Description</i>
 *
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
 * @category corelib
 * @package Users
 * @package Authorization
 *
 * @author Steffen Sørensen <ss@corelib.org>
 * @copyright Copyright (c) 2010
 * @license http://www.gnu.org/copyleft/gpl.html
 * @link http://www.corelib.org/
 * @version 2.0.0 ($Id: EventHandler.php 5186 2010-03-05 20:28:04Z wayland $)
 */

//*****************************************************************//
//******* UserAuthorizationPointerManagerStoreEvent class *********//
//*****************************************************************//
/**
 * Store user authorization pointer manager event action.
 *
 * @category corelib
 * @package Users
 * @subpackage Authorization
 * @internal
 */
class UserAuthorizationPointerManagerStoreEvent extends EventAction {


	//*****************************************************************//
	//**** UserAuthorizationPointerManagerStoreEvent class methods ****//
	//*****************************************************************//
	/**
	 * Store authorization pointer manager data.
	 *
	 * @see EventAction::update()
	 * @param EventRequestEnd
	 * @internal
	 */
	public function update(Event $event){
		UserAuthorizationPointerManager::getInstance()->store();
	}
}


//*****************************************************************//
//************* UserAuthorizationPointerManager class *************//
//*****************************************************************//
/**
 * User authorization pointer manager.
 *
 * @category corelib
 * @package Users
 * @subpackage Authorization
 * @internal
 */
final class UserAuthorizationPointerManager implements Singleton {


	//*****************************************************************//
	//******* UserAuthorizationPointerManager class properties ********//
	//*****************************************************************//
	/**
	 * Singleton Object Reference.
	 *
	 * @var UserAuthorization
	 * @internal
	 */
	private static $instance = null;

	/**
	 * @var list of pointers
	 * @internal
	 */
	private $pointers = array();


	//*****************************************************************//
	//******** UserAuthorizationPointerManager class constants ********//
	//*****************************************************************//
	/**
	 * Anonymous user id.
	 *
	 * @var string
	 * @internal
	 */
	const ANONYMOUS_ID = 'anonymous';


	//*****************************************************************//
	//******** UserAuthorizationPointerManager class methods **********//
	//*****************************************************************//
	/**
	 * User authorization pointer manager class constructor.
	 *
	 * @return void
	 * @internal
	 */
	private function __construct(){	}

	/**
	 * Return instance of UserAuthorizationPointerManager.
	 *
	 * Please refer to the {@link Singleton} interface for complete
	 * description.
	 *
	 * @see Singleton
	 * @uses UserAuthorizationPointerManager::$instance
	 * @return UserAuthorizationPointerManager
	 * @internal
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			$session = SessionHandler::getInstance();
			if($session->check(__CLASS__)){
				self::$instance	= unserialize($session->get(__CLASS__));
			} else {
				self::$instance = new UserAuthorizationPointerManager();
			}
		}
		return self::$instance;
	}

	/**
	 * Add component to user.
	 *
	 * @param string $class class name
	 * @param CompositeUser $component Component
	 * @param User $user
	 * @param string $reference component reference
	 * @return boolean true on success, else return false
	 * @internal
	 */
	public function addComponent($class, CompositeUser $component, User $user=null, $reference=null){
		$this->pointers[$class][$this->_getUserID($user)] = $this->_getUser($user)->addComponent($component, $reference);
		return true;
	}

	/**
	 * Get user component.
	 *
	 * @param string $class class name
	 * @param User $user
	 * @return CompositeUser on success, else return false
	 * @internal
	 */
	public function getComponent($class, User $user=null){
		$user = $this->_getUser($user);
		$id = $this->_getUserID($user);
		if(isset($this->pointers[$class][$id])){
			return $user->getComponent($this->pointers[$class][$id]);
		} else {
			return false;
		}
	}

	/**
	 * Store data to a session variable.
	 *
	 * @return void
	 * @internal
	 */
	public function store(){
		$session = SessionHandler::getInstance();
		$session->set(get_class($this), serialize($this));
	}

	/**
	 * Get user object.
	 *
	 * if $user parameters is null get user from {@link UserAuthorization::getUser()}
	 *
	 * @param User $user
	 * @return User
	 * @internal
	 */
	private function _getUser(User $user=null){
		if(is_null($user)){
			$user = UserAuthorization::getInstance()->getUser();
		}
		return $user;
	}

	/**
	 * Get user id.
	 *
	 * @param User $user
	 * @return User
	 * @internal
	 */
	private function _getUserID(User $user=null){
		$user = $this->_getUser($user);
		$id = $user->getID();
		if(is_null($id)){
			$id = self::ANONYMOUS_ID;
		}
		return $id;
	}
}


//*****************************************************************//
//**************** UserAuthorizationPointer class *****************//
//*****************************************************************//
/**
 * User authorization pointer.
 *
 * use this class if you want to extend authorized users with new
 * components and want to access them at a later point in the session.
 *
 * @category corelib
 * @package Users
 * @subpackage Authorization
 */
abstract class UserAuthorizationPointer {


	//*****************************************************************//
	//************ UserAuthorizationPointer class methods *************//
	//*****************************************************************//
	/**
	 * Add component to user.
	 *
	 * @param CompositeUser $component
	 * @param User $user
	 * @param string $reference component reference
	 * @return boolean true on success, else return false.
	 */
	public function addComponent(CompositeUser $component, User $user=null, $reference=null){
		return UserAuthorizationPointerManager::getInstance()->addComponent(get_class($this), $component, $user, $reference);
	}

	/**
	 * Get by user.
	 *
	 * @param User $user
	 * @return UserComposite on success, else return false
	 */
	public function getComponent(User $user=null){
		return UserAuthorizationPointerManager::getInstance()->getComponent(get_class($this), $user);
	}

}
?>