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
 * @subpackage Excel
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    07.04.2009 15:48:16
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** 
 * @see Dkplus_Model_Abstract  
 */
//require-once 'Dkplus/Model/Abstract.php';

/** 
 * @see Dkplus_Model_Excel_Interface  
 */
//require-once 'Dkplus/Model/Excel/Interface.php';

/** 
 * @see Dkplus_Model_Criteria_Executor
 */
//require-once 'Dkplus/Model/Criteria/Executor.php';

/**
 * 
 * @category   Dkplus
 * @package    Dkplus_Model
 * @subpackage Excel
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Excel extends Dkplus_Model_Abstract implements Dkplus_Model_Excel_Interface{

	/**
	 *
	 * @param string $file
	 * @param boolean $firstRowAsTitles
	 * @param boolean $readOnly
	 * @param string $charset
	 */
	public function __construct($file, $firstRowAsTitles = true, $readOnly = false, $charset = 'UTF-8'){
		$this->_file = (string) $file;
		if(
			!file_exists($this->_file)
			|| !is_readable($this->_file)
		){
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('File '.$this->_file.' does not exists or is not readable.');
		}
		$this->_readOnly = (boolean) $readOnly;
		$this->_firstRowAsTitles = (boolean) $firstRowAsTitles;
		$this->_charset = (string) $charset;

		parent::__construct();
	}

	/**
	 *
	 * @var string
	 */
	protected $_charset = 'UTF-8';
	
	/**
	 * 
	 * @var PHPExcel
	 */
	protected $_phpExcel = null;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $_firstRowAsTitles = false;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $_readOnly = false;
	
	/**
	 * Die zu lesende Datei.
	 * @var string
	 */
	protected $_file = '';
	
	/**
	 * 
	 * @var string
	 */
	protected $_type = 'csv';
	
	/**
	 * @var array
	 */
	protected $_data = null;
	
	/**
	 * 
	 * @return boolean
	 */
	public function isReadOnly(){
		return $this->_readOnly;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function _useFirstRowAsTitles(){
		return $this->_firstRowAsTitles;
	}



	/**
	 * <p>Gibt einen Paginator zurück.</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Zend_Paginator
	 */
	public function getPaginator(Dkplus_Model_Criteria_Interface $crit = null){
		throw new Dkplus_Model_Exception('Not yet implemented.');
		return;
	}
	
	/**
	 *
	 * @var array
	 */
	protected $_titles = array();


	/**
	 * 
	 * @return array
	 */
	protected function _getData(){
		if(
			is_null($this->_data)
		){
			/**
			 * @see PHPExcel_IOFactory
			 */
			//require_once 'PHPExcel/IOFactory.php';
			require_once 'PHPExcel.php';

			$reader = PHPExcel_IOFactory::createReaderForFile($this->_file);
			if(
				method_exists($reader, 'setReadDataOnly')
			){
				$reader->setReadDataOnly(true);
			}
			$this->_phpExcel = $reader->load($this->_file);
			//$this->_phpExcel = PHPExcel_IOFactory::load($this->_file);
			
			//$this->_type = PHPExcel_IOFactory::getLastReader();

			$this->_data = $this->_phpExcel->getActiveSheet()->toArray();

			if(
				$this->_charset != 'UTF-8'
			){
				if(
					!function_exists('iconv')
				){
					/**
					 * @see Dkplus_Model_Exception
					 */
					//require-once 'Dkplus/Model/Exception.php';
					throw new Dkplus_Model_Exception('Function iconv must be enabled to use charset-converting.');
				}
				foreach($this->_data AS &$data){
					foreach($data AS &$value){
						$value = iconv($this->_charset, 'UTF-8//TRANSLIT', $value);
					}
				}
			}

			if(
				$this->_useFirstRowAsTitles()
			){
				$this->_titles = $this->_data[1];
				unset($this->_data[1]);
				$this->_data = array_values($this->_data);
				foreach($this->_data AS $key => $row){
					foreach($row AS $k => $v){
						$row[$this->_titles[$k]] = $v;
						unset($row[$k]);
					}
					$this->_data[$key] = $row;
				}
			}
		}	
		return $this->_data;
	}
	
	/**
	 * <p>Holt alle vom übergebenen Model abhängigen Werte.</p>
	 * @param Dkplus_Model_Interface $model
	 * @param scalar $modelValue
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function fetchDependent(Dkplus_Model_Interface $model, $modelValue, $crit = null){
		$modelClass = get_class($model);
		
		if(
			$this->_hasOneToManyReference($modelClass)
		){	
			if(
				is_null($crit)
			){
				$crit = new Dkplus_Model_Criteria();
				$crit->andWhere($this->_getReference($modelClass), $modelValue);
			}
			elseif(
				$crit->getWhereConnector() == Dkplus_Model_Criteria::WHERE_AND
			){
				$crit->andWhere($this->_getReference($modelClass), $modelValue);
			}
			else{
				$crit2 = new Dkplus_Model_Criteria();
				$crit2->andWhere($this->_getReference($modelClass), $modelValue);				
				$exec = new Dkplus_Model_Criteria_Executor();
				$exec->setAlias($this->_alias)
					->setArray($this->_getData())
					->setCriteria($crit2);
				$data = $exec->execute();
				
				$exec = new Dkplus_Model_Criteria_Executor();
				$exec->setAlias($this->_alias)
					->setArray($data)
					->setCriteria($crit);
				$data = $exec->execute();
								
				return new $this->_rowsetClass($this, $data);
			}
			return $this->fetchRowset($crit);
		}
		
		/**
		 * @see Dkplus_Model_Exception
		 */
		//require-once 'Dkplus/Model/Exception.php';
		throw new Dkplus_Model_Exception('There is no Relation to '.$modelClass);
	}
	
	/**
	 * @var string
	 */
	protected $_rowClass = 'Dkplus_Model_Row_Excel';
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return int
	 */
	public function count(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			is_null($crit)
		){
			return count($this->_getData());
		}
		$exec = new Dkplus_Model_Criteria_Executor();
		$exec->setArray($this->_getData())
			->setCriteria($crit)
			->setAlias($this->_alias);
		return count($exec->execute());
	}

	/**
	 *
	 * @return array
	 */
	public function fetchRowsetAsArray(){
		return $this->_getData();
	}
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @throws {@link Dkplus_Model_Exception} on wrong parameters.
	 * @see Dkplus/Model/Dkplus_Model_Interface#fetchRowset()
	 */
	public function fetchRowset(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			is_null($crit)
		){
			return new $this->_rowsetClass($this, $this->_getData());
		}
		else{
			$exec = new Dkplus_Model_Criteria_Executor();
			$exec->setArray($this->_getData())
				->setCriteria($crit)
				->setAlias($this->_alias);
			return new $this->_rowsetClass($this, $exec->execute());
		}
	}
	
		/**
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @throws {@see Dkplus_Model_Exception} on wrong parameters.
	 * @return Dkplus_Model_Row_Interface Wurde kein Eintrag gefunden, so wird null
	 * zurückgegeben.
	 */
	public function fetchRow(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			is_null($crit)
		){
			$data = $this->_getData();
		}
		else{
			$exec = new Dkplus_Model_Criteria_Executor();
			$exec->setArray($this->_getData())
				->setCriteria($crit)
				->setAlias($this->_alias);
			$data = $exec->execute();
		}
		
		if(
			count($data) == 0
		){
			return null;
		}
		
		return new $this->_rowClass($this, $data[0]);
	}
	
	/**
	 * Fügt Daten zu der Datenquelle hinzu.
	 * @param array $data
	 * @return Dkplus_Model_Interface
	 */
	public function insert(array $data){
		if(
			$this->isReadOnly()
		){
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('Model is read-only.');
		}
		$this->_getData();
		
		$data = $this->_transformArray($data);

		$insertData = array();
		foreach($this->_titles AS $title){
			if(
				isset($data[$title])
			){
				$insertData[$title] = $data[$title];
			}
			else{
				$insertData[$title] = null;
			}
		}
				
		$this->_data[] = $insertData;
		$this->_write($this->_data);
		return $this;
	}
	
	/**
	 * Aktualisiert Daten aus der Datenquelle.
	 * Achtung, einige Datenquellen (wie z.B. einige Webservices) können Read-Only sein!
	 * @param array $data
	 * @param Dkplus_Model_Criteria_Interface $crit Es werden nur die Where-Argumente des Kriteriums angenommen.
	 * @return int The number of updated records. Die Anzahl der aktualisierten Datensätze. 
	 */
	public function update(array $data, Dkplus_Model_Criteria_Interface $crit = null){
		if(
			$this->isReadOnly()
		){
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('Model is read-only.');
		}

		$data = $this->_transformArray($data);

		$dataUpdate = array();
		foreach($this->_titles AS $title){
			if(
				isset($data[$title])
			){
				$dataUpdate[$title] = $data[$title];
			}
		}

		if(
			is_null($crit)
		){
			$this->_getData();
			foreach($this->_getData() AS $k => $v){
				$this->_data[$k] = array_merge($v, $dataUpdate);
			}
			$this->_write($this->_data);
		}
		else{
			$exec = new Dkplus_Model_Criteria_Executor();
			$dataToUpdate = $exec->setAlias($this->_alias)
				->setArray($this->_getData())
				->setCriteria($crit)
				->execute();

			foreach($this->_data AS $dataKey => $dataV){
				foreach($dataToUpdate AS $k => $v){
					if(
						count(array_diff_assoc($dataV, $v)) == 0
					){
						$this->_data[$dataKey] = array_merge($v, $dataUpdate);
						continue 2;
					}
				}
			}

			$this->_write($this->_data);
		}
	}
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit = null
	 * @return int The number of deleted records. Die Anzahl der gelöschten Datensätze. 
	 */
	public function delete(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			$this->isReadOnly()
		){
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('Model is read-only.');
		}
		
		$count = count($this->_getData());
		
		if(
			is_null($crit)
		){
			$this->_data = array();
			$this->_write($this->_data);
			return $count;
		}
		
		$exec = new Dkplus_Model_Criteria_Executor();
		$exec->setArray($this->_getData())
			->setAlias($this->_alias)
			->setCriteria($crit);
		$dataToDelete = $exec->execute();
		$count = count($dataToDelete);
		
		$this->_data = array_udiff($this->_getData(), $dataToDelete, array($this, '_arrayCompare'));
		$this->_write($this->_getData());
		
		return $count;
	}
	
	protected function _arrayCompare(array $arr1, array $arr2){
		foreach($arr1 AS $k => $v){
			if(
				!isset($arr2[$k])
				|| $arr2[$k] != $v
			){
				return -1;
			}
		}
		return 0;
	}
	
	protected function _write(array $data){
		//Umwandeln in numerischen Array
		foreach($data AS $k => $dataEntry){
			if(
				$this->_charset != 'UTF-8'
			){
				//Umwandeln des Zeichensatzes
				foreach($dataEntry AS $dataEntryK => $dataEntryV){
					$dataEntry[$dataEntryK] = iconv('UTF-8', $this->_charset.'//TRANSLIT', $dataEntryV);
				}
			}
			$data[$k] = array_values($dataEntry);
		}

		if(
			$this->_useFirstRowAsTitles()
		){
			$data = array_merge(array($this->_titles), $data);
		}

		$this->_phpExcel = new PHPExcel();
		$this->_phpExcel->addSheet();
		$this->_phpExcel->getActiveSheet()->fromArray($data);
		$this->_getWriter()->save($this->_file);	
	}
	
	/**
	 * @return Dkplus_Model_Row_Interface
	 * @uses $_rowClass
	 */
	public function fetchNewRow(){
		if(
			count($this->_getData()) == 0
		){
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('There are no rows stored, so properties cannot be resolved.');
		}

		return new $this->_rowClass($this, array_fill_keys($this->_titles, null), TRUE);
	}
	
	/**
	 * 
	 * @return PHPExcel_Writer_IWriter
	 */
	protected function _getWriter(){
		return PHPExcel_IOFactory::createWriter($this->_phpExcel, $this->_type);
	}
}