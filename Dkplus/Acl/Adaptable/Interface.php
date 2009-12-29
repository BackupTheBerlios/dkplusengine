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
 * @category   Dkplus
 * @package    Dkplus_Acl
 * @subpackage Adaptable
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    11.04.2009 11:44:38
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Acl
 * @subpackage Adaptable
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Acl_Adaptable_Interface{
	/**
	 * <p>Überprüft, ob ein Adapter gesetzt ist.</p>
	 * @return boolean
	 */
	public function hasAdapter();
	
	/**
	 * <p>Setzt den Adapter für die Acl.</p>
	 * @param Dkplus_Acl_Adapter_Interface $adapter
	 * @return Dkplus_Acl_Adaptable_Interface
	 */
	public function setAdapter(Dkplus_Acl_Adapter_Interface $adapter);
	
	/**
	 * <p>Prüft ob eine Kombination von Rolle und Resource bereits geladen wurden.</p>
	 * @param mixed $role
	 * @param mixed $resource
	 * @return boolean
	 */
	public function hasLoaded($role = null, $resource = null);

	/**
	 * <p>Überprüft, ob eine Resource bereits geladen wurde.</p>
	 * @param string $resource
	 * @return boolean
	 */
	public function hasResourceLoaded($resource);

	/**
	 * <p>Überprüft, ob eine Rolle bereits geladen wurde.</p>
	 * @param string $role
	 * @return boolean
	 */
	public function hasRoleLoaded($role);

	/**
	 * <p>Fügt einer Rolle eine neue Eltern-Rolle hinzu.</p>
	 * @param string|Zend_Acl_Role $role
	 * @param string|Zend_Acl_Role $parentRole
	 * @return Dkplus_Acl_Adaptable_Interface
	 */
	public function addParentRole($role, $parentRole);

	/**
	 * <p>Entfernt eine Eltern-Rolle von einer Rolle.</p>
	 * @param string|Zend_Acl_Role $role
	 * @param string|Zend_Acl_Role $parentRole
	 * @return Dkplus_Acl_Adaptable_Interface
	 */
	public function removeParentRole($role, $parentRole);
}