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
 * @package    Dkplus_Model
 * @subpackage Db
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    08.04.2009 00:17:57
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Dkplus_Model_Row_Abstract
 */
//require-once 'Dkplus/Model/Row/Abstract.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @subpackage Db
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Row_Db extends Dkplus_Model_Row_Abstract{	
	/**
	 * @param string $property
	 * @return boolean
	 */
	protected function _issetData($property){
		return isset($this->_data->$property);
	}
	
	/**
	 * @param string $property
	 * @return mixed
	 */
	protected function _getData($property){
		return $this->_data->$property;
	}
	
	/**
	 * @param string $property
	 * @param mixed $value
	 * @return Dkplus_Model_Row_Interface
	 */
	protected function _setData($property, $value){
		$this->_data->$property = $value;
		return $this;
	}
	
	/**
	 * @return Dkplus_Model_Row_Abstract
	 */
	protected function _save(){
		if(
			!$this->_data->isConnected()
		){
			$this->_data->setTable($this->_getModel()->getConnection());
		}
		$this->_data->save();
		return $this;
	}
	
	/**
	 * @return Dkplus_Model_Row_Abstract
	 */
	protected function _delete(){
		if(
			!$this->_data->isConnected()
		){
			$this->_data->setTable($this->_getModel()->getConnection());
		}
		$this->_data->delete();
		return $this;
	}
	
	/**
	 * @return array
	 */
	protected function _toArray(){		
		return $this->_data->toArray();	
	}
	
	/**
	 * @param Zend_Db_Table_Row_Abstract $row
	 * @return Dkplus_Model_Row_Interface
	 * @throws Dkplus_Model_Exception if the parameter is not an array or an object.
	 */
	protected function _setRow($data){
		if(
			!is_object($data)
			|| !($data instanceOf Zend_Db_Table_Row_Abstract)
		){
			throw new Dkplus_Model_Exception('First parameter must be an instance of Zend_Db_Table_Row_Abstract.');
		}
		 
		parent::_setRow($data);
		if(
			$this->_data->isReadOnly()
		){
			$this->setReadOnly();
		}
		return $this;
	}
}