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
 * @version    11.04.2009 12:38:40
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Dkplus_Acl_Adapter_Interface
 */
//require-once 'Dkplus/Acl/Adapter/Interface.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Acl
 * @subpackage Adapter
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Acl_Adapter_Db implements Dkplus_Acl_Adapter_Interface{
	const RESOURCE_ID			= 'id';
	const RESOURCE_NAME			= 'name';
	const RESOURCE_PARENT		= 'parent';

	const ROLE_ID				= 'id';
	const ROLE_NAME				= 'name';

	const ROLE_PARENT			= 'parent';
	const ROLE_PARENT_ID		= 'id';
	const ROLE_PARENT_PARENT	= 'parent';

	const PRIVILEGE_ID			= 'id';
	const PRIVILEGE_NAME		= 'name';

	const RULE_ROLE				= 'role';
	const RULE_RESOURCE			= 'resource';
	const RULE_PRIVILEGE		= 'privilege';
	const RULE_TYPE				= 'type';
	const RULE_ASSERT			= 'assert';
	
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_resourceTable = null;
	
	/**
	 * @var array
	 */
	protected $_resourceColumns = array();
	
	/**
	 * <p>Gibt die Resourcen-Tabelle zurück.</p>
	 * @return Zend_Db_Table_Abstract
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the resource-table has not been set before.
	 */
	protected function _getResourceTable(){
		if(
			is_null($this->_resourceTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Resource-Table must be set before.');
		}
		return $this->_resourceTable;
	}
	
	/**
	 * <p>Gibt alle Spalten der Resourcen-Spalten zurück.</p>
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the resource-table has not been set before.
	 * @deprecated {@link _getResourceColumn()} should be used.
	 */
	protected function _getResourceColumns(){
		trigger_error(__METHOD__.' is marked as deprecated.', E_USER_NOTICE);
		if(
			is_null($this->_resourceTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Resource-Table must be set before.');
		}
		return $this->_resourceColumns;
	}

	/**
	 * <p>Gibt eine Spalte der Resourcen-Spalten zurück.</p>
	 * @param string $col
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the resource-table has
	 * not been set before or if the column does not exists.
	 */
	protected function _getResourceColumn($col){
		$col = (string) $col;
		if(
			is_null($this->_resourceTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Resource-Table must be set before.');
		}

		if(
			!isset($this->_resourceColumns[$col])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('First parameter is no valid col.');
		}

		return $this->_resourceColumns[$col];
	}
	
	/**
	 * @param Zend_Db_Table_Abstract $table
	 * @param array $columns Must contain "id" and "name" and can also contain "parent".
	 * @return Dkplus_Acl_Adapter_Interface
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if there are not all columns
	 * given.
	 */
	public function setResourceTable(Zend_Db_Table_Abstract $table, array $columns){
		$this->_resourceTable = $table;
		if(
			!isset($columns[self::RESOURCE_ID])
			OR !is_string($columns[self::RESOURCE_ID])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'
				.self::RESOURCE_ID.'" that must be an string.');
		}
		if(
			!isset($columns[self::RESOURCE_NAME])
			OR !is_string($columns[self::RESOURCE_NAME])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'
				.self::RESOURCE_NAME.'" that must be an string.');
		}
		if(
			isset($columns[self::RESOURCE_PARENT])
			AND !is_string($columns[self::RESOURCE_PARENT])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter\'s element "'
				.self::RESOURCE_PARENT.'" must be an string.');
		}
		
		$this->_resourceColumns = $columns;
		return $this;		
	}
	
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_roleTable = null;
	
	/**
	 * @var array
	 */
	protected $_roleColumns = array();
	
	/**
	 * <p>Gibt die Rollen-Tabelle zurück.</p>
	 * @return Zend_Db_Table_Abstract
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the role-table has not
	 * been set before.
	 */
	protected function _getRoleTable(){
		if(
			is_null($this->_roleTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Role-Table must be set before.');
		}
		return $this->_roleTable;
	}
	
	/**
	 * <p>Gibt alle Spalten der Rollen-Spalten zurück.</p>
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the role-table has not been set before.
	 * @deprecated {@link _getRoleColumn()} should be used.
	 */
	protected function _getRoleColumns(){
		trigger_error(__METHOD__.' is marked as deprecated.', E_USER_NOTICE);
		if(
			is_null($this->_roleTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Role-Table must be set before.');
		}
		return $this->_roleColumns;
	}

	/**
	 * <p>Gibt eine Spalte der Rollen-Spalten zurück.</p>
	 * @param string $col
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the role-table has
	 * not been set before or if the column does not exists.
	 */
	protected function _getRoleColumn($col){
		$col = (string) $col;
		if(
			is_null($this->_roleTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Role-Table must be set before.');
		}


		if(
			!isset($this->_roleColumns[$col])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('First parameter '.$col.' is no valid col.');
		}

		return $this->_roleColumns[$col];
	}
	
	/**
	 * @param Zend_Db_Table_Abstract $table
	 * @param array $columns Must contain "id" and "name" and can also contain "parent".
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function setRoleTable(Zend_Db_Table_Abstract $table, array $columns){
		$this->_roleTable = $table;
		if(
			!isset($columns[self::ROLE_ID])
			OR !is_string($columns[self::ROLE_ID])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::ROLE_ID.'" that must be an string.');
		}
		if(
			!isset($columns[self::ROLE_NAME])
			OR !is_string($columns[self::ROLE_NAME])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::ROLE_NAME.'" that must be an string.');
		}

		if(
			isset($columns[self::ROLE_PARENT])
			AND !is_string($columns[self::ROLE_PARENT])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter\'s element "'.self::ROLE_PARENT.'" must be an string.');
		}
		elseif(
			isset($columns[self::ROLE_PARENT])
			&& !is_null($this->_roleParentTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter\'s element "'
					.self::ROLE_PARENT
					.'" should not be set if you are using the roleParentTable.');
		}
		
		$this->_roleColumns = $columns;
		return $this;		
	}

	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_roleParentTable = null;

	/**
	 * @var array
	 */
	protected $_roleParentColumns = array();

	/**
	 * <p>Gibt die Rollen-Eltern-Tabelle zurück.</p>
	 * @return Zend_Db_Table_Abstract
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the role-parent-table has
	 * not been set before.
	 */
	protected function _getRoleParentTable(){
		if(
			is_null($this->_roleParentTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Role-Parent-Table must be set before.');
		}
		return $this->_roleParentTable;
	}

	/**
	 * <p>Gibt eine Spalte der Rollen-Eltern-Spalten zurück.</p>
	 * @param string $col
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the role-parent-table has
	 * not been set before or if the column does not exists.
	 */
	protected function _getRoleParentColumn($col){
		$col = (string) $col;
		if(
			is_null($this->_roleParentTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Role-Parent-Table must be set before.');
		}

		if(
			!isset($this->_roleParentColumns[$col])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('First parameter is no valid col.');
		}

		return $this->_roleParentColumns[$col];
	}

	/**
	 * @param Zend_Db_Table_Abstract $table
	 * @param array $columns Must contain "id" and "parent".
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function setRoleParentTable(Zend_Db_Table_Abstract $table, array $columns){
		$this->_roleParentTable = $table;
		if(
			!isset($columns[self::ROLE_PARENT_ID])
			OR !is_string($columns[self::ROLE_PARENT_ID])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::ROLE_PARENT_ID.'" that must be an string.');
		}
		if(
			!isset($columns[self::ROLE_PARENT_PARENT])
			OR !is_string($columns[self::ROLE_PARENT_PARENT])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::ROLE_PARENT_PARENT.'" that must be an string.');
		}

		if(
			!is_null($this->_roleTable)
			&& isset($this->_roleColumns[self::ROLE_PARENT])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The roleParentTable '
					.'" should not be set if you are using the roleTable-inheritance.');
		}

		$this->_roleParentColumns = $columns;
		return $this;
	}

	/**
	 * <p>Prüft, ob die Vererbung über einen Eintrag in der Role-Tabelle
	 * vorgenommen wird.</p>
	 * @return boolean
	 */
	protected function _isUsingRoleTableInheritance(){
		return is_array($this->_roleColumns)
			&& isset($this->_roleColumns[self::ROLE_PARENT]);
	}

	/**
	 * <p>Prüft, ob die Vererbung über eine eigene Tabelle vorgenommen wird.</p>
	 * @return boolean
	 */
	protected function _isUsingRoleParentTableInheritance(){
		return !is_null($this->_roleParentTable);
	}
	
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_privilegeTable = null;
	
	/**
	 * @var array
	 */
	protected $_privilegeColumns = array();
	
	/**
	 * <p>Prüft, ob Privilegien verwendet werden.</p>
	 * @return boolean
	 */
	public function hasPrivilegeTable(){
		return !is_null($this->_privilegeTable);	
	}
	
	/**
	 * <p>Gibt die Privilegien-Tabelle zurück.</p>
	 * @return Zend_Db_Table_Abstract
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the privilege-table has not been set before.
	 */
	protected function _getPrivilegeTable(){
		if(
			is_null($this->_privilegeTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Privilege-Table must be set before.');
		}
		return $this->_privilegeTable;
	}
	
	/**
	 * <p>Gibt alle Privilegien-Spalten zurück.</p>
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the privilege-table has not been set before.
	 * @deprecated {@link _getPrivilegeColumn()} should be used.
	 */
	protected function _getPrivilegeColumns(){
		trigger_error(__METHOD__.' is marked as deprecated.', E_USER_NOTICE);
		if(
			is_null($this->_privilegeTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Privilege-Table must be set before.');
		}
		return $this->_privilegeColumns;
	}

	/**
	 * <p>Gibt eine Spalte der Privilegien-Spalten zurück.</p>
	 * @param string $col
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the privilege-table has
	 * not been set before or if the column does not exists.
	 */
	protected function _getPrivilegeColumn($col){
		$col = (string) $col;
		if(
			is_null($this->_privilegeTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Privilege-Table must be set before.');
		}

		if(
			!isset($this->_privilegeColumns[$col])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('First parameter is no valid col.');
		}

		return $this->_privilegeColumns[$col];
	}
	
	/**
	 * <p>Setzt die Privilegien-Tabelle und gibt die entsprechenden Spalten an.</p>
	 * @param Zend_Db_Table_Abstract $table
	 * @param array $columns Must contain "id" and "name"
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function setPrivilegeTable(Zend_Db_Table_Abstract $table, array $columns){
		$this->_privilegeTable = $table;
		if(
			!isset($columns[self::PRIVILEGE_ID])
			OR !is_string($columns[self::PRIVILEGE_ID])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::PRIVILEGE_ID.'" that must be an string.');
		}
		if(
			!isset($columns[self::PRIVILEGE_NAME])
			OR !is_string($columns[self::PRIVILEGE_NAME])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::PRIVILEGE_NAME.'" that must be an string.');
		}
		
		$this->_privilegeColumns = $columns;
		return $this;		
	}
	
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_ruleTable = null;
	
	/**
	 * @var array
	 */
	protected $_ruleColumns = array();
	
	/**
	 * <p>Gibt die Tabelle mit den Regeln zurück.</p>
	 * @return Zend_Db_Table_Abstract
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the rule-table has not been set before.
	 */
	protected function _getRuleTable(){
		if(
			is_null($this->_ruleTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Rule-Table must be set before.');
		}
		return $this->_ruleTable;
	}
	
	/**
	 * <p>Gibt alle Regel-Spalten zurück.</p>
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the rule-table has not been set before.
	 * @deprecated {@link _getRoleColumn()} should be used.
	 */
	protected function _getRuleColumns(){
		trigger_error(__METHOD__.' is marked as deprecated.', E_USER_NOTICE);
		if(
			is_null($this->_ruleTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Rule-Table must be set before.');
		}
		return $this->_ruleColumns;
	}

	/**
	 * <p>Gibt eine Spalte der Regel-Spalten zurück.</p>
	 * @param string $col
	 * @return array
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the rule-table has
	 * not been set before or if the column does not exists.
	 */
	protected function _getRuleColumn($col){
		$col = (string) $col;
		if(
			is_null($this->_ruleTable)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Rule-Table must be set before.');
		}

		if(
			!isset($this->_ruleColumns[$col])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('First parameter is no valid col.');
		}

		return $this->_ruleColumns[$col];
	}

	/**
	 * Überprüft, ob eine Spaltenart gesetzt ist.
	 * @param string $col
	 * @return boolean
	 */
	protected function _hasRuleColumn($col){
		$col = (string) $col;
		return isset($this->_ruleColumns[$col]);
	}
	
	/**
	 * <p>Setzt die Tabelle und die dazugehörigen Spalten der Regel-Tabelle.</p>
	 * @param Zend_Db_Table_Abstract $table
	 * @param array $columns Must contain "resource" and "role" and can also contain "privilege".
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function setRuleTable(Zend_Db_Table_Abstract $table, array $columns){
		$this->_ruleTable = $table;
		if(
			!isset($columns[self::RULE_RESOURCE])
			OR !is_string($columns[self::RULE_RESOURCE])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::RULE_RESOURCE.'" that must be an string.');
		}
		if(
			!isset($columns[self::RULE_ROLE])
			OR !is_string($columns[self::RULE_ROLE])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter must contain an element "'.self::RULE_ROLE.'" that must be an string.');
		}
		if(
			isset($columns[self::RULE_PRIVILEGE])
			AND !is_string($columns[self::RULE_PRIVILEGE])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter\'s element "'.self::RULE_PRIVILEGE.'" must be an string.');
		}

		if(
			isset($columns[self::RULE_ASSERT])
			AND !is_string($columns[self::RULE_ASSERT])
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('The second parameter\'s element "'.self::RULE_ASSERT.'" must be an string.');
		}
		
		$this->_ruleColumns = $columns;
		return $this;		
	}
	
	/**
	 * @var Dkplus_Acl_Adaptable_Interface
	 */
	protected $_adapted = null;
	
	/**
	 * <p>Setzt die adaptierte Acl, für die geladen wird.</p>
	 * @return Dkplus_Acl_Adaptable_Interface
	 */
	public function setAdapted(Dkplus_Acl_Adaptable_Interface $adapted){
		$this->_adapted = $adapted;
		return $this;	
	}
	
	/**
	 * <p>Gibt die adaptierte Acl zurück.</p>
	 * @return Dkplus_Acl_Adaptable_Interface
	 * @throws {@link Dkplus_Acl_Adapter_Exception} if the adapter has not been added to an adaptable-acl instance.
	 */
	protected function _getAdapted(){
		if(
			is_null($this->_adapted)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Adapter has not been added to an adaptable-acl instance.');
		}
		return $this->_adapted;
	}
	
	/**
	 * <p>Fügt eine Resource hinzu.</p>
	 * @param Zend_Acl_Resource_Interface $resource
	 * @param Zend_Acl_Resource_Interface|string|null $parent
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function addResource(Zend_Acl_Resource_Interface $resource, $parent = null){
		if(
			!is_null($parent)
		){
			$parentName = $parent instanceOf Zend_Acl_Resource_Interface
				? $parent->getResourceId()
				: (string) $parent;
			$rowParent = $this->_getResourceTable()->fetchRow(
				$this->_getResourceTable()->select()
				->where($this->_getResourceColumn(self::RESOURCE_NAME).' = ?', $parentName)
			);
			
			if(
				is_null($rowParent)
			){
				/**
				 * @see Dkplus_Acl_Adapter_Exception
				*/
				//require-once 'Dkplus/Acl/Adapter/Exception.php';
				throw new Dkplus_Acl_Adapter_Exception('Parent Resource "'.$parentName.'" were not found in Database.');
			}
			unset($parentName);
			$parent = $rowParent->__get($this->_getResourceColumn(self::RESOURCE_ID));
		}
		$this->_getResourceTable()->insert(
			array(
				$this->_getResourceColumn(self::RESOURCE_NAME)
					=> $resource->getResourceId(),
				$this->_getResourceColumn(self::RESOURCE_PARENT)
					=> $parent
			)
		);
		return $this;
	}
	
	/**
	 * <p>Fügt eine Rolle hinzu.</p>
	 * @param Zend_Acl_Role_Interface $role
	 * @param Zend_Acl_Role_Interface|array|string|null $parents
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function addRole(Zend_Acl_Role_Interface $role, $parents = null){
		$parents = is_null($parents)
			? array()
			: (
				is_array($parents)
				? $parents
				: array( $parents )
			);
		foreach($parents AS $k => $parent){
			$parents[$k] = $parent instanceOf Zend_Acl_Role_Interface
				? $parent->getRoleId()
				: (string) $parent;
		}
		
		//Prüfen, ob ein Parent möglich ist
		if(
			(
				count($parents) > 1
				&& $this->_isUsingRoleTableInheritance()
			)
			|| (
				count($parents) > 0
				&& !$this->_isUsingRoleParentTableInheritance()
				&& !$this->_isUsingRoleTableInheritance()
			)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('There are no possibilities '
				.'to save parent roles.');
		}


		//Nur eine Parent-Role und die befindet sich in der Role-Table:
		if(
			$this->_isUsingRoleTableInheritance()
		){
			$parents = null;
			if(
				count($parents) > 0
			){
				$rowParent = $this->_getRoleTable()->fetchRow(
					$this->_getRoleTable()->select()
						->where($this->_getRoleColumn(self::ROLE_NAME).' = ?', $parents[0])
				);

				if(
					is_null($rowParent)
				){
					/**
					 * @see Dkplus_Acl_Adapter_Exception
					*/
					//require-once 'Dkplus/Acl/Adapter/Exception.php';
					throw new Dkplus_Acl_Adapter_Exception('Role '.$parents[0]
						.' does not exists.');
				}

				$parents = $rowParent->__get($this->_getRoleColumn(self::ROLE_ID));
			}

			$this->_getRoleTable()->insert(
				array(
					$this->_getRoleColumn(self::ROLE_NAME)
						=> $role->getRoleId(),
					$this->_getRoleColumn(self::ROLE_PARENT)
						=> $parents
				)
			);
			return $this;
		}


		//Mehrere Parent-Roles die sich nicht in der Role-Table befinden:
		if(
			count($parents) > 0
		){
			$rowsetParents = $this->_getRoleTable()->fetchAll(
				$this->_getRoleTable()->select()
					->where($this->_getRoleColumn(self::ROLE_NAME).' IN (?)', $parents)
			);

			if(
				count($rowsetParents) < count($parents)
			){
				/**
				 * @see Dkplus_Acl_Adapter_Exception
				*/
				//require-once 'Dkplus/Acl/Adapter/Exception.php';
				throw new Dkplus_Acl_Adapter_Exception('Not all parent roles exist.');
			}

			$parents = array();
			foreach($rowsetParents AS $rowParent){
				$parents[] = $rowParent->__get($this->_getRoleColumn(self::ROLE_ID));
			}
		}

		//Hinzufügen der Role
		$rowRole = $this->_getRoleTable()->fetchNew();
		$rowRole->__set($this->_getRoleColumn(self::ROLE_NAME), $role->getRoleId());
		$rowRole->save();

		//Speichern der ELtern-Kind Zuweisungen
		if(
			count($parents) > 0
		){
			foreach($parents AS $parent){
				$this->_getRoleParentTable()->insert(
					array(
						$this->_getRoleParentColumn(self::ROLE_PARENT_ID)
							=> $rowRole->__get($this->_getRoleColumn(self::ROLE_ID)),
						$this->_getRoleParentColumn(self::ROLE_PARENT_PARENT)
							=> $parent
					)
				);
			}
		}

		return $this;
	}
	
	/**
	 * <p>Lädt eine Rollen/Resourcen-Kombination.</p>
	 * <p>Die zurückgegebenen Arrays sehen wie folgt aus:
	 * <code>
	 * $array = array(
	 *	0 => array(
	 *		array(
	 *			0	=> 'role1',
	 *			1	=> array()
	 *		),
	 *		array(
	 *			0	=> 'role2',
	 *			1	=> array('role1')
	 *		)
	 *	),
	 *	1 => array(
	 *		array(
	 *			'resource1',
	 *			null
	 *		),
	 *		array(
	 *			'resource2',
	 *			'resource1'
	 *		)
	 *	),
	 *	2 => array(
	 *		array(
	 *			'type'		=> 0,
	 *			'resource'	=> 'resource1',
	 *			'role'		=> 'role1',
	 *			'privilege'	=> 'priv1',	//optional, wenn eine priv-tabelle angegeben wurde
	 *		),
	 *		array(
	 *			'type'		=> 1,
	 *			'resource'	=> 'resource1',
	 *			'role'		=> 'role2',
	 *			'privilege'	=> null,	//optional, wenn eine priv-tabelle angegeben wurde
	 *		),
	 *		array(
	 *			'type'		=> 1,
	 *			'resource'	=> 'resource2',
	 *			'role'		=> 'role1',
	 *			'privilege'	=> 'priv3',	//optional, wenn eine priv-tabelle angegeben wurde
	 *		)
	 *	)
	 * );
	 * </code>
	 * </p>
	 * @param Zend_Acl_Resource_Interface|string|null $resource
	 * @param Zend_Acl_Role_Interface|string|null $role
	 * @return array
	 */
	public function load($role = null, $resource = null){
		if(
			$this->_getAdapted()->hasLoaded($resource, $role)
		){
			return $this;
		}

		$role = $role instanceOf Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (
				is_null($role)
				? null
				: (string) $role
			);

		$resource = $resource instanceOf Zend_Acl_Resource_Interface
			? $resource->getResourceId()
			: (
				is_null($resource)
				? null
				: (string) $resource
			);			
		
		//Holen der Resources und der Roles
		$resources = is_null($resource)
			? array()
			: $this->_loadResources($resource, $role);
		$roles = is_null($role)
			? array()
			: $this->_loadRoles($role, $resource);

		//Umwandeln in Rückgabeformat/Format zum Laden der Rollen
		$arrRoleNames = array();
		$arrRoleIds = array();
		$blnSearchRoleNull = false;
		foreach($roles AS $role){
			if(
				!is_null($role)
			){
				$arrRoleNames[] = array(
					$role[$this->_getRoleColumn(self::ROLE_NAME)],
					$role['parent']
				);
				$arrRoleIds[] = $role[$this->_getRoleColumn(self::ROLE_ID)];
			}
			else{
				$blnSearchRoleNull = true;
			}
		}
		$arrResNames = array();
		$arrResIds = array();
		$blnSearchResNull = false;
		foreach($resources AS $arrResource){
			if(
				!is_null($arrResource)
			){
				$arrResNames[] = 
					array(
						$arrResource[$this->_getResourceColumn(self::RESOURCE_NAME)],
						(
							isset($arrResource[$this->_getResourceColumn(self::RESOURCE_PARENT)])
							? $arrResource[$this->_getResourceColumn(self::RESOURCE_PARENT)]
							: null
						)
					);
				$arrResIds[] =
					$arrResource[$this->_getResourceColumn(self::RESOURCE_ID)];
			}
			else{
				$blnSearchResNull = true;
			}
		}
		
		return array(
			$arrRoleNames,
			$arrResNames,
			$this->_loadRules($arrRoleIds, $arrResIds, $blnSearchRoleNull, $blnSearchResNull)
		);
	}
	
	/**
	 * <p>Lädt eine Resource.</p>
	 * <p>Der zurückgegebene Array sieht ist wie folgt aufgebaut:
	 * <code>
	 * $array = array(
	 *	0 => array(
	 *		0 => 'resource1',
	 *		1 => null
	 *	),
	 *	1 => array(
	 *		0 => 'resource2',
	 *		1 => null
	 *	),
	 *	2 => array(
	 *		0 => 'resource3',
	 *		1 => 'resource1'
	 *	),
	 *	3 => array(
	 *		0 => 'resource4',
	 *		1 => 'resource2'
	 *	)
	 * );
	 * </code>
	 * </p>
	 * @param Zend_Acl_Resource_Interface|string|null $resource
	 * @return array
	 */
	public function loadResource($resource){
		$resourceId = $resource instanceof Zend_Acl_Resource_Interface
			? $resource->getResourceId()
			: (string) $resource;		
		$arrResources = $this->_loadResources($resourceId);
		$arrReturn = array();

		foreach($arrResources AS $arrResource){
			$arrReturn[] = array(
				0 => $arrResource[$this->_getResourceColumn(self::RESOURCE_NAME)],
				1 => (
					isset($arrResource[$this->_getResourceColumn(self::RESOURCE_PARENT)])
					? $arrResource[$this->_getResourceColumn(self::RESOURCE_PARENT)]
					: null
				)
			);			
		}
		return $arrReturn;
	}
	
	/**
	 * <p>Lädt eine Rolle.</p>
	 * <p>Der zurückgegebene Array sieht ist wie folgt aufgebaut:
	 * <code>
	 * $array = array(
	 *	0 => array(
	 *		0 => 'role1',
	 *		1 => array()
	 *	),
	 *	1 => array(
	 *		0 => 'role2',
	 *		1 => array()
	 *	),
	 *	2 => array(
	 *		0 => 'role3',
	 *		1 => array('role2')
	 *	),
	 *	3 => array(
	 *		0 => 'role4',
	 *		1 => array('role1', 'role2')
	 *	)
	 * );
	 * </code>
	 * </p>
	 * @param Zend_Acl_Role_Interface|string|null $role
	 * @return array
	 */
	public function loadRole($role){
		$roleId = $role instanceof Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (string) $role;
		$arrRoles = $this->_loadRoles($roleId);

		$arrReturn = array();
		foreach($arrRoles AS $role){
			$arrReturn[] = array(
				0 => $role[$this->_getRoleColumn(self::ROLE_NAME)],
				1 => $role['parent']
			);
		}
		return $arrReturn;
	}
	
	/**
	 * <p>Lädt die Resourcen und all ihre Parent-Resources und gibt sie als Array zurück.</p>
	 * <p>Der zurückgegebene Array sieht wie folgt aus:
	 * <code>
	 * $array = array(
	 *	null,
	 *	array(
	 *		'<RESOURCE_ID>'		=> '5'
	 *		'<RESOURCE_NAME>'	=> 'resource1',
	 *		'<RESOURCE_PARENT>'	=> null,
	 *	),
	 *	array(
	 *		'<RESOURCE_ID>'		=> '7'
	 *		'<RESOURCE_NAME>'	=> 'resource2',
	 *		'<RESOURCE_PARENT>'	=> 'resource1',
	 *	),
	 *	array(
	 *		'<RESOURCE_ID>'		=> '8'
	 *		'<RESOURCE_NAME>'	=> 'resource3',
	 *		'<RESOURCE_PARENT>'	=> 'resource2',
	 *	)
	 * );
	 * </code>
	 * </p>
	 * @param string $resource
	 * @param string|null $role
	 * @return array
	 */
	protected function _loadResources($resource, $role = null){
		$resource = (string) $resource;
		$resourceName = $resource;
		$role = is_null($role)
			? null
			: $role;

		$resource = $this->_getResourceTable()->fetchRow(
			$this->_getResourceTable()->select()
			->where($this->_getResourceColumn(self::RESOURCE_NAME).' = ?', $resource)
		);
		
		//Resource existiert nicht, wir geben einen leeren Array zurück.
		if(
			is_null($resource)
		){
			return array();
		}


		//In den zurückzugebenen Resourcen
		$resources = array($resource->toArray());


		//Die Resource selbst haben wir jetzt, nun kommen die Parents dran.
		if(
			isset($this->_resourceColumns[self::RESOURCE_PARENT])
			&& !is_null($resource->__get($this->_getResourceColumn(self::RESOURCE_PARENT)))
		){
			do{
				$intId = $resource->__get($this->_getResourceColumn(self::RESOURCE_PARENT));
				$resource = $this->_getResourceTable()->fetchRow(
					$this->_getResourceTable()->select()->where(
						$this->_getResourceColumn(self::RESOURCE_ID).' = ?',
						$resource->__get($this->_getResourceColumn(self::RESOURCE_PARENT))
					)
				);

				if(
					!is_null($resource)
				){
					//Ergänzung des Namens bei der Child-Role
					$resources[count($resources)-1]
						[$this->_getResourceColumn(self::RESOURCE_PARENT)]
						= $resource->__get($this->_getResourceColumn(self::RESOURCE_NAME));

					//Nur wenn die Role/Resource noch nicht geladen wurde, wird sie hinzugefügt,
					//ansonsten springen wir raus, da die Role/Resource und ihre
					//Eltern bereits geladen wurden.
					if(
						!is_null($role)
						&& !$this->_getAdapted()->hasLoaded(
							$role,
							$resource->__get($this->_getResourceColumn(self::RESOURCE_NAME))
						)
					){
						$resources[] = $resource->toArray();
					}
					elseif(
						is_null($role)
					){
						$resources[] = $resource->toArray();
					}
					else{
						break;
					}

					//Wenn die Role keine Parent-Id besitzt, springen wir raus.
					if(
						is_null($resource->__get($this->_getResourceColumn(self::RESOURCE_PARENT)))
					){
						break;
					}
				}
				else{
					/**
					 * @see Dkplus_Acl_Adapter_Exception
					*/
					//require-once 'Dkplus/Acl/Adapter/Exception.php';
					throw new Dkplus_Acl_Adapter_Exception('Parent Resource with id '.$intId.' not found.');
				}
			}
			while(true);
		}		
				
		if(
			!is_null($role)
			&& (
				!$this->_getAdapted()->hasLoaded(null, null)
				|| !$this->_getAdapted()->hasLoaded($resourceName, null)
			)
		){
			$resources[] = null;
		}

		//Die Resourcen müssen in umgekehrter Reihenfolge zurückgegeben werden.
		return array_reverse($resources);
	}

	/**
	 * <p>Liefert einen Array mit den Namen der Parent-Rows zurück.</p>
	 * <p>Zugleich werden dem Array $roles die geladenen Rollen hinzugefügt.</p>
	 * @param string $id
	 * @param array $roles
	 * @return array
	 */
	protected function _fetchRoleWithParentsRecursiv($id, &$roles){
		$id = (int) $id;

		$select = $this->_getRoleTable()->select()
			->from(
				array('parent' => $this->_getRoleParentTable()->info('name')),
				''
			)
			->joinLeft(
				array('role' => $this->_getRoleTable()->info('name')),
					'parent.' . $this->_getRoleParentColumn(self::ROLE_PARENT_PARENT)
						. ' = '
						. 'role.' . $this->_getRoleColumn(self::ROLE_ID),
				'role.*')
			->where('parent.' . $this->_getRoleParentColumn(self::ROLE_PARENT_ID)
				. ' = ?', $id);
		$rowsetRoles = $this->_getRoleTable()->fetchAll($select);

		if(
			count($rowsetRoles) == 0
		){
			return array();
		}


		$names = array();
		foreach($rowsetRoles AS $rowRole){
			$i = count($roles);
			$rowRole = $rowRole->toArray();
			$roles[] = $rowRole;
			
			$roles[$i]['parent'] = $this->_fetchRoleWithParentsRecursiv(
				$rowRole[$this->_getRoleColumn(self::ROLE_ID)],
				$roles
			);
			$names[] = $rowRole[$this->_getRoleColumn(self::ROLE_NAME)];
		}
		return $names;

	}

	/**
	 * <p>Lädt die Rollen und all ihre Parent-Rollen und gibt sie als Array zurück.</p>
	 * <p>Der zurückgegebene Array sieht wie folgt aus:
	 * <code>
	 * $array = array(
	 *	null,
	 *	array(
	 *		'<ROLE_ID>'		=> '5'
	 *		'<ROLE_NAME>'	=> 'role1',
	 *		'parent'		=> array(),
	 *	),
	 *	array(
	 *		'<ROLE_ID>'		=> '7'
	 *		'<ROLE_NAME>'	=> 'role2',
	 *		'parent'		=> array(),
	 *	),
	 *	array(
	 *		'<ROLE_ID>'		=> '8'
	 *		'<ROLE_NAME>'	=> 'role3',
	 *		'parent'		=> array('role2'),
	 *	)
	 *	array(
	 *		'<ROLE_ID>'		=> '12'
	 *		'<ROLE_NAME>'	=> 'role6',
	 *		'parent'		=> array('role3', 'role1'),
	 *	)
	 * );
	 * </code>
	 * </p>
	 * @param string $role
	 * @param string|null $resource
	 * @return array
	 */
	protected function _loadRoles($role, $resource = null){
		$role = (string) $role;
		$roleName = $role;
		$resource = is_null($resource)
			? null
			: (string) $resource;
		
		$role = $this->_getRoleTable()->fetchRow(
			$this->_getRoleTable()->select()->where($this->_getRoleColumn(self::ROLE_NAME).' = ?', $role)
		);
		
		if(
			is_null($role)
		){
			return array();
		}
		
		$roles = array($role->toArray());

		//Hier müssen wir nun unterscheiden, ob und wie die Vererbung stattfindet.
		if(
			$this->_isUsingRoleTableInheritance()
		){
			if(
				!is_null($role->__get($this->_getRoleColumn(self::ROLE_PARENT)))
			){
				do{
					$intId = $role->__get($this->_getRoleColumn(self::ROLE_PARENT));
					$role = $this->_getRoleTable()->fetchRow(
						$this->_getRoleTable()->select()
							->where($this->_getRoleColumn(self::ROLE_ID).' = ?',
								$intId)
					);

					if(
						!is_null($role)
					){
						//Ergänzung des Namens bei der Child-Role
						$roles[count($roles)-1]['parent'] =
							array($role->__get($this->_getRoleColumn(self::ROLE_NAME)));

						//Nur wenn die Role noch nicht geladen wurde, wird sie hinzugefügt, ansonsten springen wir raus.
						if(
							!is_null($resource)
							&& !$this->_getAdapted()->hasLoaded(
								$role->__get($this->_getRoleColumn(self::ROLE_NAME)),
								$resource
							)
						){
							$roles[] = $role->toArray();
						}
						elseif(
							is_null($resource)
						){
							$roles[] = $role->toArray();
						}
						else{
							break;
						}

						//Wenn die Role keine Parent-Id besitzt, springen wir raus.
						if(
							is_null($role->__get($this->_getRoleColumn(self::ROLE_PARENT)))
						){
							break;
						}
					}
					else{
						/**
						 * @see Dkplus_Acl_Adapter_Exception
						 */
						//require-once 'Dkplus/Acl/Adapter/Exception.php';
						throw new Dkplus_Acl_Adapter_Exception('Parent Role with id '
							.$intId.' not found.');
					}
				}
				while(true);
			}
			if(
				!isset($roles[count($roles)-1]['parent'])
			){
				$roles[count($roles)-1]['parent'] = array();
			}
		}
		//Vererbung über eine extra Tabelle:
		elseif(
			$this->_isUsingRoleParentTableInheritance()
		){
			$tmpParent = $this->_fetchRoleWithParentsRecursiv(
				$roles[0][$this->_getRoleColumn(self::ROLE_ID)],
				&$roles
			);
			$roles[0]['parent'] = $tmpParent;
		}		
		
		if(
			!is_null($resource)
			&& (
				!$this->_getAdapted()->hasLoaded(null, null)
				|| !$this->_getAdapted()->hasLoaded(null, $roleName)
			)
		){
			$roles[] = null;
		}

		return array_reverse($roles);
	}
	
	
	/**
	 * <p>Gibt die geforderten Regeln zurück.</p>
	 * <p>Der zurückgegebene Array hat folgendes Format:
	 * <code>
	 * $array = array(
	 *	array(
	 *		'type'		=> 0,
	 *		'resource'	=> 'resource1',
	 *		'role'		=> 'role1',
	 *		'privilege'	=> 'priv1',	//optional, wenn eine priv-tabelle angegeben wurde
	 *	),
	 *	array(
	 *		'type'		=> 1,
	 *		'resource'	=> 'resource1',
	 *		'role'		=> 'role2',
	 *		'privilege'	=> null,	//optional, wenn eine priv-tabelle angegeben wurde
	 *	),
	 *	array(
	 *		'type'		=> 1,
	 *		'resource'	=> 'resource2',
	 *		'role'		=> 'role1',
	 *		'privilege'	=> 'priv3',	//optional, wenn eine priv-tabelle angegeben wurde
	 *	)
	 * );
	 * </code>
	 * </p>
	 * @param array $arrRoles
	 * @param array $arrResources
	 * @return array
	 */
	protected function _loadRules($arrRoles, $arrResources, $blnSearchRoleNull, $blnSearchResNull){
		if(
			count($arrRoles) == 0
			&& count($arrResources) == 0
			&& !$blnSearchResNull
			&& !$blnSearchRoleNull
		){
			return array();
		}
		$sel = $this->_getRuleTable()->select()->setIntegrityCheck(false);
		if(
			$this->_hasRuleColumn(self::RULE_ASSERT)
		){
			$sel->from(
				array('rule' => $this->_getRuleTable()->info('name')),
				array(
					'type'		=> $this->_getRuleColumn(self::RULE_TYPE),
					'assert'	=> $this->_getRuleColumn(self::RULE_ASSERT)
				)
			);
		}
		else{
			$sel->from(
				array('rule' => $this->_getRuleTable()->info('name')),
				array('type' => $this->_getRuleColumn(self::RULE_TYPE))
			);
		}
		$sel->joinLeft(
				array('role' => $this->_getRoleTable()->info('name')), 
				'rule.'.$this->_getRuleColumn(self::RULE_ROLE)
					.' = role.'.$this->_getRoleColumn(self::ROLE_ID),
				array('role' => $this->_getRoleColumn(self::ROLE_NAME))
			)
			->joinLeft(
				array('res' => $this->_getResourceTable()->info('name')), 
				'rule.'.$this->_getRuleColumn(self::RULE_RESOURCE)
					.' = res.'.$this->_getResourceColumn(self::RESOURCE_ID),
				array('resource' => $this->_getResourceColumn(self::RESOURCE_NAME))
			);

		if(
			!is_null($this->_getPrivilegeTable())
		){
			$sel->joinLeft(
				array('priv' => $this->_getPrivilegeTable()->info('name')), 
				'rule.'.$this->_getRuleColumn(self::RULE_PRIVILEGE)
				.' = priv.'.$this->_getPrivilegeColumn(self::PRIVILEGE_ID),
					array('privilege' => $this->_getPrivilegeColumn(self::PRIVILEGE_NAME))
			);
			$sel->order('(rule.'.$this->_getRuleColumn(self::RULE_PRIVILEGE).' IS NULL)');
		}
		
		if(
			count($arrRoles) > 0
		){
			$sel->where(
				'rule.'.$this->_getRuleColumn(self::RULE_ROLE).' IN('.implode(',', $arrRoles).')'
				.(
					$blnSearchRoleNull
					? ' OR rule.'.$this->_getRuleColumn(self::RULE_ROLE).' IS NULL'
					: ''
				)
			);
		}
		elseif(
			$blnSearchRoleNull
		){
			$sel->where('rule.'.$this->_getRuleColumn(self::RULE_ROLE).' IS NULL');
		}
		
		
		if(
			count($arrResources) > 0
		){
			$sel->where(
				'rule.'.$this->_getRuleColumn(self::RULE_RESOURCE).' IN('.implode(',', $arrResources).')'
				.(
					$blnSearchResNull
					? ' OR rule.'.$this->_getRuleColumn(self::RULE_RESOURCE).' IS NULL'
					: ''
				)
			);
		}
		elseif(
			$blnSearchResNull
		){
			$sel->where('rule.'.$this->_getRuleColumn(self::RULE_RESOURCE).' IS NULL');
		}
		return $this->_getRuleTable()->fetchAll($sel)->toArray();
	}
	
	public function allow($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null){
		$this->_setRuleOverride($roles, $resources, $privileges, $assert, true);
	}
		
	public function deny($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null){
		$this->_setRuleOverride($roles, $resources, $privileges, $assert, false);
	}
	
	/**
	 * 
	 * @param $roles
	 * @param $resources
	 * @param $privileges
	 * @param $type
	 * @return Dkplus_Acl_Adapter_Db
	 */
	public function _setRuleOverride($roles = null, $resources = null, 
		$privileges = null, Zend_Acl_Assert_Interface $assert = null, $type = true){
		$type = (boolean) $type;
		
		if(
			!is_array($roles)
		){
			$roles = array($roles);
		}
		elseif(
			count($roles) == 0
		){
			$roles = array(null);
		}
		if(
			!is_array($resources)
		){
			$resources = array($resources);
		}
		elseif(
			count($resources) == 0
		){
			$resources = array(null);
		}
		
		if(
			!is_array($privileges)
		){
			$privileges = array($privileges);
		}
		elseif(
			count($privileges) == 0
		){
			$privileges = array(null);
		}
		
		//Umwandeln der Role-Ids
		for($i = 0; $i < count($roles); ++$i){
			$roles[$i] = (
				is_null($roles[$i])
				? null
				: (
					$roles[$i] instanceof Zend_Acl_Role_Interface
					? $roles[$i]->getRoleId()
					: (string) $roles[$i]
				)
			);
		}
		
		//Umwandeln der Resource-Ids
		for($i = 0; $i < count($resources); ++$i){
			$resources[$i] = (
				is_null($resources[$i])
				? null
				: (
					$resources[$i] instanceof Zend_Acl_Resource_Interface
					? $resources[$i]->getResourceId()
					: (string) $resources[$i]
				)
			);
		}
		
		//Umwandeln der Privilegien
		for($i = 0; $i < count($privileges); ++$i){
			$privileges[$i] = (
				is_null($privileges[$i])
				? null
				: (string) $privileges[$i]
			);
		}
		
		//Hier werden nun alle Rollen drin gespeichert!
		$arrRoles = array();

		//Müssen alle Rollen geladen werden?
		if(
			in_array(null, $roles)
		){			
			$arrRoles = $this->_fetchRoles();
			$arrRoles[] = null;
		}
		else{			
			foreach($roles AS $role){
				$arrRoles = array_merge($arrRoles, $this->_fetchParentRoles($role));
			}
		}
		
		//Hier werden nun die Resourcen drin gespeichert!
		$arrResources = array();

		//Müssen alle Resourcen geladen werden?
		if(
			in_array(null, $resources)
		){
			$arrResources = $this->_fetchResources();
			$arrResources[] = null;
		}
		else{			
			foreach($resources AS $resource){
				$arrResources = array_merge($arrResources, $this->_fetchParentResources($resource));
			}
		}
		
		if(
			count($arrResources) == 0
			|| count($arrRoles) == 0
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('There are not enough data found.');
		}
		
		//Umwandeln in Resourcen- und Role-Ids
		$arrResourceIds = array();
		foreach($arrResources AS $resource){
			if(
				!is_null($resource)
			){
				$arrResourceIds[] = $resource->__get(
					$this->_getResourceColumn(self::RESOURCE_ID)
				);
			}
			else{
				$arrResourceIds[] = null;
			}
		}
		$arrRoleIds = array();
		foreach($arrRoles AS $role){
			if(
				!is_null($role)
			){
				$arrRoleIds[] = $role->__get(
					$this->_getRoleColumn(self::ROLE_ID)
				);
			}
			else{
				$arrRoleIds[] = null;
			}
		}

		$arrResourceIds = array_unique($arrResourceIds);
		$arrRoleIds = array_unique($arrRoleIds);
		
		//Holen der Privilegien		
		$privileges = array_unique($privileges);
		$arrPrivileges = array();
		$arrPrivilegeIds = array();
		if(
			$this->hasPrivilegeTable()
		){
			if(
				in_array(null, $privileges)
			){
				unset($privileges[array_search(null, $privileges)]);	
				$arrPrivilegeIds[] = null;
				$arrPrivileges[] = null;
			}
			
			if(
				count($privileges) > 0
			){
				$select = $this->_getPrivilegeTable()->select()->where(
					$this->_getPrivilegeColumn(self::PRIVILEGE_NAME).' IN (?)', 
					$privileges
				);
				
				$rowsetPrivileges = $this->_getPrivilegeTable()->fetchAll($select);
				
				//Schauen, welche Privilegien schon in der Datenbank sind
				foreach($rowsetPrivileges AS $rowPrivilege){
					$arrPrivileges[] = $rowPrivilege;
					$arrPrivilegeIds[] = $rowPrivilege->__get(
						$this->_getPrivilegeColumn(self::PRIVILEGE_ID)
					);

					if(
						in_array(
							$rowPrivilege->__get(
								$this->_getPrivilegeColumn(self::PRIVILEGE_NAME)
							),
							$privileges
						)
					){
						unset($privileges[
							array_search(
								$rowPrivilege->__get(
									$this->_getPrivilegeColumn(self::PRIVILEGE_NAME)
								),
								$privileges)
							]);
					}
				}
				
				//Nicht in der Datenbank befindliche Privilegien hinzufügen
				if(
					count($privileges) > 0
				){
					foreach($privileges AS $priv){
						$this->_getPrivilegeTable()->insert(
							array(
								$this->_getPrivilegeColumn(self::PRIVILEGE_NAME)
									=> $priv
							)
						);
					}
					$select = $this->_getPrivilegeTable()->select()
					->where($this->_getPrivilegeColumn(self::PRIVILEGE_NAME)
							.' IN (?)', $privileges);
					if(
						count($arrPrivilegeIds) > 0
					){
						$select->where($this->_getPrivilegeColumn(self::PRIVILEGE_ID)
							.' NOT IN (?)', $arrPrivilegeIds);
					}
					$rowsetPrivileges = $this->_getPrivilegeTable()->fetchAll($select);
					foreach($rowsetPrivileges AS $rowPrivilege){
						$arrPrivileges[] = $rowPrivilege;
						$arrPrivilegeIds[] = $rowPrivilege->__get(
							$this->_getPrivilegeColumn(self::PRIVILEGE_ID)
						);

						if(
							in_array(
								$rowPrivilege->__get(
									$this->_getPrivilegeColumn(self::PRIVILEGE_NAME)
								),
								$privileges
							)
						){
							unset($privileges[
								array_search(
									$rowPrivilege->__get(
										$this->_getPrivilegeColumn(self::PRIVILEGE_NAME)
									),
									$privileges)
								]);
						}
					}
				}
			}
		}
		else{
			$arrPrivileges = null;
			$arrPrivilegeIds = null;
		}

		//Löschen der alten Rollen
		$this->_deleteRules($arrResourceIds, $arrRoleIds, $arrPrivilegeIds);

		//
		//Alte Rollen sind nun gelöscht!!!
		//

		//Jetzt müssen noch die Rollen für die Regeln gesucht werden
		$arrRoleIds = array();		
		if(
			in_array(null, $roles)
		){
			unset($roles[array_search(null, $roles)]);
			$arrRoleIds[] = null;
		}

		if(
			count($roles) > 0
		){
			$rowsetRoles = $this->_getRoleTable()->fetchAll(
				$this->_getRoleTable()->select()
					->where($this->_getRoleColumn(self::ROLE_NAME).' IN (?)',
						$roles)
			);
			foreach($rowsetRoles AS $rowRole){
				$arrRoleIds[] = $rowRole->__get(
					$this->_getRoleColumn(self::ROLE_ID)
				);
			}
		}

		//Die Resourcen für die Regeln brauchen wir auch noch
		$arrResourceIds = array();		
		if(
			in_array(null, $resources)
		){
			unset($resources[array_search(null, $resources)]);
			$arrResourceIds[] = null;
		}
		if(
			count($resources) > 0
		){
			$rowsetResources = $this->_getResourceTable()->fetchAll(
				$this->_getResourceTable()->select()
				->where($this->_getResourceColumn(self::RESOURCE_NAME).' IN (?)',
						$resources)
			);
			foreach($rowsetResources AS $rowResource){
				$arrResourceIds[] = $rowResource->__get(
					$this->_getResourceColumn(self::RESOURCE_ID)
				);
			}
		}
		foreach($arrRoleIds AS $roleId){
			foreach($arrResourceIds AS $resourceId){
				//Einzugebende Werte
				$arrInsert = array(
					$this->_getRuleColumn(self::RULE_ROLE)		=> $roleId,
					$this->_getRuleColumn(self::RULE_RESOURCE)	=> $resourceId,
					$this->_getRuleColumn(self::RULE_TYPE)		=> $type
				);

				if(
					!is_null($assert)
					&& $this->_hasRuleColumn(self::RULE_ASSERT)
				){
					$arrInsert[$this->_getRuleColumn(self::RULE_ASSERT)]
						= get_class($assert);
				}
				elseif(
					!is_null($assert)
				){
					/**
					 * @see Dkplus_Acl_Exception
					 */
					//require_once 'Dkplus/Acl/Adapter/Exception.php';
					throw new Dkplus_Acl_Adapter_Exception('Assert-Column is not set.');
				}
				
				if(
					is_null($arrPrivilegeIds)
				){					
					$this->_getRuleTable()->insert($arrInsert);
				}
				else{
					foreach($arrPrivilegeIds AS $privilegeId){						
						$this->_getRuleTable()->insert(
							array_merge($arrInsert, array(
								$this->_getRuleColumn(self::RULE_PRIVILEGE)
									=> $privilegeId
							))
						);
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @param array $resources Array with ids of the resources.
	 * @param array $roles Array with ids of the roles
	 * @param $privileges NULL or an array with ids of the privileges. 
	 * @return Dkplus_Acl_Adapter_Db
	 */
	protected function _deleteRules(array $resources, array $roles, $privileges = null){
		$where = array();

		if(
			in_array(null, $resources)
		){
			unset($resources[array_search(null, $resources)]);

			$where[] = $this->_getRuleTable()->getAdapter()
				->quoteInto(
					$this->_getRuleColumn(self::RULE_RESOURCE).' IS NULL '
					.'OR '
					.$this->_getRuleColumn(self::RULE_RESOURCE).' IN (?)',
					$resources,
					Zend_Db::INT_TYPE);
		}
		else{
			$where[] = $this->_getRuleTable()->getAdapter()
			->quoteInto($this->_getRuleColumn(self::RULE_RESOURCE).' IN (?)',
				$resources,
				Zend_Db::INT_TYPE);
		}

		if(
			in_array(null, $roles)
		){
			unset($roles[array_search(null, $roles)]);
			$where[] = $this->_getRuleTable()->getAdapter()
			->quoteInto($this->_getRuleColumn(self::RULE_ROLE).' IS NULL '
					. 'OR '
					. $this->_getRuleColumn(self::RULE_ROLE).' IN (?)',
					$roles,
					Zend_Db::INT_TYPE);
		}
		else{
			$where[] = $this->_getRuleTable()->getAdapter()
				->quoteInto($this->_getRuleColumn(self::RULE_ROLE).' IN (?)',
					$roles,
					Zend_Db::INT_TYPE);
		}	

		//Privilegien-Tabelle vorhanden?
		if(
			$this->hasPrivilegeTable()
			&& !is_null($privileges)
		){
			if(
				!is_array($privileges)
			){
				throw new Exception('Third argument is invalid, must be an array or null.');
			}

			/**
			 * @todo
			 * Änderung: Ist eine null dabei, werden nun alle privilegien entfernt (reset).
			 */
			if(
				in_array(null, $privileges)
			){
				
			}
			/*//Möglichkeiten 1, es existieren mehrere Privilegien und eine davon ist NULL:
			if(
				in_array(null, $privileges)
				&& count($privileges) > 1
			){
				unset($privileges[array_search(null, $privileges)]);
				$where[] = $this->_getRuleTable()->getAdapter()
				->quoteInto($this->_getRuleColumn(self::RULE_PRIVILEGE).' IS NULL '
						. 'OR '
						. $this->_getRuleColumn(self::RULE_PRIVILEGE).' IN (?)',
						$privileges, 
						Zend_Db::INT_TYPE);
			}
			//Möglichkeit 2, es existiert nur die Privilegie NULL:
			elseif(
				in_array(null, $privileges)
			){
				$where[] = $this->_getRuleColumn(self::RULE_PRIVILEGE).' IS NULL';
			}*/
			//Möglichkeit 3, es existieren mehrere Privilegien und keine ist NULL
			elseif(
				count($privileges) > 0
			){
				$where[] = $this->_getRuleTable()->getAdapter()
					->quoteInto(
						$this->_getRuleColumn(self::RULE_PRIVILEGE).' IN (?)',
						$privileges,
						Zend_Db::INT_TYPE);
			}
			else{
				/**
				 * @see Dkplus_Acl_Adapter_Exception
				 */
				//require-once 'Dkplus/Acl/Adapter/Exception.php';
				throw new Dkplus_Acl_Adapter_Exception('There must be more than 0 privileges given.');
			}
		}
		$this->_getRuleTable()->delete($where);
		return $this;
	}
	
	/**
	 * @param string $role
	 * @return array
	 */
	protected function _fetchParentRoles($role){

		$role = (string) $role;
		$rowRole = $this->_getRoleTable()->fetchRow(
			$this->_getRoleTable()->select()
				->where($this->_getRoleColumn(self::ROLE_NAME).' = ?', $role)
		);
		
		if(
			is_null($rowRole)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Role "'.$role
				.'" could not be found in the Database.');
		}

		$arrRoles = array($rowRole);

		//Vererbung wird genutzt
		if(
			$this->_isUsingRoleParentTableInheritance()
			|| $this->_isUsingRoleTableInheritance()
		){
			$arrRoleIds = array(
				$rowRole->__get($this->_getRoleColumn(self::ROLE_ID))
			);

			$rowsetRoles = array();	//Initialisierung für NetBeans
			do{
				$select = $this->_getRoleTable()->select();

				//Vererbung über ein Feld in der Tabelle
				if(
					$this->_isUsingRoleTableInheritance()
				){
					$select->where(
						$this->_getRoleColumn(self::ROLE_PARENT).' IN (?)',
						$arrRoleIds);
				}
				//Vererben über eine eigene Tabelle
				else{
					$select
						->from(
							array('parent' => $this->_getRoleParentTable()->info('name')),
							''
						)
						->joinLeft(
							array('role' => $this->_getRoleTable()->info('name')),
								'parent.' . $this->_getRoleParentColumn(self::ROLE_PARENT_ID)
									. ' = '
									. 'role.' . $this->_getRoleColumn(self::ROLE_ID),
							'role.*')
						->where('parent.' . $this->_getRoleParentColumn(self::ROLE_PARENT_PARENT)
							. ' IN (?)', $arrRoleIds);
				}

				$rowsetRoles = $this->_getRoleTable()->fetchAll($select);

				$arrRoleIds = array();
				foreach($rowsetRoles AS $rowRole){
					$arrRoles[] = $rowRole;
					$arrRoleIds[] = $rowRole->__get(
						$this->_getRoleColumn(self::ROLE_ID)
					);
				}
			}
			while(count($rowsetRoles) > 0);
		}
		return $arrRoles;
	}
	
	/**
	 * <p>Gibt alle Rollen zurück.</p>
	 * @return array
	 */
	protected function _fetchRoles(){
		$arrReturn = array();
		$rowSet = $this->_getRoleTable()->fetchAll();
		foreach($rowSet AS $row){
			$arrReturn[] = $row;
		}
		return $arrReturn;
	}
	
	/**
	 * <p>Gibt alle Resourcen zurück, die Eltern-Resourcen der übergebenen Resource sind.</p>
	 * @param string $resource
	 * @return array
	 */
	protected function _fetchParentResources($resource){
		$resource = (string) $resource;

		$rowResource = $this->_getResourceTable()->fetchRow(
			$this->_getResourceTable()->select()->where(
				$this->_getResourceColumn(self::RESOURCE_NAME).' = ?', $resource
			)
		);
		
		if(
			is_null($rowResource)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Resource "'.$resource.'" could not be found in the Database.');
		}

		$rowsetResources = null;	//Initialisieren für NetBeans
		$arrResources = array($rowResource);
		$arrResourceIds = array(
			$rowResource->__get($this->_getResourceColumn(self::RESOURCE_ID)
		));
		do{
			$rowsetResources = $this->_getResourceTable()->fetchAll(
				$this->_getResourceTable()->select()->where(
					$this->_getResourceColumn(self::RESOURCE_PARENT)
					.' IN ('.implode(',', $arrResourceIds).')')
			);
			$arrResourceIds = array();
			foreach($rowsetResources AS $rowResource){
				$arrResources[] = $rowResource;
				$arrResourceIds[] = $rowResource->__get(
					$this->_getResourceColumn(self::RESOURCE_ID));
			}
		}
		while(count($rowsetResources) > 0);
		return $arrResources;
	}
	
	/**
	 * <p>Gibt alle vorhandenen Resourcen zurück.</p>
	 * @return array
	 */
	protected function _fetchResources(){
		$arrReturn = array();
		$rowSet = $this->_getResourceTable()->fetchAll();
		foreach($rowSet AS $row){
			$arrReturn[] = $row;
		}
		return $arrReturn;
	}
	
	/**
	 * <p>Entfernt eine Rolle.</p>
	 * @param string $role
	 * @return Dkplus_Acl_Adapter_Interface Provides a fluent interface.
	 */
	public function removeRole($role){
		$role = (string) $role;

		$rowRole = $this->_getRoleTable()->fetchRow(
			$this->_getRoleTable()->select()->where(
				$this->_getRoleColumn(self::ROLE_NAME).' = ?', $role)
		);
		
		if(
			is_null($rowRole)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Role "'.$role
				.'" could not be found in the Database.');
		}
		$arrRoles
			= array($rowRole);
		$arrRoleIds
			= array($rowRole->__get($this->_getRoleColumn(self::ROLE_ID)));
		$arrRoleNames
			= array($rowRole->__get($this->_getRoleColumn(self::ROLE_NAME)));
		$arrRoleIdsTmp
			= array($rowRole->__get($this->_getRoleColumn(self::ROLE_ID)));

		//Vererbung?
		if(
			$this->_isUsingRoleTableInheritance()
			|| $this->_isUsingRoleParentTableInheritance()
		){
			$rowsetRoles = array();	//Initialisierung für Netbeans
			do{
				$select = $this->_getRoleTable()->select();
				
				//Vererbung über ein Feld in der Tabelle
				if(
					$this->_isUsingRoleTableInheritance()
				){
					$select->where(
						$this->_getRoleColumn(self::ROLE_PARENT)
							.' IN (?)', $arrRoleIdsTmp);
				}
				//Vererben über eine eigene Tabelle
				else{
					$select
						->from(
							array('parent' => $this->_getRoleParentTable()->info('name')),
							''
						)
						->joinLeft(
							array('role' => $this->_getRoleTable()->info('name')),
								'parent.' . $this->_getRoleParentColumn(self::ROLE_PARENT_ID)
									. ' = '
									. 'role.' . $this->_getRoleColumn(self::ROLE_ID),
							'role.*')
						->where('parent.' . $this->_getRoleParentColumn(self::ROLE_PARENT_PARENT)
							. ' IN (?)', $arrRoleIdsTmp);
				}

				//Suchen wir die Child-Rollen
				$rowsetRoles = $this->_getRoleTable()->fetchAll($select);

				$arrRoleIdsTmp = array();
				foreach($rowsetRoles AS $rowRole){
					$arrRoles[] = $rowRole;
					$arrRoleIds[] = $rowRole->__get(
						$this->_getRoleColumn(self::ROLE_ID));
					$arrRoleNames[] = $rowRole->__get(
						$this->_getRoleColumn(self::ROLE_NAME));
					$arrRoleIdsTmp[] = $rowRole->__get(
						$this->_getRoleColumn(self::ROLE_ID));
				}
			}
			while(count($rowsetRoles) > 0);
		}

		//Löschen der Reihen
		foreach($arrRoles AS $rowRole){
			$rowRole->delete();
		}

		//Löschen der dazugehörigen Regeln
		$this->_getRuleTable()->delete(
			$this->_getRuleTable()->getAdapter()->quoteInto(
				$this->_getRuleColumn(self::RULE_ROLE).' IN (?)',
				$arrRoleIds,
				Zend_Db::INT_TYPE)
		);

		return $this;
	}
	
	/**
	 * <p>Entfernt eine Resource.</p>
	 * @param string $resource
	 * @return Dkplus_Acl_Adapter_Interface Provides a fluent interface.
	 */
	public function removeResource($resource){
		$resource = (string) $resource;

		$rowRes = $this->_getResourceTable()->fetchRow(
			$this->_getResourceTable()->select()->where(
				$this->_getResourceColumn(self::RESOURCE_NAME).' = ?', $resource)
		);
		
		if(
			is_null($rowRes)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception('Resource "'.$resource.'" could not be found in the Database.');
		}
		$arrResources = array($rowRes);
		$arrResourceIds = array($rowRes->__get($this->_getResourceColumn(self::RESOURCE_ID)));
		$arrResourceNames = array($rowRes->__get($this->_getResourceColumn(self::RESOURCE_NAME)));
		$arrResourceIdsTmp = array($rowRes->__get($this->_getResourceColumn(self::RESOURCE_ID)));
		$rowsetResources = null; //Initialisierung für Netbeans
		do{
			$rowsetResources = $this->_getResourceTable()->fetchAll(
				$this->_getResourceTable()->select()->where($this->_getResourceColumn(self::RESOURCE_PARENT)
					.' IN ('.implode(',', $arrResourceIdsTmp).')')
			);
			$arrResourceIdsTmp = array();
			foreach($rowsetResources AS $rowRes){
				$arrResources[] = $rowRes;
				$arrResourceIds[] = $rowRes->__get(
					$this->_getResourceColumn(self::RESOURCE_ID));
				$arrResourceNames[] = $rowRes->__get(
					$this->_getResourceColumn(self::RESOURCE_NAME));
				$arrResourceIdsTmp[] = $rowRes->__get(
					$this->_getResourceColumn(self::RESOURCE_ID));
			}
		}
		while(count($rowsetResources) > 0);
		foreach($arrResources AS $rowRes){
			$rowRes->delete();
		}
		
		$this->_getRuleTable()->delete(
			$this->_getRuleTable()->getAdapter()->quoteInto($this->_getRuleColumn(self::RULE_RESOURCE).' IN (?)', $arrResourceIds, Zend_Db::INT_TYPE)
		);
		return array_reverse($arrResourceNames);
	}

	/**
	 * <p>Fügt einer Rolle eine neue Eltern-Rolle hinzu.</p>
	 * @param string $role
	 * @param string $parentRole
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function addParentRole($role, $parentRole){
		$rowRole = $this->_getRoleTable()->fetchRow(
			$this->_getRoleTable()->select()->where(
				$this->_getRoleColumn(self::ROLE_NAME).' = ?', $role
			)
		);

		if(
			is_null($rowRole)
		){
			/**
			* @see Dkplus_Acl_Adapter_Exception
			*/
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception(
				'There is no row id '.$role.'.');
		}

		$rowParentRole = $this->_getRoleTable()->fetchRow(
			$this->_getRoleTable()->select()->where(
				$this->_getRoleColumn(self::ROLE_NAME).' = ?', $parentRole
			)
		);

		if(
			is_null($rowParentRole)
		){
			/**
			* @see Dkplus_Acl_Adapter_Exception
			*/
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception(
				'There is no row id '.$parentRole.'.');
		}

		//Die eigentliche Aktion geht los
		if(
			$this->_isUsingRoleParentTableInheritance()
		){
			$rowInheritance = $this->_getRoleParentTable()->fetchRow(
				$this->_getRoleParentTable()->select()->where(
					$this->_getRoleParentColumn(self::ROLE_PARENT_ID).' = ?',
					$rowRole->__get($this->_getRoleColumn(self::ROLE_ID))
				)
				->where(
					$this->_getRoleParentColumn(self::ROLE_PARENT_PARENT).' = ?',
					$rowParentRole->__get($this->_getRoleColumn(self::ROLE_ID))
				)
			);

			if(
				!is_null($rowInheritance)
			){
				/**
				* @see Dkplus_Acl_Adapter_Exception
				*/
				//require-once 'Dkplus/Acl/Adapter/Exception.php';
				throw new Dkplus_Acl_Adapter_Exception(
					'Role '.$role.' is already a child of role '.$parentRole.'.');
			}

			$this->_getRoleParentTable()->insert(
				array(
					$this->_getRoleParentColumn(self::ROLE_PARENT_ID)
						=> $rowRole->__get($this->_getRoleColumn(self::ROLE_ID)),
					$this->_getRoleParentColumn(self::ROLE_PARENT_PARENT)
						=> $rowParentRole->__get($this->_getRoleColumn(self::ROLE_ID))
				)
			);
		}
		elseif(
			$this->_isUsingRoleTableInheritance()
		){			
			$rowRole->__set($this->_getRoleColumn(self::ROLE_PARENT),
				$rowParentRole->__get($this->_getRoleColumn(self::ROLE_ID)));
			$rowRole->save();
		}
		else{
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception(
				'There is no possibility for inheritance defined.');
		}
		return $this;
	}

	/**
	 * <p>Entfernt eine Eltern-Rolle von einer Rolle.</p>
	 * @param string $role
	 * @param string $parentRole
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	public function removeParentRole($role, $parentRole){
		//Holen der Rollen
		$rowRole = $this->_getRoleTable()->fetchRow(
			$this->_getRoleTable()->select()->where(
				$this->_getRoleColumn(self::ROLE_NAME).' = ?', $role
			)
		);

		if(
			is_null($rowRole)
		){
			/**
			* @see Dkplus_Acl_Adapter_Exception
			*/
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception(
				'There is no row id '.$role.'.');
		}

		$rowParentRole = $this->_getRoleTable()->fetchRow(
			$this->_getRoleTable()->select()->where(
				$this->_getRoleColumn(self::ROLE_NAME).' = ?', $parentRole
			)
		);

		if(
			is_null($rowParentRole)
		){
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			*/
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception(
				'There is no row id '.$parentRole.'.');
		}


		//Hier geht die eigentliche Aktion los
		if(
			$this->_isUsingRoleParentTableInheritance()
		){
			$rowInheritance = $this->_getRoleParentTable()->fetchRow(
				$this->_getRoleParentTable()->select()->where(
					$this->_getRoleParentColumn(self::ROLE_PARENT_ID).' = ?',
					$rowRole->__get($this->_getRoleColumn(self::ROLE_ID))
				)
				->where(
					$this->_getRoleParentColumn(self::ROLE_PARENT_PARENT).' = ?',
					$rowParentRole->__get($this->_getRoleColumn(self::ROLE_ID))
				)
			);

			if(
				is_null($rowInheritance)
			){
				/**
				* @see Dkplus_Acl_Adapter_Exception
				*/
				//require-once 'Dkplus/Acl/Adapter/Exception.php';
				throw new Dkplus_Acl_Adapter_Exception(
					'Role '.$role.' is not a child of role '.$parentRole.'.');
			}

			$rowInheritance->delete();
		}
		elseif(
			$this->_isUsingRoleTableInheritance()
		){
			if(
				is_null($rowRole->__get($this->_getRoleColumn(self::ROLE_PARENT)))
			){
				/**
				* @see Dkplus_Acl_Adapter_Exception
				*/
				//require-once 'Dkplus/Acl/Adapter/Exception.php';
				throw new Dkplus_Acl_Adapter_Exception(
					'There is no parent role for role '.$role.'.');
			}
			
			if(
				$rowRole->__get($this->_getRoleColumn(self::ROLE_PARENT))
					!= $rowParentRole->__get($this->_getRoleColumn(self::ROLE_ID))
			){
				/**
				 * @see Dkplus_Acl_Adapter_Exception
				*/
				//require-once 'Dkplus/Acl/Adapter/Exception.php';
				throw new Dkplus_Acl_Adapter_Exception(
					'Role '.$role.' is no child of '.$parentRole.'.');
			}
			$rowRole->__set($this->_getRoleColumn(self::ROLE_PARENT), null);
			$rowRole->save();
		}
		else{
			/**
			 * @see Dkplus_Acl_Adapter_Exception
			 */
			//require-once 'Dkplus/Acl/Adapter/Exception.php';
			throw new Dkplus_Acl_Adapter_Exception(
				'There is no possibility for inheritance defined.');
		}
		return $this;
	}
}