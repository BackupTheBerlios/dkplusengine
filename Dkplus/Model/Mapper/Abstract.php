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
 * @see Dkplus_Model_Criteria
 */
//require-once 'Dkplus/Model/Criteria.php';

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
abstract class Dkplus_Model_Mapper_Abstract {//implements Dkplus_Model_Mapper_Interface{
	/**
	 * <p>Die zu nutzende Klasse für Domain-Objekte.</p>
	 * @var string
	 */
	protected $_domainClass = '';
	
	/**
	 * <p>Die zu nutzende Klasse für Domainset-Objekte.</p>
	 * @var string
	 */
	protected $_domainContainerClass = 'SplObjectStorage';
	
	/**
	 * <p>Alias-Liste für die Inflection.</p>
	 * @var array
	 */
	protected $_alias = array();
	
	/**
	 * <p>Alias-Liste für die Inflection in die andere Richtung.</p>
	 * <p>Wird automatisch generiert.</p>
	 * @var array
	 */
	protected $_unalias = array();
	
	/**
	 * Initialisiert die Alias.
	 * @uses _initAlias()
	 */
	public function __construct()
	{
		$this->_initAlias();
		$this->_init();
	}

	/**
	 * <p>Initialisierungsmethode</p>
	 * @return void
	 */
	protected function _init()
	{
	}
	
	/**
	 * <p>Hook, normiert die Alias-Werte.</p>
	 * @return Dkplus_Model_Abstract
	 */
	protected function _initAlias()
	{
		foreach ($this->_alias AS $k => $v)
		{
			unset($this->_alias[$k]);
			$this->_alias[strToLower($k)] = $v;
			$this->_unalias[strToLower($v)] = $k;
		}
	}
	
	/**
	 * <p>Prüft, ob ein Alias vorhanden ist.</p>
	 * @param string $alias
	 * @return boolean
	 * @see _transformAlias()
	 */
	protected function _hasAlias($alias)
	{
		return isset($this->_alias[strToLower($alias)]);		
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
	protected function _transformAlias($alias)
	{
		$alias = strToLower($alias);
		if (!$this->_hasAlias($alias))
		{
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
	protected function _transformArray(array $alias)
	{
		$return = array();
		foreach ($alias AS $k => $v)
		{
			if ($this->_hasAlias($k))
			{
				unset($alias[$k]);
				$k = $this->_transformAlias($k);
			}
			$return[$k] = $v;
		}
		return $return;
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
	public function __call($method, array $arguments)
	{
		$method = (string) $method;
		if (subStr($method, 0, 7) == 'fetchBy'
			&& $this->_hasAlias(subStr($method, 7)))
		{
			if (count($arguments) != 1)
			{
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
		
		if (subStr($method, 0, 13) == 'fetchRowsetBy'
			&& $this->_hasAlias(subStr($method, 13)))
		{
			if (count($arguments) != 1)
			{
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
		
		if (subStr($method, 0, 10) == 'fetchRowBy'
			&& $this->_hasAlias(subStr($method, 10)))
		{
			if(count($arguments) != 1)
			{
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
		
		throw new Dkplus_Model_Mapper_Exception('Method ' . $method
			. ' does not exist and was not trabbed.');
	}

	protected function _createDomainObject(array $data = array())
	{
		if(count($data) > 0)
		{
			$data = $this->_transformArray($data);
		}
		return new $this->_domainClass($data);
	}
}