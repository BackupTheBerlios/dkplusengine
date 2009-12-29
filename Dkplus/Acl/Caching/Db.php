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
 * @package    Acl
 * @subpackage Caching
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    27.07.2009 00:36:33
 */

/**
 * @category   Dkplus
 * @package    Acl
 * @subpackage Caching
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Acl_Caching_Db implements Dkplus_Acl_Caching_Interface{
	const TIMESTAMP = 'timestamp';
	const ROLE = 'role';
	const RESOURCE = 'resource';

	protected $_lastChecking = 0;

	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_table = null;

	/**
	 *
	 * @var array
	 */
	protected $_columns = array();

    public function __construct(Zend_Db_Table_Abstract $table, array $columns){
		$this->_table = $table;

		if(
			!isset($columns[self::TIMESTAMP])
			|| !is_string($columns[self::TIMESTAMP])
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require_once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('There is no valid column with key '.self::TIMESTAMP.' set.');
		}

		if(
			!isset($columns[self::RESOURCE])
			|| !is_string($columns[self::RESOURCE])
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require_once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('There is no valid column with key '.self::RESOURCE.' set.');
		}

		if(
			!isset($columns[self::ROLE])
			|| !is_string($columns[self::ROLE])
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require_once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('There is no valid column with key '.self::ROLE.' set.');
		}

		$this->_lastChecking = time();
		$this->_columns = $columns;
	}

	/**
	 * @return Zend_Db_Table_Abstract
	 */
	protected function _getDbTable(){
		return $this->_table;
	}

	/**
	 * @return string
	 */
	protected function _getRoleColumn(){
		return $this->_columns[self::ROLE];
	}

	/**
	 * @return string
	 */
	protected function _getResourceColumn(){
		return $this->_columns[self::RESOURCE];
	}

	/**
	 * @return string
	 */
	protected function _getTimestampColumn(){
		return $this->_columns[self::TIMESTAMP];
	}

	/**
	 * Markiert eine Kombination in der Datenbank als geladen.
	 * @param Zend_Acl_Role_Interface|string $role
	 * @param Zend_Acl_Rule_Interface|string $resource
	 * @return Dkplus_Acl_Caching_Db Provides a fluent interface.
	 */
	public function change($role = null, $resource = null){
		if(
			is_null($role)
			AND is_null($resource)
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require_once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('Resource or role must be a string.');
		}

		$role = is_null($role)
			? $role
			: (
				$role instanceOf Zend_Acl_Role_Interface
				? $role->getRoleId()
				: (string) $role
			);

		$resource = is_null($resource)
			? $resource
			: (
				$resource instanceOf Zend_Acl_Resource_Interface
				? $resource->getResourceId()
				: (string) $resource
			);

		$this->_getDbTable()->delete(
			array(
				$this->_getDbTable()->getAdapter()->quoteInto(
					$this->_getResourceColumn() . ' = ?', $resource),
				$this->_getDbTable()->getAdapter()->quoteInto(
					$this->_getRoleColumn() . ' = ?', $role),
				$this->_getDbTable()->getAdapter()->quoteInto(
					$this->_getTimestampColumn() . ' < ?', time())
			)
		);

		$this->_getDbTable()->insert(
			array(
				$this->_getTimestampColumn()	=> time(),
				$this->_getResourceColumn()		=> $resource,
				$this->_getRoleColumn()			=> $role
			)
		);

		return $this;
	}

	/**
	 * Gibt alle Veränderungen zurück.
	 * @param Zend_Acl_Role_Interface|string $role
	 * @param Zend_Acl_Rule_Interface|string $resource
	 */
	public function getChanges(){
		$sel = $this->_getDbTable()->select()
			->from(
				$this->_getDbTable()->info('name'),
				array(
					self::RESOURCE	=> $this->_getResourceColumn(),
					self::ROLE		=> $this->_getRoleColumn()
				)
			)
			->where($this->_getTimestampColumn().' > ?', $this->_lastChecking);
		$this->_lastChecking = time();
		return $this->_getDbTable()->fetchAll($sel)->toArray();
	}
}