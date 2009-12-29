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
 * @version    07.04.2009 15:48:16
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** 
 * @see Dkplus_Model_Db_Interface  
 */
//require-once 'Dkplus/Model/Db/Interface.php';

/** 
 * @see Dkplus_Model_Abstract
 */
//require-once 'Dkplus/Model/Abstract.php';

/**
 * <p>Die Db-Variante des Models dient als weitere Abstraktionsschicht, die den
 * direkten Zugriff auf das Zend_Db_Table_Abstract-Objekt verweigert.</p>
 * 
 * <p>
 * Um ein Model mit Dkplus_Model_Db_Abstract zu erstellen, muss eine Klasse 
 * geschrieben werden, die Methode {@link _setDbTable()} überschreibt.
 * </p>
 * 
 * <p>
 * Das "inneren Objekt" wird standardmäßig über Sessions gespeichert. So ist es
 * möglich, z.B. die Userdaten oder andere Daten persistent zu speichern und zentral
 * zu nutzen.
 * </p>
 * 
 * <p>
 * Ein weiteres Feature nennt sich Interflection. Über das Attribut {@link $_alias}
 * lassen sich Spaltenwerte mit Alias-Werten belegen.
 * <code>
 * protected $_alias = array(
 * 	'Name'	=> 'user_name',
 * 	'Id'	=> 'user_id'
 * );
 * </code>
 * Diese Alias-Tabelle lässt sich z.B. für das Einfügen neuer Werte nutzen:
 * <code>
 * $data = array(
 * 	'Name'	=> 'Oskar Bley',
 * 	'Id'	=> 5
 * );
 * Model_User::getInstance()->insert($data);
 * 
 * //Hat den gleichen Effekt wie:
 * $data = array(
 * 	'user_name'	=> 'Oskar Bley',
 * 	'user_id'	=> 5
 * );
 * Model_User::getInstance()->insert($data);
 * </code>
 * Die Inflections sind durchgehend bei allen Methoden eingearbeitet.
 * Zudem gibt es eine weitere Möglichkeit, die die eingebaute Inflection bietet:
 * <code>
 * $rowset = Dkplus_Model_Factory::get('User')->fetchByName('Oskar Bley');
 * 
 * //Ist das Gleiche wie
 * $rowset = Dkplus_Model_Factory::get('User')->fetchRowsetByName('Oskar Bley');
 * 
 * //Und auch das Gleiche wie
 * $rowset = Dkplus_Model_Factory::get('User')->fetchRowset('Oskar Bley', 'Name');
 * 
 * //Und entspricht letztendlich folgendem:
 * $rowset = Dkplus_Model_Factory::get('User')->fetchRowset('Oskar Bley', 'user_name');
 * 
 * //Auch zum Holen eines einzelnen Eintrages gibt es verschiedene Möglichkeiten:
 * $row = Dkplus_Model_Factory::get('User')->fetchRowByName('Oskar Bley');
 * $row = Dkplus_Model_Factory::get('User')->fetchRow('Oskar Bley', 'Name');
 * $row = Dkplus_Model_Factory::get('User')->fetchRow('Oskar Bley', 'user_name');
 * </code>
 * </p>
 * 
 * @category   Dkplus
 * @package    Dkplus_Model
 * @subpackage Db
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Dkplus_Model_Db_Abstract extends Dkplus_Model_Abstract implements Dkplus_Model_Db_Interface{	
	
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
				$select = $this->_critToQuery($crit);
				$select->where($this->_getReference($modelClass).' = ?', $modelValue);
				return $this->_fetchRowsetBySelect($select);
			}
			return $this->fetchRowset($crit);
		}
		
		if(
			$this->_hasManyToManyReference($modelClass)
		){
			if(
				is_null($crit)
			){
				$select = $this->_getDbTable()->select();
			}
			else{
				$select = $this->_critToQuery($crit);
			}
			return $this->_fetchRowsetBySelect(
				$select
					->from(array('a' => $this->_getReferenceTable($modelClass)), '')
					->joinLeft(
						array('b' => $this->_getDbTable()->info('name')), 
						'a.' . $this->_getReferenceTableCol($modelClass) . ' = b.' . $this->_getReferenceCol($modelClass),
						'b.*'
					)
					->where('a.'.$this->_getReferenceModelCol($modelClass).' = ?', $modelValue)
			);
		}

		throw new Dkplus_Model_Exception('There is no relation defined for class '.$modelClass.'.');
	}
	
	/**
	 * @see Dkplus/Model/Db/Dkplus_Model_Db_Interface#getConnection()
	 */
	public function getConnection(){
		return $this->_getDbTable();
	}
	
	/**
	 * <p>Die zu nutzende Klasse für Row-Objekte.</p>
	 * 
	 * <p>Mittels dieses Attributes lässt sich eine andere Klasse als die
	 * Standard-Klasse {@link Dkplus_Model_Row_Db} für die 
	 * {@link Dkplus_Model_Row_Interface Row-Objekte} nutzen.</p>
	 * 
	 * <p>Um eine volle Unterstützung zu gewährleisten, sollte sie das
	 * {@link Dkplus_Model_Db_Connectable_Interface} implementieren.</p>
	 * 
	 * @var string
	 */
	protected $_rowClass = 'Dkplus_Model_Row_Db';
	
	/**
	 * <p>Die Tabelle sollte nicht mit gespeichert werden, um keine Zugangsdaten in
	 * der Session zu speichern.</p>
	 * @return array
	 */
	public function __sleep(){
		return array('_sessionNamespace', '_rowClass', '_rowsetClass', '_alias', 
			'_referenceMap', '_dependencies');
	}
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_dbTable = null;
	
	/**
	 * Hook zum Setzen des DbTable-Objektes.
	 * @return Dkplus_Model_Db_Abstract
	 */
	abstract protected function _setDbTable();
	

	
	/**
	 * <p>Gibt das als Datenquelle dienende Table-Objekt zurück.</p>
	 * @return Zend_Db_Table_Abstract
	 * @throws {@link Dkplus_Model_Exception} if the table has not been set before.
	 * Wirft eine {@link Dkplus_Model_Exception} wenn die Tabelle nicht gesetzt wurde.
	 */
	protected function _getDbTable(){
		if(
			is_null($this->_dbTable)
		){
			$this->_setDbTable();
			if(
				is_null($this->_dbTable)
			){
				/** 
				 * @see Dkplus_Model_Exception
 				*/
				//require-once 'Dkplus/Model/Exception.php';
				throw new Dkplus_Model_Exception('Table must be set before.');
			}
		}
		return $this->_dbTable;
	}	
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return int
	 */
	public function count(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			is_null($crit)
		){
			$select = $this->_getDbTable()->select();
		}
		else{
			$select = $this->_critToQuery($crit);
		}

		return $this->_getDbTable()->fetchRow(
			$select->from(
				array('a' => $this->_getDbTable()->info('name')),
				array('count' => 'COUNT(*)')
			)
		)->count;
	}
	
	/**
	 * 
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Zend_Db_Table_Select
	 */
	protected function _critToQuery(Dkplus_Model_Criteria_Interface $crit){
		$sel = $this->_getDbTable()->select();

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
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @throws {@link Dkplus_Model_Exception} on wrong parameters.
	 * @see Dkplus/Model/Dkplus_Model_Interface#fetchRowset()
	 */
	public function fetchRowset(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			!is_null($crit)
		){
			$sel = $this->_critToQuery($crit);
		}
		else{
			$sel = $this->_getDbTable()->select();
		}

		return new $this->_rowsetClass(
			$this,
			$this->_getDbTable()->fetchAll(
				$sel
			)
		);
	}
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @throws {@see Dkplus_Model_Exception} on wrong parameters.
	 * @return Dkplus_Model_Row_Interface Wurde kein Eintrag gefunden, so wird null
	 * zurückgegeben.
	 */
	public function fetchRow(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			!is_null($crit)
		){
			$sel = $this->_critToQuery($crit);
		}
		else{
			$sel = $this->_getDbTable()->select();
		}

		$dbRow = $this->_getDbTable()->fetchRow($sel);
		
		if(
			is_null($dbRow)
		){
			return null;
		}		

		return new $this->_rowClass($this, $dbRow);	
		
	}
	
	/**
	 * Fügt Daten zu der Datenquelle hinzu.
	 * @param array $data
	 * @return Dkplus_Model_Interface
	 */
	public function insert(array $data){
		$data = $this->_transformArray($data);
		$fields = $this->_getDbTable()->info(Zend_Db_Table_Abstract::COLS);
		foreach($data AS $k => $v){
			if(
				!in_array($k, $fields)
			){
				unset($data[$k]);
			}
		}
		return $this->_getDbTable()->insert($data);
	}
	
	/**
	 * Aktualisiert Daten aus der Datenquelle.
	 * Achtung, einige Datenquellen (wie z.B. einige Webservices) können Read-Only sein!
	 * @param array $data
	 * @param Dkplus_Model_Criteria_Interface $crit Es werden nur die Where-Argumente des Kriteriums angenommen.
	 * @return int The number of updated records. Die Anzahl der aktualisierten Datensätze. 
	 */
	public function update(array $data, Dkplus_Model_Criteria_Interface $crit = null){
		$data = $this->_transformArray($data);
		
		if(
			is_null($crit)
		){
			$where = '';
		}
		else{
			$where = implode(' ', $this->_critToQuery($crit)
				->getPart(Zend_Db_Select::WHERE));
		}
		return $this->_getDbTable()->update($data, $where);
	}
	
	/**
	 * @param Dkplus_Model_Criteria_Interface $crit = null
	 * @return int The number of deleted records. Die Anzahl der gelöschten Datensätze. 
	 */
	public function delete(Dkplus_Model_Criteria_Interface $crit = null){
		if(
			is_null($crit)
		){
			$where = '';
		}
		else{
			$where = implode(' ', $this->_critToQuery($crit)
				->getPart(Zend_Db_Select::WHERE));
		}
		return $this->_getDbTable()->delete($where);
	}
	
	/**
	 * @return Dkplus_Model_Row_Interface
	 * @uses $_rowClass
	 */
	public function fetchNewRow(){
		return new $this->_rowClass($this, $this->_getDbTable()->fetchNew(), true);
	}
	
	/**
	 * @param Zend_Db_Table_Select $select
	 * @return Dkplus_Model_Rowset_Interface
	 */
	protected function _fetchRowsetBySelect(Zend_Db_Table_Select $select){
		return new $this->_rowsetClass(
			$this,
			$this->_getDbTable()->fetchAll(
				$select
			)
		);
	}

	public function getPaginator(Dkplus_Model_Criteria_Interface $crit = null){
		$unaliasData = array();
		foreach($this->_unalias AS $unalias => $alias){
			$unaliasData[$alias] = $unalias;
		}
		$select = is_null($crit)
			? $this->_getDbTable()->select()
			: $this->_critToQuery($crit);
		$select = $select
			->from($this->_getDbTable()->info(Zend_Db_Table::NAME), $unaliasData);
		return Zend_Paginator::factory($select);
	}
}