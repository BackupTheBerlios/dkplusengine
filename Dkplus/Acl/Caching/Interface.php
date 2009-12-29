<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category	Dkplus
 * @package		Acl
 * @subpackage	Caching
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    27.07.2009 00:06:55
 */

/**
 * @category	Dkplus
 * @package		Acl
 * @subpackage	Caching
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Acl_Caching_Interface{
	/**
	 * Markiert eine Kombination in der Datenbank als geladen.
	 * @param Zend_Acl_Role_Interface|string $role
	 * @param Zend_Acl_Rule_Interface|string $resource
	 * @return Dkplus_Acl_Caching_Db Provides a fluent interface.
	 */
	public function change($role = null, $resource = null);

	/**
	 * Gibt alle Veränderungen zurück.
	 * @return array
	 */
	public function getChanges();


}