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
 * @internal
 */

//*****************************************************************//
//******************* UserSettingManager class ********************//
//*****************************************************************//
/**
 * User setting manager class.
 *
 * @category corelib
 * @package Users
 * @subpackage UserSettings
 * @internal
 */
class UserSettingManager extends CompositeUser {


	//*****************************************************************//
	//************* UserSettingManager class properties ***************//
	//*****************************************************************//
	/**
	 * @var array list of {@UserSetting}
	 * @internal
	 */
	private $settings = array();

	/**
	 * @var array list of changed {@UserSetting}
	 * @internal
	 */
	private $changed = array();


	//*****************************************************************//
	//*************** UserSettingManager class methods ****************//
	//*****************************************************************//
	/**
	 * Get user id.
	 *
	 * @return mixed integer user id or null
	 * @internal
	 */
	private function _getUserID(){
		return $this->getUser()->getID();
	}

	/**
	 * Delete user setting.
	 *
	 * @param string $ident setting identification string
	 * @return boolean true on success, else return false
	 * @internal
	 */
	public function delete($ident){
		$id = $this->_getUserID();
		if(isset($this->settings[$ident])){
			if(!is_null($id)){
				$this->settings[$ident]->delete();
			}
			unset($this->settings[$ident]);
		}
		return true;
	}

	/**
	 * Get user setting.
	 *
	 * @param string $ident setting identification string
	 * @param string $default default setting value
	 * @return mixed string setting on success, else return default value
	 * @internal
	 */
	public function get($ident, $default=null){
		$id = $this->_getUserID();
		if(isset($this->settings[$ident])){
			return $this->settings[$ident]->getValue();
		} else if(!is_null($id)) {
			$this->settings[$ident] = new UserSetting($id, $ident);
			if(!$this->settings[$ident]->read()){
				$this->settings[$ident]->setValue($default);
			}
			return $this->settings[$ident]->getValue();
		} else {
			return $default;
		}
	}

	/**
	 * Set user setting.
	 *
	 * @param string $ident setting identification string
	 * @param string $value setting value
	 * @return boolean true on success, else return false
	 * @internal
	 */
	public function set($ident, $value){
		$id = $this->_getUserID();
		if(!isset($this->settings[$ident]) || $this->settings[$ident]->getValue() != $value){
			$this->settings[$ident] = new UserSetting($id, $ident);
			if(!is_null($id)){
				$this->changed[$ident] = $this->settings[$ident];
			}
			$this->settings[$ident]->setValue($value);
		}
		return true;
	}

	/**
	 * Commit changes to database.
	 *
	 * @return boolean return true on success, else return false
	 * @internal
	 */
    public function commit(){
		foreach ($this->changed as $change){
			$change->commit();
		}
		$this->changed = array();
		return true;
    }

	/**
	 * Get content XML.
	 *
	 * @see Output::getXML()
	 * @param DOMDocument $xml
	 * @return DOMElement XML output
	 * @internal
	 */
	public function getXML(DOMDocument $xml){
		$settings = $xml->createElement('user-settings');
		foreach ($this->settings as $setting){
			$settings->appendChild($setting->getXML($xml));
		}
		return $settings;
	}
}
?>