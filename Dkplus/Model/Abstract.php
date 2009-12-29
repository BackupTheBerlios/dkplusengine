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
 * @version    07.04.2009 15:48:16
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** 
 * @see Dkplus_Model_Interface  
 */
//require-once 'Dkplus/Model/Interface.php';

/** 
 * @see Dkplus_Model_Criteria
 */
//require-once 'Dkplus/Model/Criteria.php';

/** 
 * @see Zend_Session_Namespace
 */
//require-once 'Zend/Session/Namespace.php';

/**
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
 * Dkplus_Model_Factory::get('User')->insert($data);
 * 
 * //Hat den gleichen Effekt wie:
 * $data = array(
 * 	'user_name'	=> 'Oskar Bley',
 * 	'user_id'	=> 5
 * );
 * Dkplus_Model_Factory::get('User')->insert($data);
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
 * 
 * Die eingebaute Inflection bietet also die Möglichkeit, die dahinterliegende 
 * Datenquelle auf einfache Art und Weise austauschbar zu machen bzw. "schönere"
 * Namen für die einzelnen Attribute zu wählen.
 * </p>
 * 
 * 
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Dkplus_Model_Abstract implements Dkplus_Model_Interface{

	/**
	 * <p>Kann gesetzt werden, um den Session-Namespace zu setzen.</p>
	 * <p>Andernfalls wird der Name der Klasse als Session-Namespace genutzt.</p>
	 * 
	 * @var string
	 */
	protected $_sessionNamespace = null;
	
	/**
	 * <p>Session-Namespace-Objekt des Models.</p>
	 * 
	 * @var Zend_Session_Namespace
	 */
	protected $_session = null;
	
	/**
	 * <p>Gibt den Session-Namespace zur Speicherung des "inneren Objektes" zurück.</p>
	 * @return Zend_Session_Namespace
	 */
	protected function _getSessionNamespace(){
		if(
			is_null($this->_session)
		){
			$sessionNamespace = is_null($this->_sessionNamespace)
				? get_class($this)
				: (string) $this->_sessionNamespace;
			$this->_session = new Zend_Session_Namespace($sessionNamespace);
		}
		return $this->_session;
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
	protected $_rowClass = 'Dkplus_Model_Row';
	
	/**
	 * <p>Die zu nutzende Klasse für Rowset-Objekte.</p>
	 * 
	 * <p>Mittels dieses Attributes lässt sich eine andere Klasse als Standard-
	 * Klasse {@link Dkplus_Model_Rowset_Db} für die 
	 * {@link Dkplus_Model_Rowset_Interface Rowset-Objekte} nutzen.</p>
	 * 
	 * <p>Um eine volle Unterstützung zu gewährleisten, sollte sie das
	 * {@link Dkplus_Model_Db_Connectable_Interface} implementieren.</p>
	 * 
	 * @var string
	 */
	protected $_rowsetClass = 'Dkplus_Model_Rowset';
	
	/**
	 * <p>Alias-Liste für die Inflection.</p>
	 * @var array
	 */
	protected $_alias = array();
	
	/**
	 * <p>Alias-Liste für die Inflection in die andere Richtung.</p>
	 * @var array
	 */
	protected $_unalias = array();
	
	/**
	 * Initialisiert die Alias.
	 * @uses _initAlias()
	 */
	public function __construct(){
		if(
			!class_exists($this->_rowClass)
		){
			//require-once str_replace('_', '/', $this->_rowClass).'.php';
		}
		
		if(
			!class_exists($this->_rowsetClass)
		){
			//require-once str_replace('_', '/', $this->_rowsetClass).'.php';
		}
		$this->_initAlias();
	}
	
	/**
	 * <p>Hook, normiert die Alias-Werte.</p>
	 * @return Dkplus_Model_Abstract
	 */
	protected function _initAlias(){
		foreach($this->_alias AS $k => $v){
			unset($this->_alias[$k]);
			$this->_alias[strToLower($k)] = $v;
			$this->_unalias[strToLower($v)] = $k;
		}
	}
	
	/**
	 * @return array
	 */
	public function getAlias(){
		return $this->_alias;
	}
	
	/**
	 * @return array
	 */
	public function getUnalias(){
		return $this->_unalias;
	}
	
	/**
	 * <p>Prüft, ob ein Alias vorhanden ist.</p>
	 * @param string $alias
	 * @return boolean
	 * @see _transformAlias()
	 */
	protected function _hasAlias($alias){		
		return isset($this->_alias[strToLower((string) $alias)]);		
	}
	
	/**
	 * <p>Wandelt einen Alias um in seinen entsprechenen Spalten-Namen.</p>
	 * @param string $alias
	 * @return string
	 * @throws {@link Dkplus_Model_Exception} if the alias does not exist. 
	 * Wirft eine {@link Dkplus_Model_Exception} wenn der Alias nicht existiert.
	 * @uses _hasAlias()
	 * @see _transformArray()
	 */
	protected function _transformAlias($alias){
		$alias = strToLower((string) $alias);
		if(
			!$this->_hasAlias($alias)
		){
			throw new Dkplus_Model_Exception('Alias '.$alias.' does not exists.');
		}
		return $this->_alias[$alias];
	}
	
	/**
	 * <p>Wandelt alle Schlüssel des Arrays per Inflection um.</p>
	 * @param array $alias
	 * @return array
	 * @uses _hasAlias()
	 * @uses _transformAlias()
	 */
	protected function _transformArray(array $alias){
		$return = array();
		foreach($alias AS $k => $v){
			if(
				$this->_hasAlias($k)
			){
				unset($alias[$k]);
				$k = $this->_transformAlias($k);
			}
			$return[$k] = $v;
		}
		return $return;
	}
	
	/**
	 * <p>Enthält die Referenzen anderer Models.</p>
	 * @var array
	 * @see _hasReference()
	 */
	protected $_referenceMap = array();
	
	/**
	 * <p>Enthält die Abhängigkeiten zu anderen Models.</p>
	 * @var array
	 */
	protected $_dependencies = array();
	
	/**
	 * <p>Überprüft, ob eine Referenz auf eine bestimmte Model-Klasse vorhanden ist.</p>
	 *
	 * <p>Eine Referenz ist das Gegenstück zur dependency. Nur wenn eine Referenz 
	 * vorhanden ist, können von einem anderen Model aus Daten dieses Models 
	 * bezogen werden.</p> 
	 * @param string $modelClass
	 * @return boolean
	 */
	protected function _hasReference($modelClass){
		return isset($this->_referenceMap[(string) $modelClass]);
	}
	
	/**
	 * <p>Prüft, ob eine 1:n Verknüpfung von einem anderen Model zu diesem Model existiert.</p>
	 * @param string $modelClass
	 * @return boolean
	 */
	protected function _hasOneToManyReference($modelClass){
		if(
			!$this->_hasReference($modelClass)
		){
			//require-once 'Dkplus/Model/Exception';
			throw new Dkplus_Model_Exception('There is no reference to '.$modelClass.' defined.');
		}
		
		return is_string($this->_referenceMap[(string) $modelClass]);
	}
	
	/**
	 * <p>Prüft, ob eine n:m Relation von einem anderen Model zu diesem Model existiert.</p>
	 * @param string $modelClass
	 * @return boolean
	 */
	protected function _hasManyToManyReference($modelClass){
		if(
			!$this->_hasReference($modelClass)
		){
			//require-once 'Dkplus/Model/Exception';
			throw new Dkplus_Model_Exception('There is no reference to '.$modelClass.' defined.');
		}
		$modelClass = (string) $modelClass;
		return is_array($this->_referenceMap[$modelClass])
			&& isset($this->_referenceMap[$modelClass]['table'])
			&& is_string($this->_referenceMap[$modelClass]['table'])
			&& isset($this->_referenceMap[$modelClass]['table_col'])
			&& is_string($this->_referenceMap[$modelClass]['table_col'])
			&& isset($this->_referenceMap[$modelClass]['col'])
			&& is_string($this->_referenceMap[$modelClass]['col'])
			&& isset($this->_referenceMap[$modelClass]['model_col'])
			&& is_string($this->_referenceMap[$modelClass]['model_col']);
	}
	
	/**
	 * <p>Gibt die Beziehungstabelle einer n:m Relation zurück.</p>
	 * @param string $modelClass
	 * @return string
	 */
	protected function _getReferenceTable($modelClass){
		if(
			!$this->_hasManyToManyReference($modelClass)
		){
			//require-once 'Dkplus/Model/Exception';
			throw new Dkplus_Model_Exception('There is no reference-table to '.$modelClass.' defined.');
		}
		return $this->_referenceMap[(string) $modelClass]['table'];
	}
	
	
	/**
	 * <p>Gibt die Spalte der Beziehungstabelle einer n:m Relation zurück, die 
	 * auf die Tabelle des Models verweist.</p>
	 * @param string $modelClass
	 * @return string
	 */
	protected function _getReferenceTableCol($modelClass){
		if(
			!$this->_hasManyToManyReference($modelClass)
		){
			//require-once 'Dkplus/Model/Exception';
			throw new Dkplus_Model_Exception('There is no reference-table-col to '.$modelClass.' defined.');
		}
		
		return $this->_referenceMap[(string) $modelClass]['table_col'];
	}
	
	/**
	 * <p>Gibt die Spalte der Tabelle des Models zurück, die auf die 
	 * Beziehungstabelle einer n:m Relation verweist.</p>
	 * @param string $modelClass
	 * @return string
	 */
	protected function _getReferenceCol($modelClass){
		if(
			!$this->_hasManyToManyReference($modelClass)
		){
			//require-once 'Dkplus/Model/Exception';
			throw new Dkplus_Model_Exception('There is no reference-col to '.$modelClass.' defined.');
		}
		
		return $this->_referenceMap[(string) $modelClass]['col'];
	}
	
	/**
	 * <p>Gibt die Spalte der Beziehungstabelle eine n:m Beziehung zurück, nach
	 * der mit externen Daten gesucht werden soll.</p>
	 * @param string $modelClass
	 * @return string
	 */
	protected function _getReferenceModelCol($modelClass){
		if(
			!$this->_hasManyToManyReference($modelClass)
		){
			//require-once 'Dkplus/Model/Exception';
			throw new Dkplus_Model_Exception('There is no reference-model-column to '.$modelClass.' defined.');
		}
		
		return $this->_referenceMap[(string) $modelClass]['model_col'];
	}
	
	/**
	 * <p>Gibt die Spalte zurück, nach der bei einer 1:n Beziehung gesucht werden soll.</p>
	 * @param string $modelClass
	 * @return string
	 */
	protected function _getReference($modelClass){
		$modelClass = (string) $modelClass;		
		
		if(
			!$this->_hasOneToManyReference($modelClass)
		){
			//require-once 'Dkplus/Model/Exception';
			throw new Dkplus_Model_Exception('There is no 1:n reference to '.$modelClass.' defined.');
		}
		
		$col = $this->_referenceMap[$modelClass];
		if(
			$this->_hasAlias($col)
		){
			$col = $this->_transformAlias($col);
		}
		return $col;
	}
	
	/**
	 * @return array
	 */
	public function getDependencies(){
		return $this->_dependencies;
	}
	
	/**
	 * @see Dkplus/Model/Dkplus_Model_Interface#__get()
	 * @uses Dkplus_Model_Row_Db::offsetGet()
	 */
	public function __get($property){
		return $this->fetchInnerRow()->$property;
	}
	
	/**
	 * @see Dkplus/Model/Dkplus_Model_Interface#__set()
	 * @uses Dkplus_Model_Row_Db::offsetSet()
	 */
	public function __set($property, $value){
		$this->fetchInnerRow()->$property = $value;
		return $this;
	}
	
	/**
	 * @see Dkplus/Model/Dkplus_Model_Interface#__isset()
	 * @uses Dkplus_Model_Row_Db::offsetExists()
	 */
	public function __isset($property){
		return isset($this->fetchInnerRow()->$property);
	}
	
	/**
	 * <p>Alias of {@link __isset()}.</p>
	 * 
	 * <p>Alias für {@link __isset()}.</p>
	 * @see __isset()
	 * @uses Dkplus_Model_Row_Db::offsetExists()
	 */
	public function offsetExists($offset){
		return isset($this->fetchInnerRow()->$property);
	}
	
	/**
	 * <p>Alias of {@link __set()}.</p>
	 * 
	 * <p>Alias für {@link __set()}.</p>
	 * @see __set()
	 * @uses Dkplus_Model_Row_Db::offsetSet()
	 */
	public function offsetSet($offset, $value){
		$this->fetchInnerRow()->$offset = $value;
	}
	
	/**
	 * <p>Alias of {@link __get()}.</p>
	 * 
	 * <p>Alias für {@link __get()}.</p>
	 * @see __get()
	 * @uses Dkplus_Model_Row_Db::offsetGet()
	 */
	public function offsetGet($offset){
		return $this->_getInnerRow()->$offset;
	}
	
	/**
	 * <p>Deletes an property from the "inner row".</p>
	 * 
	 * <p>Löscht eine Eigenschaft aus dem "inneren Objekt".</p>
	 * @see __set()
	 * @uses Dkplus_Model_Row_Db::offsetUnset()
	 */
	public function offsetUnset($offset){
		$this->_getInnerRow()->offsetUnset($offset);
	}
	
	
	/**
	 * Implementiert eine Inflection. Für Näheres siehe {@link Dkplus_Model_Db_Abstract hier}.
	 * @see Dkplus/Model/Dkplus_Model_Interface#__call()
	 * @throws {@link Dkplus_Model_Exception} if there is no inner row or if the inner row
	 * does not implements the method.
	 * Wirft eine {@link Dkplus_Model_Exception} wenn kein "inneres Objekt" existiert oder
	 * das "inner Objekt" die Methode nicht implementiert.
	 * @uses Dkplus_Model_Criteria
	 */
	public function __call($method, array $arguments){
		$method = (string) $method;
		if(
			subStr($method, 0, 7) == 'fetchBy'
			&& $this->_hasAlias(subStr($method, 7))
		){
			if(
				count($arguments) != 1
			){
				/** 
				 * @see Dkplus_Model_Exception
 				*/
				//require-once 'Dkplus/Model/Exception.php';
				throw new Dkplus_Model_Exception('There must be exactly one argument for fetchByXyz-Methods.');
			}
			
			$crit = new Dkplus_Model_Criteria();
			$crit->andWhere($this->_transformAlias(subStr($method, 7)), $arguments[0]);
			return $this->fetchRowset($crit);
		}
		
		if(
			subStr($method, 0, 13) == 'fetchRowsetBy'
			&& $this->_hasAlias(subStr($method, 13))
		){
			if(
				count($arguments) != 1
			){
				/** 
				 * @see Dkplus_Model_Exception
 				*/
				//require-once 'Dkplus/Model/Exception.php';
				throw new Dkplus_Model_Exception('There must be exactly one argument for fetchRowsetByXyz-Methods.');
			}
			$crit = new Dkplus_Model_Criteria();
			$crit->andWhere($this->_transformAlias(subStr($method, 13)), $arguments[0]);
			return $this->fetchRowset($crit);
		}
		
		if(
			subStr($method, 0, 10) == 'fetchRowBy'
			&& $this->_hasAlias(subStr($method, 10))
		){
			if(
				count($arguments) != 1
			){
				/** 
				 * @see Dkplus_Model_Exception
 				*/
				//require-once 'Dkplus/Model/Exception.php';
				throw new Dkplus_Model_Exception('There must be exactly one argument for fetchRowByXyz-Methods.');
			}
			$crit = new Dkplus_Model_Criteria();
			$crit->andWhere($this->_transformAlias(subStr($method, 10)), $arguments[0]);
			return $this->fetchRow($crit);
		}
		
		if(
			!$this->_hasInnerRow()
		){
			/** 
			 * @see Dkplus_Model_Exception
 			*/
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('There is no inner row, method '.$method.' cannot be called.');
		}
		
		if(
			!method_exists($this->fetchInnerRow(), $method)
		){
			/** 
			 * @see Dkplus_Model_Exception
 			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('There is an inner row but it does not implements a method '.$method.'.');
		}
		
		return call_user_func_array(array($this->fetchInnerRow(), $method), $arguments);
	}
	
	/**
	 * <p>Sucht nach einem Datensatz und speichert es als "inneres Objekt".</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Abstract
	 * @uses fetchRow()
	 * @uses _setInnerRow()
	 */
	protected function _fetchToInner($crit = null){
		$row = $this->fetchRow($crit);
		$this->_setInnerRow($row);
		return $this;
	}
	
	/**
	 * <p>Setzt das "innere Objekt".</p>
	 * <p>Um es zu löschen, kann null (bzw. kein Parameter) übergeben werden.</p>
	 * @param Dkplus_Model_Row_Interface $row
	 * @return Dkplus_Model_Db_Abstract
	 */
	protected function _setInnerRow(Dkplus_Model_Row_Interface $row = null){
		$this->_getSessionNamespace()->row = $row;
		return $this;
	}
	
	/**
	 * <p>Gibt das "innere Objekt" zurück.</p>
	 * @return Dkplus_Model_Row_Interface
	 * @throws {@link Dkplus_Model_Exception} if there is no inner row. 
	 * Wirft eine {@link Dkplus_Model_Exception} wenn kein "inneres Objekt" vorhanden ist.
	 */
	public function fetchInnerRow(){
		if(
			!$this->_hasInnerRow()
		){
			/** 
			 * @see Dkplus_Model_Exception
 			*/
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('There are no inner row stored.');
		}		
		return $this->_getSessionNamespace()->row;
	}
	
	/**
	 * <p>Prüft, ob ein "inneres Objekt" existiert.</p>
	 * @return boolean
	 */
	protected function _hasInnerRow(){
		return isset($this->_getSessionNamespace()->row) && !is_null($this->_getSessionNamespace()->row);
	}
	
	/**
	 * @return string
	 */
	public function getRowClass(){
		return $this->_rowClass;
	}
}