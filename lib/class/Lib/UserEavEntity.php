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
//************************* Define Constants **********************//
//*****************************************************************//
define('USER_EAV_ENTITY_TYPE_IDENT', 'USER-INFORMATION');


//*****************************************************************//
//******************* UserSettingManager class ********************//
//*****************************************************************//
/**
 * User EAV Entity class.
 *
 * @category corelib
 * @package Users
 * @internal
 */
class UserEavEntity extends CompositeUser {
	/**
	 * @var EavEntity
	 */
	private $entity = null;

	private $type = null;

	public function setReadAttributes($state=true){
		$this->_getEavEntity();
		$this->entity->setReadAttributes($state);
		return true;
	}

	public function setEavAttributeSet(EavAttributeSet $eav_attribute_set){
		$this->_getEavEntity();
		$this->entity->setEavAttributeSet($eav_attribute_set);
		return true;
	}

	public function getXML(DOMDocument $xml){
		$this->_getEavEntity();
		return $this->entity->getXML($xml);
	}

	public function read(){
		$this->_getEavEntity();
		$this->_getEavEntityType();
		return $this->entity->getByReferenceObject($this->type, get_class($this->getUser()), $this->getUser()->getID());
	}

	public function setEavAttributeData($field='EAV'){
		$this->_getEavEntity();
		$this->_getEavEntityType();
		$this->entity->setEavAttributeData($field);
	}

	public function commit(){
		$this->_getEavEntity();
		$this->_getEavEntityType();
		$this->entity->setReferenceObject($this->type, get_class($this->getUser()), $this->getUser()->getID());
		$this->entity->commit();
	}

	private function _getEavEntity(){
		if(is_null($this->entity)){
			$this->entity = new EavEntity();
		}
	}

	private function _getEavEntityType(){
		if(is_null($this->type)){
			$this->type = new EavEntityType();
			$this->type->getByIdent(USER_EAV_ENTITY_TYPE_IDENT);
		}
	}
}