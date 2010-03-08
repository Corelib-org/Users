<?php
/* vim: set tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Get handler for ${submodulename}.
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
 * @author Steffen Soerensen <ss@corelib.org>
 * @copyright Copyright (c) 2005-2008 Steffen Soerensen
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package AutoGenerated
 * @subpackage UserPermission
 * @link http://www.corelib.org/
 * @version 1.0.0 ($Id$)
 */

/**
 * @ignore
 */
class WebPage extends ManagerPage {
	/* Interface post methods */
	public function edit($id){
		$input = InputHandler::getInstance();
		$user_permission = new UserPermission($id);
		if($this->_validateUserPermissionInput($user_permission, 'ident', 'title', 'description')){
			$user_permission->commit();
			$this->post->setLocation('corelib/extensions/Users/Permissions/');
		} else {
			$this->post->setLocation('corelib/extensions/Users/Permissions/'.$id.'/edit/?error', $input->serializePost());
		}
	}

	public function create(){
		$input = InputHandler::getInstance();
		$user_permission = new UserPermission();
		if($this->_validateUserPermissionInput($user_permission, 'ident', 'title', 'description')){
			$user_permission->commit();
			$this->post->setLocation('corelib/extensions/Users/Permissions/');
		} else {
			$this->post->setLocation('corelib/extensions/Users/Permissions/create/?error', $input->serializePost());
		}
	}

	/* Interface post methods end */

	/**
	 * Validate input.
	 */
	private function _validateUserPermissionInput(UserPermission $user_permission /*, [$variables...] */){
		$input = InputHandler::getInstance();
		$checkvalid = func_get_args();
		array_shift($checkvalid);

		/* Interface post validation */
		$input->validatePost('ident', new InputValidatorNotEmpty());
		$input->validatePost('title', new InputValidatorNotEmpty());
		$input->validatePost('description', new InputValidatorNotEmpty());
		/* Interface post validation end */

		if($input->isValidPostVariables($checkvalid)){
			/* Interface actions */
			if($input->isValidPost('ident')){
				$user_permission->setIdent($input->getPost('ident'));
			}
			if($input->isValidPost('title')){
				$user_permission->setTitle($input->getPost('title'));
			}
			if($input->isValidPost('description')){
				$user_permission->setDescription($input->getPost('description'));
			}
			/* Interface actions end */
			return true;
		} else {
			return false;
		}
	}
}
?>