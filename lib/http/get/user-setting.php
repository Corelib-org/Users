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
class WebPage extends ManagerPage {
	/* Interface get methods */
	public function UserSettingList($id){
		$user = new User($id);
		if($user->read()){
			$this->addContent(UsersExtensionConfig::getInstance()->getPropertyOutput('user-editmodes'));
			$settings = new UserSettingList();
			$settings->setUserFilter($user);
			$this->addContent($settings);
			$this->addContent($user);
			$this->xsl->addTemplate(CORELIB.'/Users/share/xsl/pages/user-setting/list.xsl');
		} else {
			$this->xsl->addTemplate('share/xsl/pages/404.xsl');
		}
	}

	public function edit($id, $ident){
		$user_setting = new UserSetting($id, $ident);
		if($user_setting->read()){
			$this->xsl->addTemplate(CORELIB.'/Users/share/xsl/pages/user-setting/edit.xsl');
			$this->addContent($user_setting);
		} else {
			$this->xsl->addTemplate('share/xsl/pages/404.xsl');
		}
	}

	public function create($id){
		$user = new User($id);
		if($user->read()){
			$this->xsl->addTemplate(CORELIB.'/Users/share/xsl/pages/user-setting/create.xsl');
		} else {
			$this->xsl->addTemplate('share/xsl/pages/404.xsl');
		}
	}

	public function delete($id, $ident){
		$user_setting = new UserSetting($id, $ident);
		if($user_setting->read()){
			$user_setting->delete();
			$this->xsl->setLocation('corelib/extensions/Users/'.$id.'/Settings/');
		} else {
			$this->xsl->addTemplate('share/xsl/pages/404.xsl');
		}
	}

	/* Interface get methods end */
}
?>