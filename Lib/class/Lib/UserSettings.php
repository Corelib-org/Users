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


//*****************************************************************//
//************ UserSettingsCommitChangesEvent class ***************//
//*****************************************************************//
/**
 * Commit setting changes event action.
 *
 * @category corelib
 * @package Users
 * @internal
 */
class UserSettingsCommitChangesEvent extends EventAction {


	//*****************************************************************//
	//******** UserSettingsCommitChangesEvent class methods ***********//
	//*****************************************************************//
	/**
	 * Commit changes.
	 *
	 * @see EventAction::update()
	 * @param EventRequestEnd
	 * @internal
	 */
	public function update(Event $event){
		UserSettings::getInstance()->commit();
	}
}


//*****************************************************************//
//********************** UserSettings class ***********************//
//*****************************************************************//
/**
 * User settings manager.
 *
 * The user setting manager allows you to manage specific
 * user settings for the current authed user.
 *
 * @category corelib
 * @package Users
 */
class UserSettings extends UserAuthorizationPointer {


	//*****************************************************************//
	//**************** UserSettings class properties ******************//
	//*****************************************************************//
	/**
	 * Singleton Object Reference.
	 *
	 * @var UserAuthorization
	 * @internal
	 */
	private static $instance = null;

	/**
	 * Current permission manager instance.
	 *
	 * @var UserSettingManager
	 * @internal
	 */
	private $manager = null;


	//*****************************************************************//
	//****************** UserSettings class methods *******************//
	//*****************************************************************//
	/**
	 * Return instance of UserSettings.
	 *
	 * Please refer to the {@link Singleton} interface for complete
	 * description.
	 *
	 * @see Singleton
	 * @uses UserSettings::$instance
	 * @return UserSettings
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new UserSettings();
		}
		return self::$instance;
	}

	/**
	 * User authorization class constructor.
	 *
	 * @return void
	 * @internal
	 */
	private function __construct(){
		$this->_getUserSettingsManager();
	}

	/**
	 * Get current user settings manager instance.
	 *
	 * @return UserSettingManager
	 * @internal
	 */
	private function _getUserSettingsManager(){
		if(is_null($this->manager)){
			if(!$this->manager = $this->getComponent()){
				$this->manager = new UserSettingManager();
				$this->addComponent($manager, UserAuthorization::getInstance()->getUser());
			}
		}
		return $this->manager;
	}

	/**
	 * Commit changes to database.
	 *
	 * @return boolean true on success, else return false
	 * @internal
	 */
	public function commit(){
		return $this->manager->commit();
	}

	/**
	 * Set user setting.
	 *
	 * @param string $ident setting identification string
	 * @param string $value setting value
	 * @return boolean true on success, else return false
	 */
	public function set($ident, $value){
		return $this->manager->set($ident, $value);
	}

	/**
	 * Delete user setting.
	 *
	 * @param string $ident setting identification string
	 * @return boolean true on success, else return false
	 */
	public function delete($ident){
		return $this->manager->delete($ident);
	}

	/**
	 * Get user setting.
	 *
	 * @param string $ident setting identification string
	 * @param string $default default setting value
	 * @return mixed string setting on success, else return default value
	 */
	public function get($ident, $default=null){
		return $this->manager->get($ident, $default);
	}
}

EventHandler::getInstance()->register(new UserSettingsCommitChangesEvent(), 'EventRequestEnd');
?>