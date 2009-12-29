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
 * @see Dkplus_Model_Criteria
 */
//require-once 'Dkplus/Model/Criteria.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @subpackage Db
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Row_Excel extends Dkplus_Model_Row_Abstract{
	
	public function __construct(Dkplus_Model_Interface $model, $row = null, $new = FALSE){
		parent::__construct($model, $row, $new);
		$this->_insert = !$this->isSaved();
	}
	
	/**
	 * 
	 * @var boolean
	 */
	protected $_insert = false;
	
	/**
	 * 
	 * @var array
	 */
	protected $_unsavedData = array();
	
	/**
	 * @param string $property
	 * @return boolean
	 */
	protected function _issetData($property){
		return isset($this->_data[$property]);
	}
	
	/**
	 * @param string $property
	 * @return mixed
	 */
	protected function _getData($property){
		return $this->_data[$property];
	}
	
	/**
	 * @param string $property
	 * @param mixed $value
	 * @return Dkplus_Model_Row_Interface
	 */
	protected function _setData($property, $value){
		$this->_data[$property] = $value;
		return $this;
	}
	
	/**
	 * @return Dkplus_Model_Row_Abstract
	 */
	protected function _save(){
		if(
			!$this->_insert
		){
			$crit = new Dkplus_Model_Criteria();
			foreach($this->_unsavedData AS $k => $v){
				$crit->andWhere($k, $v);
			}
			$this->_getModel()->update($this->_data, $crit);
		}
		else{
			$this->_getModel()->insert($this->_data);
			$this->_insert = false;
		}
		
		
		$this->_unsavedData = $this->_data;
		return $this;
	}
	
	/**
	 * @return Dkplus_Model_Row_Abstract
	 */
	protected function _delete(){
		$crit = new Dkplus_Model_Criteria();
		foreach($this->_unsavedData AS $k => $v){
			$crit->andWhere($k, $v);
		}
		$this->_getModel()->delete($crit);
		return $this;
	}
	
	/**
	 * @return array
	 */
	protected function _toArray(){		
		return $this->_data;	
	}
	
	/**
	 * @param Zend_Db_Table_Row_Abstract $row
	 * @return Dkplus_Model_Row_Interface
	 * @throws Dkplus_Model_Exception if the parameter is not an array or an object.
	 */
	protected function _setRow($data){
		if(
			!is_array($data)
		){
			throw new Dkplus_Model_Exception('First parameter must be an array.');
		}
		 
		parent::_setRow($data);
		if(
			$this->_getModel() instanceOf Dkplus_Model_Excel_Interface
			&& $this->_getModel()->isReadOnly()
		){
			$this->setReadOnly();
		}
		$this->_unsavedData = $this->_data;
		return $this;
	}
}