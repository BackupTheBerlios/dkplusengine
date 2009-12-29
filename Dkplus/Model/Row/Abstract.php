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
 * @version    07.04.2009 21:38:49
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Dkplus_Model_Row_Interface
 */
//require-once 'Dkplus/Model/Row/Interface.php';

/**
 * @see Dkplus_Model_Criteria
 */
//require-once 'Dkplus/Model/Criteria.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Dkplus_Model_Row_Abstract implements Dkplus_Model_Row_Interface{
	/**
	 * Die zu verwendende Factory-Class
	 * @var string
	 */
	protected static $_factoryClass = 'Dkplus_Model_Factory';

	/**
	 * Setzt die für Models zu verwendende Factory-Class.
	 * @param string $className
	 */
	public static function setFactoryClass($className){
		if(
			!class_exists($className)
		){
			throw new Dkplus_Model_Exception($className.' is no valid class');
		}
		self::$_factoryClass = $className;
	}

	/**
	 * Liefert das gewünschte Model.
	 * @param string $model
	 * @return Dkplus_Model_Abstract
	 */
	protected function _getOtherModel($model){
		return call_user_func_array(array(self::$_factoryClass, 'get'), array($model));
	}

	/**
	 * @param string $property
	 * @return boolean
	 */
	abstract protected function _issetData($property);
	
	/**
	 * <p>Gibt eine Eigenschaft des Objektes zurück.</p>
	 * <p>Alle notwendigen Prüfungen wurden vorher bereits durchgeführt.</p>
	 * @param string $property
	 * @return mixed
	 */
	abstract protected function _getData($property);
	
	/**
	 * <p>Setzt die Daten der Eigenschaft.</p>
	 * <p>Alle Prüfungen und Änderungen wurden vorher bereits erledigt.</p>
	 * @param string $property
	 * @param mixed $value
	 * @return Dkplus_Model_Row_Interface
	 */
	abstract protected function _setData($property, $value);
	
	/**
	 * <p>Wandelt die Row in einen assoziativen Array um.</p>
	 * @return array
	 */
	abstract protected function _toArray();
	
	/**
	 * <p>Löscht die Daten aus der Datenquelle.</p>
	 * @return Dkplus_Model_Row_Abstract
	 */
	abstract protected function _delete();
	
	/**
	 * <p>Speichert die Daten in der Datenquelle.</p>
	 * <p>Die Überprüfung auf ReadOnly und vorheriges Speichern muss nicht 
	 * implementiert werden. Ebenso wird das Setzen der {@link _isSaved} 
	 * Eigenschaft schon implementiert durch die {@link save()} Methode.
	 * @return Dkplus_Model_Row_Abstract
	 */
	abstract protected function _save();
	
	/**
	 * @var array
	 * @see Dkplus_Model_Db_Abstract::$_alias
	 */	
	protected $_alias = array();
	
	/**
	 * @var array
	 * @see Dkplus_Model_Db_Abstract::$_unalias
	 */	
	protected $_unalias = array();
	
	public function __construct(Dkplus_Model_Interface $model, $row = null, $new = FALSE){
		$this->_model = $model;
		
		if(
			is_null($row)
		){
			$new = TRUE;
		}
		else{
			$new = (boolean) $new;
			$this->_setRow($row);
		}
		if(
			$new
		){
			$this->_setUnsaved();
		}
		else{
			$this->_setSaved();
		}
		
		if(
			$model instanceOf Dkplus_Model_Abstract
		){
			$this->_alias = $model->getAlias();
			$this->_unalias = $model->getUnalias();
		}
	}

	public function  __call($name, $arguments){
		if(
			subStr($name, 0, 14) == 'fetchDependent'
		){
			if(
				isset($arguments[0])
			){
				return $this->fetchDependentRowset(subStr($name, 14), $arguments[0]);
			}
			return $this->fetchDependentRowset(subStr($name, 14));
		}

		if(
			subStr($name, 0, 11) == 'fetchParent'
		){
			return $this->fetchParentRow(subStr($name, 11));
		}

		/**
		 * @see Dkplus_Model_Exception
		 */
		//require-once 'Dkplus/Model/Exception.php';
		throw new Dkplus_Model_Exception('Method '.$name.' does not exists in '.get_class($this).'.');
	}
	
	/**
	 * 
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
			throw new Dkplus_Model_Exception('Model has been removed from rows.');
		}
		return $this->_model;
	}
	
	/**
	 * @var mixed
	 */
	protected $_data = null;
	
	/**
	 * @var boolean
	 */
	protected $_readOnly = false;
	
	/**
	 * @var boolean
	 */
	protected $_saved = false;
	
	
	/**
	 * @return boolean
	 */
	public function isSaved(){
		return $this->_saved;
	}
	
	/**
	 * <p>Markiert das Objekt als gespeichert.</p>
	 * @return Dkplus_Model_Row_Abstract
	 */
	protected function _setSaved(){
		$this->_saved = TRUE;
		return $this;
	}
	
	/**
	 * <p>Markiert das Objekt als nicht gespeichert.</p>
	 * @return Dkplus_Model_Row_Abstract
	 */
	protected function _setUnsaved(){
		$this->_saved = FALSE;
		return $this;
	}
	
	/**
	 * @param string $property
	 * @return scalar
	 * @uses get()
	 */
	public function __get($property){
		return $this->get($property);
	}
	
	/**
	 * <p>Gibt eine Eigenschaft des Objektes zurück.</p>
	 * @param string $property
	 * @return scalar
	 * @throws Dkplus_Model_Exception if the property does not exist.
	 */
	public function get($property){
		$property = (string) $property;
		
		if(
			!$this->__isset($property)
		){
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('There is no property '.$property);
		}
		
		if(
			$this->_hasAlias($property)
		){
			$property = $this->_transformAlias($property);
		}
		return $this->_getData($property);
	}
	
	/**
	 * @param string $property
	 * @param scalar $value
	 * @return Dkplus_Model_Row_Abstract
	 * @throws Dkplus_Model_Exception if the property does not exist 
	 * or the row is read-only.
	 */
	public function set($property, $value){
		if(
			$this->isReadOnly()
		){
			throw new Dkplus_Model_Exception('An read-only row cannot be changed.');
		}

		$property = (string) $property;
		
		if(
			!$this->__isset($property)
		){
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('There is no property '.$property);
		}
		
		$this->_setUnsaved();
		
		if(
			$this->_hasAlias($property)
		){
			$property = $this->_transformAlias($property);
		}
		
		return $this->_setData($property, $value);
	}
	
	/**
	 * @param string $property
	 * @return boolean
	 */
	public function __isset($property){
		$property = (string) $property;
		//Zend_Debug::dump($this->_hasAlias($property), $property);
		if(
			$this->_hasAlias($property)
		){
			$property = $this->_transformAlias($property);
		}
		return $this->_issetData($property);
	}

	/**
	 * @param string $property
	 * @param scalar $value
	 * @return Dkplus_Model_Row_Abstract
	 * @uses set()
	 */
	public function __set($property, $value){
		return $this->set($property, $value);
	}
	
	/**
	 * @param string $offset
	 * @return boolean
	 * @uses __isset()
	 */
	public function offsetExists($offset){
		return $this->__isset($offset);
	}
	
	/**
	 * @param string $offset
	 * @return scalar	 
	 * @uses get()
	 */
	public function offsetGet($offset){
		return $this->get($offset);
	}
	
	/**
	 * @param string $offset
	 * @param scalar $value
	 * @return Dkplus_Model_Row_Abstract
	 * @uses set()
	 */
	public function offsetSet($offset, $value){
		return $this->set($offset, $value);
	}
	
	/**
	 * @param string $offset
	 * @throws Dkplus_Model_Exception every time.
	 */
	public function offsetUnset($offset){
		/**
		 * @see Dkplus_Model_Exception
		 */
		//require-once 'Dkplus/Model/Exception.php';
		throw new Dkplus_Model_Exception('You cannot unset an offset of an row.');	
	}
	

	
	/**
	 * @param array|object $row
	 * @return Dkplus_Model_Row_Interface
	 * @throws Dkplus_Model_Exception if the parameter is not an array or an object.
	 */
	protected function _setRow($row){
		if(
			is_array($row)
		){
			$this->_data = $row;
			$this->setReadOnly();
		}
		elseif(
			is_object($row)			
		){
			$this->_data = $row;
		}
		else{
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('Parameter must be an array or an object.');
		}
		
		return $this;
	}
	
	/**
	 * @uses _setRowDataByArray()
	 * @uses isReadOnly()
	 * @throws {@link Dkplus_Model_Exception} if the row is read-only. Wirft
	 * eine {Dkplus_Model_Exception} wenn die Row Read-Only ist.
	 */
	public function setFromArray(array $data){
		if(
			$this->isReadOnly()
		){
			throw new Dkplus_Model_Exception('Data cannot be set because the row is read-only.');
		}
		
		return $this->_setRowDataByArray($this->_transformArray($data));
	}
	
	/**
	 * @param array $data
	 * @return Dkplus_Model_Row_interface
	 */
	protected function _setRowDataByArray(array $data){
		foreach($data AS $property => $value){
			if(
				$this->__isset($property)
			){
				$this->__set($property, $value);
			}
		}	
		return $this;
	}
	
	
	
	/**
	 * @see Model/Row/Dkplus_Model_Row_Interface#save()
	 * @throws Dkplus_Model_Exception if data has not been stored before or the row is read-only.
	 */
	public function save(){
		if(
			$this->isSaved()
		){
			throw new Dkplus_Model_Exception('Row has been already saved.');
		}
		
		if(
			$this->isReadOnly()
		){
			throw new Dkplus_Model_Exception('Row is read-only.');
		}
		$this->_save();
		$this->_setSaved();
		return $this;
	}
	

	
	/**
	 * @throws Dkplus_Model_Exception if data has not been stored before or the row is read-only.
	 * @see Model/Row/Dkplus_Model_Row_Interface#delete()
	 */
	public function delete(){
		if(
			!$this->isSaved()
		){
			throw new Dkplus_Model_Exception('Data must be saved before deleting them');
		}
		
		if(
			$this->isReadOnly()
		){			
			throw new Dkplus_Model_Exception('Row is read-only.');
		}
		$this->_delete();	
		return $this;
	}	
	
	/**
	 * @return Dkplus_Model_Row_Interface
	 * @see Model/Row/Dkplus_Model_Row_Interface#setReadOnly()
	 */
	public function setReadOnly(){
		$this->_readOnly = true;
		return $this;
	}
	
	/**
	 * @return boolean
	 * @see Model/Row/Dkplus_Model_Row_Interface#isReadOnly()
	 */
	public function isReadOnly(){
		return $this->_readOnly;
	}
	
	/**
	 * @return array
	 * @see Model/Row/Dkplus_Model_Row_Interface#toArray()
	 * @throws Dkplus_Model_Exception if the data has not been stored before or there is no array method available.
	 */
	public function toArray(){		
		return $this->_untransformArray($this->_toArray());
	}
	
	/**
	 * <p>Prüft, ob ein Alias vorhanden ist.</p>
	 * @param string $alias
	 * @return boolean
	 * @see _transformAlias()
	 * @see _hasUnalias()
	 */
	protected function _hasAlias($alias){		
		return isset($this->_alias[strToLower((string) $alias)]);		
	}
	
	/**
	 * <p>Prüft, ob ein Alias für eine Spalte vorhanden ist.</p>
	 * @param string $unalias
	 * @return boolean
	 * @see _transformUnalias()
	 * @see _hasAlias()
	 */
	protected function _hasUnalias($unalias){		
		return isset($this->_unalias[strToLower((string) $unalias)]);		
	}
	
	/**
	 * <p>Wandelt einen Alias um in seinen entsprechenen Spalten-Namen.</p>
	 * @param string $alias
	 * @return string
	 * @throws {@link Dkplus_Model_Exception} if the alias does not exist. 
	 * Wirft eine {@link Dkplus_Model_Exception} wenn der Alias nicht existiert.
	 * @see _hasAlias()
	 * @see _transformUnalias()
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
	 * <p>Wandelt einen Spalten-Namen um in seinen entsprechenen Alias.</p>
	 * @param string $unalias
	 * @return string
	 * @throws {@link Dkplus_Model_Exception} if there is no alias. 
	 * Wirft eine {@link Dkplus_Model_Exception} wenn kein Alias existiert.
	 * @see _hasUnalias()
	 * @see _transformAlias()
	 */
	protected function _transformUnalias($unalias){
		$unalias = strToLower((string) $unalias);
		if(
			!$this->_hasUnalias($unalias)
		){
			throw new Dkplus_Model_Exception('Unalias '.$unalias.' does not exists.');
		}
		return $this->_unalias[$unalias];
	}
	
	/**
	 * <p>Wandelt alle Schlüssel des Arrays per Inflection um.</p>
	 * @param array $alias
	 * @return array
	 * @uses _hasAlias()
	 * @uses _transformAlias()
	 * @see _untransformArray()
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
	 * <p>Wandelt alle Schlüssel des Arrays per Inflection umgekehrt um.</p>
	 * @param array $unalias
	 * @return array
	 * @uses _hasUnalias()
	 * @uses _transformUnalias()
	 * @see _transformArray()
	 */
	protected function _untransformArray(array $unalias){
		$return = array();
		foreach($unalias AS $k => $v){
			if(
				$this->_hasUnalias($k)
			){
				unset($unalias[$k]);
				$k = $this->_transformUnalias($k);
			}
			$return[$k] = $v;
		}
		return $return;
	}
	
	/**
	 * <p>Gibt eine Abhängigkeit zurück und prüft sie auf  Validität.</p>
	 * @param string $type
	 * @return array
	 */
	protected function _getDependency($type){
		$type = (string) $type;
		$dependencies = $this->_getModel()->getDependencies();
		if(
			!isset($dependencies[$type])
			|| !isset($dependencies[$type]['model'])
			|| !is_string($dependencies[$type]['model'])
			|| !isset($dependencies[$type]['col'])
			|| !is_string($dependencies[$type]['col'])
		){
			throw new Dkplus_Model_Exception('There is no dependent with name '.$type);
		}
		return $dependencies[$type];
	}
	
	/**
	 * @param string $type
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function fetchDependentRowset($type, Dkplus_Model_Criteria_Interface $crit = null){
		$dependency = $this->_getDependency($type);
		$model = $this->_getOtherModel($dependency['model']);
		return $model->fetchDependent($this->_getModel(), $this->get($dependency['col']), $crit);
	}
	
	/**
	 * 
	 * @param string $type
	 * @return Dkplus_Model_Row_Interface
	 */
	public function fetchParentRow($type){
		$dependency = $this->_getDependency($type);
		$model = $this->_getOtherModel($dependency['model']);
		return $model->fetchDependent($this->_getModel(), $this->get($dependency['col']))->current();
	}
}