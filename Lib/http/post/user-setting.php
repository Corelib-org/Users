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
 * @subpackage UserSetting
 * @link http://www.corelib.org/
 * @version 1.0.0 ($Id$)
 */

/**
 * @ignore
 */
class WebPage extends ZhostingPagePost {
	/* Interface post methods */
	public function edit($id, $ident){
		$input = InputHandler::getInstance();
		$user_setting = new UserSetting($id, $ident);
		if($user_setting->read() && $this->_validateUserSettingInput($user_setting, 'value')){
			$user_setting->commit();
			$this->post->setLocation('corelib/extensions/Users/'.$id.'/Settings/');
		} else {
			$this->post->setLocation('corelib/extensions/Users/'.$id.'/Settings/'.$ident.'/?error', $input->serializePost());
		}
	}

	public function create($id){
		$input = InputHandler::getInstance();
		if($input->validatePost('ident', new InputValidatorRegex('/[a-zA-Z\-_0-9]+/'))){
			$user_setting = new UserSetting($id, $input->getPost('ident'));
		}

		if(isset($user_setting) && $this->_validateUserSettingInput($user_setting, 'value')){
			$user_setting->commit();
			$this->post->setLocation('corelib/extensions/Users/'.$id.'/Settings/');
		} else {
			$this->post->setLocation('corelib/extensions/Users/'.$id.'/Settings/add/?error', $input->serializePost());
		}
	}

	/* Interface post methods end */

	/**
	 * Validate input.
	 */
	private function _validateUserSettingInput(UserSetting $user_setting /*, [$variables...] */){
		$input = InputHandler::getInstance();
		$checkvalid = func_get_args();
		array_shift($checkvalid);

		/* Interface post validation */
		$input->validatePost('user', new InputValidatorModelExists('User'));
		$input->validatePost('value', new InputValidatorNotEmpty());
		/* Interface post validation end */

		if($input->isValidPostVariables($checkvalid)){
			/* Interface actions */
			if($input->isValidPost('user')){
				$user_setting->setUser(new User($input->getPost('user')));
			}
			if($input->isValidPost('value')){
				$user_setting->setValue($input->getPost('value'));
			}
			/* Interface actions end */
			return true;
		} else {
			return false;
		}
	}
}
?>