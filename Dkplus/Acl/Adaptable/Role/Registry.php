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
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    29.06.2009 00:03:24
 */

/**
 * @see Zend_Acl_Role_Registry
 */
//require-once 'Zend/Acl/Role/Registry.php';

/**
 * @category   Dkplus
 * @package    Dkplus_Acl
 * @subpackage Adaptable
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Acl_Adaptable_Role_Registry extends Zend_Acl_Role_Registry{

	/**
	 * <p>Entfernt eine Eltern-Rolle aus einer Rolle.</p>
	 * @param string|Zend_Acl_Role $role
	 * @param string|Zend_Acl_Role $parentRole
	 * @return Dkplus_Acl_Adaptable_Role_Registry
	 * @throws {@link Zend_Acl_Role_Registry_Exception}
	 */
	public function removeParentRole($role, $parentRole){
		$role = $role instanceOf Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (string) $role;
		$parentRole = $parentRole instanceOf Zend_Acl_Role_Interface
			? $parentRole->getRoleId()
			: (string) $parentRole;

		//Überprüfung, ob die Rolle/ParentRolle existiert
		if(
			!$this->has($role)
		){
			/**
			 * @see Zend_Acl_Role_Registry_Exception
			 */
			//require-once 'Zend/Acl/Role/Registry/Exception.php';
			throw new Zend_Acl_Role_Registry_Exception('There is no role id '.$role);
		}

		//Überprüfung, ob die Rolle/ParentRolle existiert
		if(
			!$this->has($parentRole)
		){
			/**
			 * @see Zend_Acl_Role_Registry_Exception
			 */
			//require-once 'Zend/Acl/Role/Registry/Exception.php';
			throw new Zend_Acl_Role_Registry_Exception('There is no role id '.$parentRole);
		}

		//Überprüfen, ob $parentRole überhaupt eine Eltern-Rolle ist.
		if(
			!$this->inherits($role, $parentRole, true)
		){
			/**
			 * @see Zend_Acl_Role_Registry_Exception
			 */
			//require-once 'Zend/Acl/Role/Registry/Exception.php';
			return $this;
			throw new Zend_Acl_Role_Registry_Exception(
				'Role '.$role.' has no parent role '.$parentRole
			);
		}

		//Entfernen der Parent-Role
		unset($this->_roles[$role]['parents'][$parentRole]);

		//Entfernen bei den Kind-Rollen
		unset($this->_roles[$parentRole]['children'][$role]);

		return $this;

	}

	/**
	 * <p>Fügt einer Rolle eine neue Eltern-Rolle hinzu.</p>
	 * @param string|Zend_Acl_Role $role
	 * @param string|Zend_Acl_Role $parentRole
	 * @return Dkplus_Acl_Adaptable_Role_Registry
	 * @throws {@link Zend_Acl_Role_Registry_Exception}
	 */
	public function addParentRole($role, $parentRole){
		$role = $role instanceOf Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (string) $role;
		$parentRole = $parentRole instanceOf Zend_Acl_Role_Interface
			? $parentRole->getRoleId()
			: (string) $parentRole;
		
		//Überprüfung, ob die Rolle/ParentRolle existiert
		if(
			!$this->has($role)
		){
			/**
			 * @see Zend_Acl_Role_Registry_Exception
			 */
			//require-once 'Zend/Acl/Role/Registry/Exception.php';
			throw new Zend_Acl_Role_Registry_Exception('There is no role id '.$role);
		}

		//Überprüfung, ob die Rolle/ParentRolle existiert
		if(
			!$this->has($parentRole)
		){
			/**
			 * @see Zend_Acl_Role_Registry_Exception
			 */
			//require-once 'Zend/Acl/Role/Registry/Exception.php';
			throw new Zend_Acl_Role_Registry_Exception('There is no role id '.$parentRole);
		}

		//Überprüfen, ob $parentRole nicht schon eine Eltern-Rolle ist.
		if(
			$this->inherits($role, $parentRole, true)
		){
			/**
			 * @see Zend_Acl_Role_Registry_Exception
			 */
			//require-once 'Zend/Acl/Role/Registry/Exception.php';
			throw new Zend_Acl_Role_Registry_Exception(
				'Role '.$role.' has already a parent role '.$parentRole
			);
		}

		//Hinzufügen bei den Parent-Rollen
		$roleParents = $this->_roles[$role]['parents'];
		$roleParents[$parentRole] = $this->get($parentRole);
		$this->_roles[$role]['parents'] = $roleParents;

		//Hinzufügen bei den Kind-Rollen
		$this->_roles[$parentRole]['children'][$role] = $this->get($role);

		return $this;
	}
}