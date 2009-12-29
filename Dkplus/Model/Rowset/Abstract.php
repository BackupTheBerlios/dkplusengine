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
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    07.04.2009 17:53:40
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** 
 * @see Dkplus_Model_Rowset_Interface 
 */
//require-once 'Dkplus/Model/Rowset/Interface.php';

/** 
 * @see Dkplus_Model_Exception 
 */
//require-once 'Dkplus/Model/Exception.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Dkplus_Model_Rowset_Abstract implements Dkplus_Model_Rowset_Interface{
	
	/**
	 * 
	 * @param Dkplus_Model_Interface $model
	 * @param mixed $rows
	 * @return void
	 */
	public function __construct(Dkplus_Model_Interface $model, $rows = null){
		$this->_model = $model;
		$this->_rowClass = $this->_model->getRowClass();
		if(
			!is_null($rows)
		){
			$this->addRows($rows);
		}
	}
	
	/**
	 * @var Dkplus_Model_Interface
	 */
	protected $_model = null;
	
	/**
	 * 
	 * @return Dkplus_Model_Interface
	 * @throws {@link Dkplus_Model_Exception} if the model has been removed.
	 * Wirft eine {@link Dkplus_Model_Exception} wenn das Model entfernt wurde.
	 */
	protected function _getModel(){
		if(
			is_null($this->_model)
		){
			throw new Dkplus_Model_Exception('Model has been removed from rowset.');
		}
		return $this->_model;
	}
	
	/**
	 * @var array
	 */
	protected $_rows = array();
	
	/**
	 * @var int
	 */
	private $_position = 0;
	
	/**
	 * @var boolean
	 */
	protected $_isReadOnly = false;
	
	/**
	 * @var string
	 */
	protected $_rowClass = 'Dkplus_Model_Row';
	
	/**
	 * @var int
	 */
	protected $_count = 0;
	

	/**
	 * <p>Setzt die interne Position zurück auf den Anfang.</p>
	 * @uses rewind()
	 * @return void
	 */
	public function __wakeup(){
		$this->rewind();	
	}
	
	/**
	 * @return int
	 */
	public function count(){
		return $this->_count;
	}
	
	/**
	 * @return array
	 */
	public function toArray(){
		$arr = array();
		foreach($this->_rows AS $row){
			$arr[] = $row->toArray();
		}
		return $arr;
	}
	
	/**
	 * @return Dkplus_Model_Rowset_Abstract
	 */
	public function rewind(){
		$this->_position = 0;
		return $this;
	}
	
	/**
	 * @return Dkplus_Model_Row_Interface
	 */
	public function current(){
		if(
			$this->valid()
		){
			return $this->_rows[$this->_position];
		}
		return null;
	}
	
	/**
	 * @return int
	 */
	public function key(){
		return $this->_position;
	}

	/**
	 * @return boolean
	 */
	public function valid(){
		if(
			$this->_position >= $this->_count
		){
			$this->rewind();
			return false;
		}
		return true;		
	}

	/**
	 * @return Dkplus_Model_Rowset_Abstract
	 */
	public function next(){
		++$this->_position;
		return $this;
	}
	
	/**
	 * @return Dkplus_Model_Rowset_Abstract
	 */
	public function previous(){
		--$this->_position;
		return $this;
	}
	
	/**
	 * @param int $offset
	 * @return boolean
	 */
	public function offsetExists($offset){
		if(
			!is_int($offset)
		){
			throw new Dkplus_Model_Exception(
				sprintf('First Parameter has an invalid type "%s", must be an integer.', 
    				getType($offset)
    			)
    		);
		}
		return isset($this->_rows[$offset]);
	}
	
	/**
	 * 
	 * @param int $offset
	 * @return Dkplus_Model_Row_Interface
	 */
	public function offsetGet($offset){
		if(
			!$this->offsetExists($offset)
		){
			throw new Dkplus_Model_Exception('There is no element with an offset '.$offset);
		}
		return $this->_rows[$offset];
	}
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value){
		throw new Dkplus_Model_Exception('You cannot add a row using offsetSet().');
	}
	
	/**
	 * @param int $offset
	 * @return Dkplus_Model_Rowset_Abstract 
	 */
	public function offsetUnset($offset){
		if(
			!$this->offsetExists($offset)
		){
			throw new Dkplus_Model_Exception('There is no element with an offset '.$offset);
		}
		unset($this->_rows[$offset]);
		$this->_count--;
		$this->_rows = array_values($this->_rows);
		$this->previous();
		return $this;
	}
	
	/**
	 * @param string $property
	 * @param mixed $value
	 * @see Model/Rowset/Dkplus_Model_Rowset_Interface#__set()
	 */
	public function __set($property, $value){
		foreach($this->_rows AS &$row){
			$row->__set($property, $value);
		}
	}
	
	/**
	 * @see Model/Rowset/Dkplus_Model_Rowset_Interface#isReadOnly()
	 */
	public function isReadOnly(){
		return $this->_isReadOnly;
	}
	
	/**
	 * @see Model/Rowset/Dkplus_Model_Rowset_Interface#setReadOnly()
	 */
	public function setReadOnly(){
		$this->_isReadOnly = true;
		return $this;
	}
	
	/**
	 * @see Dkplus/Model/Rowset/Dkplus_Model_Rowset_Interface#setRowsReadOnly()
	 */
	public function setRowsReadOnly(){
		foreach($this->_rows AS $row){
			$row->setReadOnly();
		}
		return $this;
	}
	
	/**
	 * @param mixed $row
	 * @return Dkplus_Model_Rowset_Interface
	 * @throws Dkplus_Model_Exception on wrong parameter or if the row is read-only.
	 * @see Model/Rowset/Dkplus_Model_Rowset_Interface#addRow()
	 */
	public function addRow($row){
		if(
			$this->isReadOnly()
		){
			throw new Dkplus_Model_Exception('You cannot add a row to a read-only rowset.');
		}
		
		if(
			!$row instanceOf Dkplus_Model_Row_Interface
		){
			/**
			 * @var Dkplus_Model_Row_Interface
			 */
			$row = new $this->_rowClass($this->_getModel(), $row);
		}
		
		if(
			strToLower($this->_rowClass) != strToLower(get_class($row))
		){
			throw new Dkplus_Model_Exception(
				sprintf('First Parameter has an invalid class "%s", must be an '.$this->_rowClass.'.', 
    				get_class($r)
    			)
    		);
		}
		$this->_count++;
		$this->_rows[] = $row;
		return $this;
	}
	
	/**
	 * @param array|Traversable $rows
	 * @return Dkplus_Model_Rowset_Interface
	 * @throws {@link Dkplus_Model_Exception} on wrong parameter or if the rowset is read-only.
	 * Wirft eine {@link Dkplus_Model_Exception} bei falschen Parametern oder 
	 * wenn der Rowset "readOnly" ist. 
	 * @see Model/Rowset/Dkplus_Model_Rowset_Interface#addRows()
	 */
	public function addRows($rows){
		if(
			$this->isReadOnly()
		){
			throw new Dkplus_Model_Exception('You cannot add a row to a read-only rowset.');
		}
		if(
			!is_array($rows)
			&& !$rows instanceOf Traversable
		){
			throw new Dkplus_Model_Exception(
				sprintf('First parameter is an %s, must be an array or an implementation of Traversable.', 
					getType($rows))
			);
		}
		foreach($rows AS $row){
			$this->addRow($row);
		}
		return $this;
	}
	
	/**
	 * Hook
	 * @return Dkplus_Model_Rowset_Interface
	 */
	protected function _fetchNewRowset(){
		$className = get_class($this);
		return new $className($this->_getModel());
	}
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function filter(Dkplus_Model_Criteria_Interface $crit){
		//Zuerst erschaffen wir einen neuen Rowset, der nachher zurückgegeben wird. 
		$rowset = $this->_fetchNewRowset();
		
		$exec = new Dkplus_Model_Criteria_Executor();
		$exec->setCriteria($crit);
		$exec->setArray($this->_rows);
		
		$rowset->addRows($exec->execute());
		
		return $rowset;
	}
	
	/**
	 * @see Model/Rowset/Dkplus_Model_Rowset_Interface#fetchNewRow()
	 */
	public function fetchNewRow(){
		return $this->_model->fetchNewRow();
	}
	
	/**
	 * @see Model/Rowset/Dkplus_Model_Rowset_Interface#getNewRow()
	 */
	public function save(){
		foreach($this->_rows AS $row){
			$row->save();
		}
		return $this;
	}
}