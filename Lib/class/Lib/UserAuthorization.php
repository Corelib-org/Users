<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 *	User authorization classes.
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
//************ UserAuthorizationConfirmEvent class ****************//
//*****************************************************************//
/**
 * Confirm user authorization event.
 *
 * @category corelib
 * @package Users
 * @subpackage Authorization
 * @internal
 */
class UserAuthorizationConfirmEvent extends EventAction  {


	//*****************************************************************//
	//******** UserAuthorizationConfirmEvent class methods ************//
	//*****************************************************************//
	/**
	 * Update and initiate authorization.
	 *
	 * @see EventAction::update()
	 * @param EventRequestStart
	 * @internal
	 */
	public function update(Event $event){
		UserAuthorization::getInstance();
	}
}

//*****************************************************************//
//************* UserAuthorizationStoreEvent class *****************//
//*****************************************************************//
/**
 * Store user authorization event.
 *
 * @category corelib
 * @package Users
 * @subpackage Authorization
 * @internal
 */
class UserAuthorizationStoreEvent extends EventAction {


	//*****************************************************************//
	//********** UserAuthorizationStoreEvent class methods ************//
	//*****************************************************************//
	/**
	 * Store authorization.
	 *
	 * @see EventAction::update()
	 * @param EventRequestEnd
	 * @internal
	 */
	public function update(Event $event){
		UserAuthorization::getInstance()->store();
	}
}

//*****************************************************************//
//************ UserAuthorizationConfirmEvent class ****************//
//*****************************************************************//
/**
 * Append authorization data to xml tree.
 *
 * @category corelib
 * @package Users
 * @subpackage Authorization
 * @internal
 */
class UserAuthorizationPutSettingsXML extends EventAction {


	//*****************************************************************//
	//******** UserAuthorizationConfirmEvent class methods ************//
	//*****************************************************************//
	/**
	 * Append authorization data to xml tree.
	 *
	 * @param EventApplyDefaultSettings $update
	 * @internal
	 */
	public function update(Event $event){
		$auth = UserAuthorization::getInstance();
		if($auth->isAuthorized()){
			$event->getPage()->addSettings($auth);
		}
	}
}


//*****************************************************************//
//***************** EventUserAuthorized class *********************//
//*****************************************************************//
class EventUserAuthorized implements Event {
	private $user = null;

	public function __construct(User $user){
		$this->user = $user;
	}

	public function getUser(){
		return $this->user;
	}
}


//*****************************************************************//
//******************* UserAuthorization class *********************//
//*****************************************************************//
/**
 * Event handler class.
 *
 * @category corelib
 * @package Users
 * @subpackage Authorization
 */
class UserAuthorization implements Singleton,Output {


	//*****************************************************************//
	//************* UserAuthorization class properties ****************//
	//*****************************************************************//
	/**
	 * List of authorized users.
	 *
	 * @var array list of users.
	 * @internal
	 */
	private $users = null;

	/**
	 * Singleton Object Reference.
	 *
	 * @var UserAuthorization
	 * @internal
	 */
	private static $instance = null;


	//*****************************************************************//
	//**************** UserAuthorization class methods ****************//
	//*****************************************************************//
	/**
	 * User authorization class constructor.
	 *
	 * @return void
	 * @internal
	 */
	private function __construct(){ }

	/**
	 * Return instance of UserAuthorization.
	 *
	 * Please refer to the {@link Singleton} interface for complete
	 * description.
	 *
	 * @see Singleton
	 * @uses UserAuthorization::$instance
	 * @return UserAuthorization
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			$session = SessionHandler::getInstance();
			if($session->check(__CLASS__)){
				self::$instance	= unserialize($session->get(__CLASS__));
			} else {
				self::$instance = new UserAuthorization();
			}
		}
		return self::$instance;
	}

	/**
	 * Authorize user.
	 *
	 * @param User $user
	 * @return boolean true on success, else return false
	 */
	public function authorize(User $user){
		$this->users = array();
		SessionHandler::getInstance()->regenerateID();
		return $this->su($user);
	}

	/**
	 * Switch user.
	 *
	 * This method is the equivalent of the unix tool su
	 *
	 * @param User $user
	 * @return boolean true on success, else return false.
	 */
	public function su(User $user){
		if(is_array($this->users)){
			$manager = new UserPermissionManager();
			$user->addComponent($manager);
			$manager->reload();
			$this->users[] = array('user' => $user,
			                       'permissions' => $manager);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Logout current user.
	 *
	 * @return boolean true on success, else return false
	 */
	public function logout(){
		if($this->isAuthorized()){
			array_pop($this->users);
			if(sizeof($this->users) <= 0){
				$this->users = null;
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if there is a authorized user.
	 *
	 * @return boolean if a user is authorized, else return false.
	 */
	public function isAuthorized(){
		if(count($this->users) > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check permissions.
	 *
	 * Check if authorized user has one or more permissions. this method
	 * take any number of arguments with mixed data type of string permission
	 * ident name or {@link UserPermission} instance.
	 *
	 * @param mixed $item string permission ident name or {@link UserPermission} instance
	 * @return unknown_type
	 */
	public function checkPermissions($item=null){
		if($this->isAuthorized()){
			$array = func_get_args();
			foreach (func_get_args() as $permission){
				if($permission instanceof UserPermission){
					if($this->getPermissionManager()->getPermission($permission)){
						return true;
					}
				} else {
					if($this->getPermissionManager()->getPermissionByIdent($permission)){
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Get current authorized user.
	 *
	 * @return User user instance if a user is authorized, else return false.
	 */
	public function getUser(){
		$user = sizeof($this->users) - 1;
		if(isset($this->users[$user])){
			return $this->users[$user]['user'];
		} else {
			return false;
		}
	}

	/**
	 * Get current authorized user permission manager.
	 *
	 * @return UserPermissionManager permission manager instance if a user is authorized, else return false.
	 */
	public function getPermissionManager(){
		$user = sizeof($this->users) - 1;
		if(isset($this->users[$user])){
			return $this->users[$user]['manager'];
		} else {
			return false;
		}
	}

	/**
	 * Store user information in session.
	 *
	 * @return boolean true on success, else return false
	 * @internal
	 */
	public function store(){
		if($this->isAuthorized()){
			$session = SessionHandler::getInstance();
			$session->set(__CLASS__, serialize($this));
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Output XML.
	 *
	 * @param DOMDocument $xml
	 * @return DOMElement
	 */
	public function getXML(DOMDocument $xml){
		if($this->isAuthorized()){
			return $this->getUser()->getXML($xml);
		}
	}

	/**
	 * Prevent class from being cloned.
	 *
	 * @ignore
	 * @return void
	 */
	private function __clone(){ }
}

$eventHandler = EventHandler::getInstance();
$eventHandler->register(new UserAuthorizationConfirmEvent(), 'EventRequestStart');
$eventHandler->register(new UserAuthorizationStoreEvent(), 'EventRequestEnd');
$eventHandler->register(new UserAuthorizationPutSettingsXML(), 'EventApplyDefaultSettings');
?>