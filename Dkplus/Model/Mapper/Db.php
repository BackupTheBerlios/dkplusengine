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
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    22.09.2009 08:48:18
 */

/**
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Mapper_Db extends Dkplus_Model_Mapper_Abstract{
	protected static $_defaultAdapter = null;

	public static function setDefaultDbAdapter(Zend_Db_Adapter_Abstract $adapter){
		self::$_defaultAdapter = $adapter;
	}

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	protected static function _getDefaultDbAdapter(){
		if(
			is_null(self::$_defaultAdapter)
		){
			throw new Dkplus_Model_Mapper_Exception('No default adapter is defined.');
		}
		return self::$_defaultAdapter;
	}

	protected $_adapter = null;

	public function setDbAdapter(Zend_Db_Adapter_Abstract $adapter){
		$this->_adapter = $adapter;
		return $this;
	}

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	protected function _getDbAdapter(){
		if(
			!is_null($this->_adapter)
		){
			return $this->_adapter;
		}
		
		if(
			is_null(self::$_defaultAdapter)
		){
			throw new Dkplus_Model_Mapper_Exception('No adapter is defined.');
		}
		return self::$_defaultAdapter;
	}

	/**
	 * @return Zend_Db_Select
	 */
	public function _createSelect(){}

	/**
	 *
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Zend_Db_Table_Select
	 */
	protected function _critToSelect(Zend_Db_Select $select, Dkplus_Model_Criteria_Interface $crit = null){
		$sel = $this->_createSelect();
		if(
			is_null($crit)
		){
			return $sel;
		}

		//Where
		if(
			count($crit->getWheres()) > 0
		){
			$arrWherePart = array();
			$adapter = $this->_getDbTable()->getAdapter();
			foreach($crit->getWheres() AS $arrWhere){
				//Alias umwandeln
				if(
					$this->_hasAlias($arrWhere['col'])
				){
					$arrWhere['col'] = $this->_transformAlias($arrWhere['col']);
				}

				//Zusammenfügen, wenn der Haupt-Connektor wieder Auftritt
				if(
					$arrWhere['connector'] == $crit->getWhereConnector()
				){
					if(
						$crit->getWhereConnector() == Dkplus_Model_Criteria::WHERE_OR
					){
						$sel->orWhere(' ( '.implode(' AND ', $arrWherePart).' ) ');
					}
					else{
						$sel->where(' ( '.implode(' OR ', $arrWherePart).' ) ');
					}
					$arrWherePart = array();
				}

				//Hinzufügen einer Bedingung
				if(
					is_array($arrWhere['value'])
				){
					switch($arrWhere['type']){
						case Dkplus_Model_Criteria::WHERE_IS:
							$strCrit = ' IN(?)';
							break;
						case Dkplus_Model_Criteria::WHERE_IS_NOT:
							$strCrit = ' NOT IN(?)';
							break;
					}
				}
				else{
					switch($arrWhere['type']){
						case Dkplus_Model_Criteria::WHERE_IS:
							if(
								is_null($arrWhere['value'])
							){
								$strCrit = ' IS NULL';
								break;
							}
							$strCrit = ' = ?';
							break;
						case Dkplus_Model_Criteria::WHERE_IS_NOT:
							if(
								is_null($arrWhere['value'])
							){
								$strCrit = ' IS NOT NULL';
								break;
							}
							$strCrit = ' != ?';
							break;
						case Dkplus_Model_Criteria::WHERE_LIKE:
							if(
								is_null($arrWhere['value'])
							){
								$strCrit = ' IS NULL';
								break;
							}
							$strCrit = ' LIKE ?';
							$arrWhere['value'] = '%'.strToLower($arrWhere['value']).'%';
							break;
						case Dkplus_Model_Criteria::WHERE_NOT_LIKE:
							if(
								is_null($arrWhere['value'])
							){
								$strCrit = ' IS NOT NULL';
								break;
							}
							$strCrit = ' NOT LIKE ?';
							$arrWhere['value'] = '%'.strToLower($arrWhere['value']).'%';
							break;
						case Dkplus_Model_Criteria::WHERE_BIGGER:
							$strCrit = ' > ?';
							$arrWhere['value'] = strToLower($arrWhere['value']);
							break;
						case Dkplus_Model_Criteria::WHERE_SMALLER:
							$strCrit = ' < ?';
							$arrWhere['value'] = strToLower($arrWhere['value']);
							break;
					}
				}
				$arrWherePart[] = is_null($arrWhere['value'])
					? $arrWhere['col'].$strCrit
					: $adapter->quoteInto($arrWhere['col'].$strCrit, $arrWhere['value']);
			}

			//Hinzufügen der letzten Bedingung
			if(
				$crit->getWhereConnector() == Dkplus_Model_Criteria::WHERE_OR
			){
				$sel->orWhere(' ( '.implode(' AND ', $arrWherePart).' ) ');
			}
			else{
				$sel->where(' ( '.implode(' OR ', $arrWherePart).' ) ');
			}
		}


		//Order-Part der Query:
		foreach($crit->getOrders() AS $col => $order){
			$col = $this->_hasAlias($col)
				? $this->_transformAlias($col)
				: $col;
			$sel->order(
				$col
				.' '
				.($order == Dkplus_Model_Criteria::ORDER_ASC ? 'ASC' : 'DESC')
			);
		}

		//Hinzufügen der Limit-Klausel
		if(
			!is_null($crit->getLimitCount())
		){
			$sel->limit($crit->getLimitCount(), $crit->getLimitStart());
		}
		return $sel;
	}

	public function fetchRowset(Dkplus_Model_Criteria_Interface $crit = null){
		$select = $this->_critToSelect($this->_createSelect());
		$domainModels = array();
		foreach($this->_getDbAdapter()->fetchAll($select) AS $row){
			$row = (array) $row;
			$domainModels[] = $this->_createDomainObject($row);
		}
		return $domainModels;
	}

	public function fetchRow(Dkplus_Model_Criteria_Interface $crit = null){
		$select = $this->_critToSelect($this->_createSelect());
		return $this->_createDomainObject($this->_getDbAdapter()->fetchRow($select));
	}

	abstract function save(Dkplus_Model_Domain_Abstract $domain);
}