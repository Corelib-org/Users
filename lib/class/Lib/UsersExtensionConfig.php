<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * User extension handler class.
 *
 * This file contains the corelib user extension class for handling the
 * corelib xml configuration.
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
 *
 * @author Steffen Sørensen <ss@corelib.org>
 * @copyright Copyright (c) 2010
 * @license http://www.gnu.org/copyleft/gpl.html
 * @link http://www.corelib.org/
 * @version 2.0.0 ($Id: EventHandler.php 5186 2010-03-05 20:28:04Z wayland $)
 */

//*****************************************************************//
//****************** UsersExtensionConfig class *******************//
//*****************************************************************//
/**
 * Page factory.
 *
 * @category corelib
 * @package Users
 *
 * @author Steffen Sørensen <ss@corelib.org>
 */

class UsersExtensionConfig extends CorelibManagerExtension {

	//*****************************************************************//
	//*********** UsersExtensionConfig class properties ***************//
	//*****************************************************************//
	/**
	 * Singleton Object Reference.
	 *
	 * @var UsersExtensionConfig
	 * @internal
	 */
	private static $instance = null;

	/**
	 * Get UsersExtensionConfig instance.
	 *
	 * Please refer to the {@link Singleton} interface for complete
	 * description.
	 *
	 * @see Singleton
	 * @uses UsersExtensionConfig::$instance
	 * @return UsersExtensionConfig
	 * @internal
	 */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new UsersExtensionConfig();
		}
		return self::$instance;
	}
}
?>