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
 * @package    Dkplus_
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    07.04.2009 21:39:01
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Dkplus_Model_Exception
 */
//require-once 'Dkplus/Model/Exception.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Row extends Dkplus_Model_Row_Abstract{
	
	/**
	 * @var string
	 */
	protected $_saveMethod = null; 
	
	/**
	 * @var string
	 */	
	protected $_deleteMethod = null;
	
	/**
	 * @var string
	 */
	protected $_arrayMethod = null;
	
	/**
	 * <p>Löscht die Daten aus der Datenquelle.</p>
	 * @return Dkplus_Model_Row_Abstract
	 * @throws Dkplus_Model_Exception if there is no delete-method available.
	 */
	protected function _delete(){
		if(
			!is_null($this->_deleteMethod)
		){
			if(
				!method_exists($this->_data, $this->_deleteMethod)
			){
				throw new Dkplus_Model_Exception('The given delete-method is not available.');
			}
			call_user_func_array(array($this->_data, $this->_deleteMethod));
			return $this;
		}
		
		$arrMethods = array('delete', 'remove', 'record', 'write');
		foreach($arrMethods AS $method){
			if(
				method_exists($this->_data, $method)
			){
				call_user_func_array(array($this->_data, $method));
				return $this;
			}
		}
		throw new Dkplus_Model_Exception('Row cannot be deleted because there is not delete-method available.');
	}
	
	/**
	 * <p>Speichert die Daten in der Datenquelle.</p>
	 * <p>Die Überprüfung auf ReadOnly und vorheriges Speichern muss nicht 
	 * implementiert werden. Ebenso wird das Setzen der {@link _isSaved} 
	 * Eigenschaft schon implementiert durch die {@link save()} Methode.
	 * @return Dkplus_Model_Row_Abstract
	 * @throws Dkplus_Model_Exception if there is no save-method available.
	 */
	protected function _save(){
		if(
			!is_null($this->_saveMethod)
		){
			if(
				!method_exists($this->_data, $this->_saveMethod)
			){
				throw new Dkplus_Model_Exception('The given save-method is not available.');
			}
			call_user_func_array(array($this->_data, $this->_saveMethod));
			return $this;
		}
		
		$arrMethods = array('save', 'update', 'record', 'write');
		foreach($arrMethods AS $method){
			if(
				method_exists($this->_data, $method)
			){
				call_user_func_array(array($this->_data, $method));
				return $this;
			}
		}
		throw new Dkplus_Model_Exception('Row cannot be saved because there is not save-method available.');
	}
	
	/**
	 * <p>Wandelt die Row in einen assoziativen Array um.</p>
	 * @return array
	 */
	protected function _toArray(){
		$return = null;
		
		if(
			is_array($this->_data)
		){
			return $this->_data;
		}
		
		if(
			!is_null($this->_arrayMethod)
		){
			if(
				!method_exists($this->_data, $this->_arrayMethod)
			){
				throw new Dkplus_Model_Exception('The given array-method is not available.');
			}
			$return = call_user_func_array(array($this->_data, $this->_arrayMethod));
		}
		
		if(
			empty($return)
		){
			$arrMethods = array('toArray', 'asArray', 'makeArray', 'getArray');
			foreach($arrMethods AS $method){
				if(
					method_exists($this->_data, $method)
				){
					$return = call_user_func_array(array($this->_data, $method));
				}
			}
		}

		if(
			$this->_data instanceof Traversable
		){
			$return = array();
			foreach($this->_data AS $data){
				$return[] = $data;
			}
		}
		
		if(
			!is_null($return)
			&& is_array($return)
		){
			return $return;
		}
		elseif(
			!is_array($return)
		){
			throw new Dkplus_Model_Exception('Row cannot be saved because there is no valid array-method available.');
		}

		throw new Dkplus_Model_Exception('Row cannot be saved because there is no array-method available.');
				
	}
	
	/**
	 * @param string $property
	 * @return scalar
	 */
	protected function _getData($property){
		if(
			is_array($this->_data)
		){
			return $this->_data[$property];
		}
		
		if(
			$this->_data instanceof ArrayAccess
		){
			return $this->_data[$property];
		}
		
		return $this->_data->$property;
	}
	
	/**
	 * @param string $property
	 * @return boolean
	 */
	protected function _issetData($property){
		
		if(
			is_array($this->_data)
		){
			return isset($this->_data[$property]);
		}
		
		if(
			$this->_data instanceof ArrayAccess
		){
			return isset($this->_data[$property]);
		}
		
		return isset($this->_data->$property);
	}	
	
	/**
	 * 
	 * @param string $property
	 * @param mixed $value
	 * @return Dkplus_Model_Row_Interface
	 * @uses $_setUnsaved()
	 */
	protected function _setData($property, $value){
		$this->_setUnsaved();
		if(
			is_array($this->_data)
		){
			$this->_data[$property] = $value;
			return $this;
		}
		
		if(
			$this->_data instanceof ArrayAccess
		){
			$this->_data[$property] = $value;
			return $this;
		}
		
		$this->_data->$property = $value;
		return $this;
	}
	
}