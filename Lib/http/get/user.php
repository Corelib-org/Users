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
 * @subpackage User
 * @link http://www.corelib.org/
 * @version 1.0.0 ($Id$)
 * @filesource
 */

/**
 * @ignore
 */
class WebPage extends ManagerPage {
	/* Interface get methods */
	public function UserList(){
		$list = new UserList();
		$list->setLimit(20);
		$list->setPage($this->getPagingPage());

		$input = InputHandler::getInstance();
		if($input->validateGet('view', new InputValidatorEnum('active', 'inactive', 'deleted', 'all'))){
			switch($input->getGet('view')){
				case 'inactive':
					$list->setActivatedFilter(false);
					$list->setDeletedFilter(false);
					break;
				case 'deleted':
					$list->setDeletedFilter(true);
					break;
				case 'active':
					$list->setActivatedFilter(true);
					$list->setDeletedFilter(false);
					break;
				}
		} else {
			$list->setActivatedFilter(true);
			$list->setDeletedFilter(false);
		}

		$this->addContent($list);
		$this->xsl->addTemplate(CORELIB.'/Users/share/xsl/pages/user/list.xsl');
	}

	public function edit($id){
		$user = new User($id);
		if($user->read()){
			$this->xsl->addTemplate(CORELIB.'/Users/share/xsl/pages/user/edit.xsl');
			$this->addContent($user);
		} else {
			$this->xsl->addTemplate('share/xsl/pages/404.xsl');
		}
	}

	public function create(){
		$this->xsl->addTemplate(CORELIB.'/Users/share/xsl/pages/user/create.xsl');
	}

	public function delete($id){
		$user = new User($id);
		if($user->read()){
			$user->setDeleted(true);
			$user->commit();
			$this->xsl->setLocation('corelib/extensions/Users/');
		} else {
			$this->xsl->addTemplate('share/xsl/pages/404.xsl');
		}
	}

	/* Interface get methods end */
}
?>