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
 * @version    21.09.2009 23:55:10
 */

/**
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Dkplus_Model_Domain_Entity_EntityAbstract implements IEntity
{
	/**
	 * <p>Überprüft, ob Veränderungen gespeichert werden sollen.</p>
	 * @var boolean
	 */
	protected $__bIsChangeMarked = false;

	/**
	 * <p>Speichert die Schlüssel der geänderten Werte.</p>
	 * @var array
	 */
	protected $__aMarkedChanges = array();

	/**
	 * <p>Speichert die Schlüssel der abhängigen Entities.</p>
	 * @var array
	 */
	protected $__aChildEntities = array();

	/**
	 * <p>Speichert die Schlüssel der abhängigen EntityCollections.</p>
	 * @var array
	 */
	protected $__aChildEntityCollections = array();

	/**
	 * <p>Die Validatoren der einzelnen Eigenschaften.</p>
	 * @var array
	 */
	protected static $_aValidators = array();

	/**
	 * <p>Überprüft, ob ein Validator für eine Eigenschaft vorhanden ist.</p>
	 * @param	string	$sProperty
	 * @return	boolean
	 */
	protected static function _hasValidator($sProperty)
	{
		return isset(self::$_aValidators[(string) $sProperty]);
	}

	/**
	 * <p>Gibt einen Validator für eine Eigenschaft zurück.</p>
	 * @param string $sProperty
	 * @return Zend_Validate
	 * @throws {@link Dkplus_Model_Domain_Exception} on empty validator.
	 */
	protected static function _getValidator($sProperty)
	{
		$sProperty = (string) $sProperty;
		if(
			!self::_hasValidator($sProperty)
		){
			throw new Dkplus_Model_Domain_Exception(
				'There is no validator for property ' . $sProperty
			);
		}
		return self::$_aValidators[$sProperty];
	}

	/**
	 * <p>Setzt einen Validator für eine Eigenschaft.</p>
	 * @param string $sProperty
	 * @param Zend_Validate_Abstract $oValidator
	 * @return void
	 */
	public static function setValidator($sProperty, Zend_Validate_Abstract $oValidator)
	{
		self::$_aValidators[(string) $sProperty] = $oValidator;
	}

	/**
	 * <p>Gibt einen eindeutigen Identifier für dieses Domain-Objekt zurück.</p>
	 * @return string|int
	 */
	abstract public function getUniqueIdentifier();
	
    /**
     * Eigenschaften können dem Konstruktor übergeben werden.
     *
     * @param    array|Zend_Config    $aoOptions
     * @return    void
     */
    public function __construct($aoOptions = null)
    {
        if (!is_null($aoOptions)) {
            $this->setOptions($aoOptions);
        }
		$this->__bIsChangeMarked = true;
        $this->init();
    }

    /**
     * <p>Hook zum Erweitern. Wird nach dem Setzen der Werte aufgerufen.</p>
     *
     * @return    void
     */
    public function init()
    {
    }

    /**
     * <p>Setzt die Werte anhand eines Array oder eines Zend_Config
	 * Objektes.</p>
     *
     * @param    array|Zend_Config    $aoOptions
     * @return    Dkplus_Model_Domain_Abstract    Provides a fluent interface
     */
    public function setOptions($aoOptions)
    {
        if ($aoOptions instanceof Zend_Config) {
            $aoOptions = $aoOptions->toArray();
        }
        if (!is_array($aoOptions)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument! Array or Zend_Config required'
            ));
        }
        foreach ($aoOptions as $sName => $mValue) {
            if (substr($sName, 0, 1) != '_') {
                $sMethod = 'set' . ucfirst($sName);
                if (method_exists($this, $sMethod)) {
                    $this->$sMethod($mValue);
                }
				$this->_setProperty($sName, $mValue);
            }
        }
        return $this;
    }

	/**
	 * <p>Prüft, ob eine Eigenschaft existiert.</p>
	 * @param string $sName
	 * @return boolean
	 */
	public function  __isset($sName)
	{
		$sName = (string) $sName;
		return property_exists($this, $sName)
			|| property_exists($this, '_' . $sName)
			|| ( method_exists($this, 'set' . ucFirst($sName))
				&& method_exists($this, ucFirst($sName)) );

	}

	/**
	 * <p>Prüft, ob eine Eigenschaft existiert.</p>
	 * @param string $sOffset
	 * @return boolean
	 */
	public function  offsetExists($sOffset)
	{
		return $this->__isset($sOffset);
	}

	/**
	 * <p>Setzt die Eigenschaft und validiert sie, wenn ein Validator zugewiesen
	 * wurde, vorher.</p>
	 * @param string	$sName
	 * @param mixed		$mValue
	 * @return	Dkplus_Model_Domain_Abstract	Provides a fluent interface
	 */
	protected function _setProperty($sName, $mValue)
	{
		if ($this->_hasValidator($sName)
			&& !$this->_getValidator($sName)->isValid($mValue)) {
			throw new Dkplus_Model_Domain_Exception(
				'The property is not valid: '
				. implode(";\n", $this->_getValidator($sName)->getMessages())
			);
		}

		if (property_exists($this, $sName)) {
            $this->$sName = $mValue;
		} else if (property_exists($this, '_' . $sName)) {
            $this->{'_' . $sName} = $mValue;
        }

		//Wenn es sich bei dem gesetzen Objekt um eine Entity-Instanz handelt,
		//wird dies notiert.
		if ($mValue instanceOf Dkplus_Model_Domain_Entity_IEntity
			|| $mValue instanceOf Dkplus_Model_Domain_LazyLoad_ILazyLoad
		) {
			$this->__aChildEntities[] = $sName;
			$this->__aChildEntities = array_unique($this->__aChildEntities);
		//Wenn es sich bei dem gesetzen Objekt um eine Collection-Instanz handelt,
		//wird dies ebenfalls notiert.
		} else if ($mValue instanceOf Dkplus_Model_Domain_Collection_ICollection
					|| $mValue instanceOf Dkplus_Model_Domain_LazyLoad_ILazyLoad
		) {
			$this->__aChildEntityCollections[] = $sName;
			$this->__aChildEntityCollections
					= array_unique($this->__aChildEntityCollections);
		}

		//Wenn die Änderungen schon gespeichert werden sollen, tun wir dies nun:
		if ($this->__bIsChangeMarked) {
			$this->__aMarkedChanges[] = $sName;
		}
        return $this;
	}

    /**
     * <p>Überschreibt __set zur Nutzung als eine magische set-Methode.</p>
	 * <p>Wenn eine manuelle set()-Methode existiert, wird diese genutzt,
	 * ansonsten wird die entsprechende Eigenschaft gesetzt. Wenn weder die
	 * Eigenschaft noch die Funktion existieren, passiert nichts.</p>
     *
     * @param	string	$sName	Name der Eigenschaft
     * @param	mixed	$mValue  Wert der Eigenschaft
     * @return	Dkplus_Model_Domain_Abstract	Provides a fluent interface
     */
    public function __set($sName, $mValue)
    {
        if (substr($sName, 0, 1) == '_') {
			return $this;
		}
        $sMethod = 'set' . ucFirst($sName);
        if (method_exists($this, $sMethod)) {
            $this->$sMethod($mValue);
        }
		$this->_setProperty($sName, $mValue);
		return $this;
    }

	/**
	 * <p>Setzt eine Eigenschaft mittels ArrayAccess.</p>
	 * @param	string	$sOffset
	 * @param	mixed	$mValue
	 * @return	mixed
	 */
	public function offsetSet($sOffset, $mValue)
	{
		return $this->__set($sOffset, $mValue);
	}

	/**
	 * <p>Setzt eine Eigenschaft mittels ArrayAccess mit dem Wert null.</p>
	 * @param	string	$sOffset
	 * @return	mixed
	 */
	public function offsetUnset($sOffset)
	{
		return $this->__set($sOffset, null);
	}

	/**
	 * <p>Setzt die Eigenschaft und validiert sie, wenn ein Validator zugewiesen
	 * wurde, vorher.</p>
	 * <p>Handelt es sich bei der Eigenschaft um eine Instanz von
	 * {@link Dkplus_Model_Domain_LazyLoad_ILazyLoad} wird sie geladen.
	 * @param	string	$sName
	 * @return	mixed	Der Wert der gewünschten Eigenschaft oder null.
	 */
	protected function _getProperty($sName){
		if (property_exists($this, $sName)) {
			if ($this->$sName instanceOf Dkplus_Model_Domain_LazyLoad_ILazyLoad) {
				$this->$sName = $this->{$sName}->load();
			}
            return $this->{$sName};
        } else if (property_exists($this, '_' . $sName)) {
			if ($this->$sName instanceOf Dkplus_Model_Domain_LazyLoad_ILazyLoad) {
				$this->$sName = $this->{$sName}->load();
			}
			return $this->{'_' . $sName};
		}
		return null;
	}

    /**
	 * <p>Überschreibt __get zur Nutzung als eine magische get-Methode.</p>
     * <p>Wenn eine manuelle get()-Methode existiert, wird diese genutzt,
	 * ansonsten wird die entsprechende Eigenschaft gesetzt. Wenn weder die
	 * Eigenschaft noch die Funktion existieren, wird null zurückgegeben.</p>
     *
     * @param	string	$sName	Name der Eigenschaft
     * @return	mixed	Gibt den Wert der Eigenschaft oder null zurück.
     */
    public function __get($sName)
    {
        if (substr($sName, 0, 1) == '_') return $this;
        $sMethod = 'get' . ucFirst($sName);
        if (method_exists($this, $sMethod)) {
            return $this->$sMethod();
        }
        return $this->_getProperty($sProperty);
    }

	/**
	 * <p>Gibt eine Eigenschaft mittels ArrayAccess zurück.</p>
	 * @param	string	$sOffset
	 * @return	mixed
	 */
	public function offsetGet($sOffset)
	{
		return $this->__get($sOffset);
	}

    /**
     * Override __call to fetch non-existing get/set-methods and redirect to
     * __set or __get instead.
     *
     * @param    string    $sname    Name of the method called
     * @param    array    $aArguments    Arguments provided with call
     * @return    mixed    Returns either the result of __set or __get or null
     */
    public function __call($sName, $aArguments)
    {
        $sPrefix = strToLower( subStr($sName, 0, 3) );
        if ($prefix == 'set') {
            $sProperty = lcFirst( subStr($sName, 3) );
            return $this->_setProperty($sProperty, $arguments[0]);
        } else if ($prefix == 'get') {
            $sProperty = lcFirst(substr($sName, 3));
            return $this->_getProperty($sProperty);
        }
        return null;
    }

	/**
	 * <p>Gibt die geänderte Werte zurück.</p>
	 * @return array
	 */
	public function getChangedValues()
	{
		$aReturn = array();
		foreach ($this->__aMarkedChanges AS $sChange)
		{
			$aReturn[$sChange] = $this->__get($sChange);
		}
		return $aReturn;
	}

	/**
	 * <p>Prüft, ob bei dem Entity irgendwelche Änderungen vorgenommen wurden.</p>
	 * @return boolean
	 */
	public function isDirty()
	{
		if (count($this->__aMarkedChanges) > 0) {
			return true;
		}

		foreach($this->__aChildEntities AS $oEntity){
			if ($oEntity->isDirty()) {
				return true;
			}
		}

		foreach($this->__aChildEntityCollections AS $oEntityCollection){
			if ($oEntityCollection->isDirty()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * <p>Prüft, ob das Entity-Objekt mit dem Entity-Objekt identisch ist.</p>
	 * @param Dkplus_Model_Domain_Entity_IEntity $oEntity
	 * @return boolean
	 */
	public function equals(Dkplus_Model_Domain_Entity_IEntity $oEntity)
	{
		return $oEntity === $this;
	}


	/**
	 * <p>Lässt das Entity alle gemerkten Änderungen vergessen.</p>
	 * @return Dkplus_Model_Domain_Entity_IEntity
	 */
	public function forgetChanges()
	{
		$this->__aMarkedChanges = array();
		return $this;
	}
}

/*abstract class Dkplus_Model_Domain_Abstract{
	/**
	 * Die Eigenschaften des
	 * @var array
	 */
	/*protected $_properties = array();
	protected $_refilteredProperties = array();

	protected $_addedProperties = array();
	
	protected static $_validators = array();
	protected static $_filter = array();
	protected static $_refilter = array();

	/**
	 * <p>Überprüft, ob ein Validator für eine Eigenschaft vorhanden ist.</p>
	 * @param string $sProperty
	 * @return boolean
	 */
	/*protected static function _hasValidator($sProperty){
		return isset(self::$_validators[(string) $sProperty]);
	}

	/**
	 * <p>Gibt einen Validator für eine Eigenschaft zurück.</p>
	 * @param string $sProperty
	 * @return Zend_Validate
	 * @throws {@link Dkplus_Model_Domain_Exception} on empty validator.
	 */
	/*protected static function _getValidator($sProperty){
		$sProperty = (string) $sProperty;
		if(
			!self::_hasValidator($sProperty)
		){
			throw new Dkplus_Model_Domain_Exception(
				'There is no validator for property ' . $sProperty
			);
		}
		return self::$_validators[$sProperty];
	}

	/**
	 * <p>Überprüft, ob ein Filter für eine Eigenschaft vorhanden ist.</p>
	 * @param string $sProperty
	 * @return boolean
	 */
	/*protected static function _hasFilter($sProperty){
		$sProperty = (string) $sProperty;
		return isset(self::$_filter[$sProperty]);
	}

	/**
	 *
	 * @param string $sProperty
	 * @return Zend_Filter
	 * @throws {@link Dkplus_Model_Domain_Exception} on empty filter.
	 */
	/*protected static function _getFilter($sProperty){
		$sProperty = (string) $sProperty;
		if(
			empty(self::$_filter[$sProperty])
		){
			throw new Dkplus_Model_Domain_Exception(
				'There is no filter for property ' . $sProperty
			);
		}
		return  self::$_filter[$sProperty];
	}

	/**
	 *
	 * @param string $sProperty
	 * @return boolean
	 */
	/*protected static function _hasRefilter($sProperty){
		$sProperty = (string) $sProperty;
		return isset(self::$_refilter[$sProperty]);
	}

	/**
	 *
	 * @param string $sProperty
	 * @return Zend_Filter
	 * @throws {@link Dkplus_Model_Domain_Exception} on empty refilter.
	 */
	/*protected static function _getRefilter($sProperty){
		$sProperty = (string) $sProperty;
		if(
			empty(self::$_refilter[$sProperty])
		){
			throw new Dkplus_Model_Domain_Exception(
				'There is no refilter for property ' . $sProperty
			);
		}
		return  self::$_refilter[$sProperty];
	}

    public function __construct(array $data = array()/*, $filter = false){
		$this->_init();
		foreach($data AS $sProperty => $mValue){
			$this->set($sProperty, $mValue, $filter);
		}
	}

	protected function _init(){}

	protected static function _addValidator($sProperty, Zend_Validate_Interface $oValidator){
		$sProperty = (string) $sProperty;
		if(
			empty(self::$_validators[$sProperty])
		){
			self::$_validators[$sProperty] = new Zend_Validate();
		}
		self::$_validators[$sProperty]->addValidator($oValidator);
		return $this;
	}

	protected static function _addFilter($sProperty, Zend_Filter_Interface $filter){
		$sProperty = (string) $sProperty;
		if(
			!self::_hasFilter($sProperty)
		){
			self::$_filter[$sProperty] = new Zend_Filter();
		}
		self::_getFilter($sProperty)->addFilter($filter);
	}

	protected static function _addRefilter($sProperty, Zend_Filter_Interface $filter){
		$sProperty = (string) $sProperty;
		if(
			!self::_hasRefilter($sProperty)
		){
			self::$_refilter[$sProperty] = new Zend_Filter();
		}
		self::_getRefilter($sProperty)->addFilter($filter);
	}

	public function set($sName, $mValue, $filter = false){
		$sName = (string) $sName;
		if(
			method_exists($this, 'set' . $sName)
		){
			return call_user_method('set' . $sName, $this, array($refilter));
		}
		
		if(
			$filter
			&& self::_hasFilter($sName)
		){
			$mValue = self::_getFilter($sName)->filter($mValue);
		}
		
		$this->_properties[$sName] = $mValue;
		return $this;
	}

	public function get($sName, $refilter = false){
		$sName = (string) $sName;
		if(
			method_exists($this, 'get' . $sName)
		){
			return call_user_method('get' . $sName, $this, array($filter));
		}

		if(
			!isset($this->_properties[$sName])
		){
			throw new Dkplus_Model_Domain_Exception('There is no property ' . $sName);
		}

		return (
				$refilter
				&& $this->_hasRefilter($sName)
			)
			? $this->_getRefilter($sName)->filter($this->_properties[$sName])
			: $this->_properties[$sName];
	}

	public function __set($sName, $mValue){
		return $this->set($sName, $mValue);
	}

	public function __get($sName){
		return $this->get($sName);
	}

	public function toArray($refilter = false){
		$data = array();
		foreach($this->_properties AS $sName => $mValue){
			$data[$sName] = $this->get($sName, $refilter);
		}
		foreach($this->_addedProperties AS $sProperty){
			$data[$sProperty] = $this->get($sProperty, $refilter);
		}
		return $data;
	}
}