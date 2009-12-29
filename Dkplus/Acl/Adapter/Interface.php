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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    11.04.2009 11:38:46
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Acl
 * @subpackage Adapter
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Acl_Adapter_Interface{
	/**
	 * <p>Fügt eine Resource hinzu.</p>
	 * @param Zend_Acl_Resource_Interface $resource
	 * @param string|Zend_Acl_Resource_Interface $parent
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function addResource(Zend_Acl_Resource_Interface $resource, $parent = null);
	
	/**
	 * <p>Fügt eine Rolle hinzu.</p>
	 * @param Zend_Acl_Resource_Interface $resource
	 * @param string|Zend_Acl_Role_Interface $parents
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function addRole(Zend_Acl_Role_Interface $role, $parents = null);

	/**
	 * <p>Erlaubt die gegebenen Kombinationen und überschreibt dabei alle Regeln
	 * für die Kind-Rollen/-Resourcen.</p>
	 * @param Zend_Acl_Role_Interface|array|string|null $roles
	 * @param Zend_Acl_Resource_Interface|array|string|null $resources
	 * @param array|string|null $privileges
	 */
	public function allow($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null);

	/**
	 * <p>Verbietet die gegebenen Kombinationen und überschreibt dabei alle Regeln
	 * für die Kind-Rollen/-Resourcen.</p>
	 * @param Zend_Acl_Role_Interface|array|string|null $roles
	 * @param Zend_Acl_Resource_Interface|array|string|null $resources
	 * @param array|string|null $privileges
	 */
	public function deny($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null);
	
	/**
	 * <p>Lädt eine Rollen/Resourcen-Kombination.</p>
	 * @param Zend_Acl_Resource_Interface|string|null $resource
	 * @param Zend_Acl_Role_Interface|string|null $role
	 * @return array
	 */
	public function load($role = null, $resource = null);

	/**
	 * <p>Lädt eine Resource.</p>
	 * @param Zend_Acl_Resource_Interface|string|null $resource
	 * @return array
	 */
	public function loadResource($resource);
	
	/**
	 * <p>Lädt eine Rolle.</p>
	 * @param Zend_Acl_Role_Interface|string|null $role
	 * @return array
	 */
	public function loadRole($role);
	
	/**
	 * <p>Setzt die adaptierte Acl.</p>
	 * @param Dkplus_Acl_Adaptable_Interface $adapted
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function setAdapted(Dkplus_Acl_Adaptable_Interface $adapted);
	
	/**
	 * <p>Entfernt eine Rolle.</p>
	 * @param string $role
	 * @return Dkplus_Acl_Adapter_Interface Provides a fluent interface.
	 */
	public function removeRole($role);
	
	/**
	 * <p>Entfernt eine Resource.</p>
	 * @param string $resource
	 * @return Dkplus_Acl_Adapter_Interface Provides a fluent interface.
	 */
	public function removeResource($resource);

	/**
	 * <p>Fügt einer Rolle eine neue Eltern-Rolle hinzu.</p>
	 * @param string $role
	 * @param string $parentRole
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function addParentRole($role, $parentRole);

	/**
	 * <p>Entfernt eine Eltern-Rolle von einer Rolle.</p>
	 * @param string $role
	 * @param string $parentRole
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function removeParentRole($role, $parentRole);
}