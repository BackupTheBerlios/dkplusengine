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
 * @version    23.12.2009 01:59:29
 */

/**
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Domain_Collection_CollectionAbstract 
	implements Dkplus_Model_Domain_Collection_ICollection
{
	/**
	 * <p>Fügt ein Array von Entities hinzu.</p>
	 * @param array $aEntities
	 */
	protected function  __construct(array $aEntities = array())
	{
		foreach($aEntites AS $oEntity){
			$this->_addEntity($oEntity);
		}
		$this->_init();
		$this->_bIsChangeMarked = true;
	}

	/**
	 * <p>Hook.</p>
	 * @return void
	 */
	protected function _init()
	{
	}

	/**
	 * <p>Überprüft, ob Veränderungen gespeichert werden sollen.</p>
	 * @var boolean
	 */
	protected $_bIsChangeMarked = false;

	/**
	 * <p>Die Entities</p>
	 * @var array
	 */
    protected $_aEntities = array();

	/**
	 * <p>Die hinzugefügten Entities.</p>
	 * @var array
	 */
	protected $_aAddedEntities = array();

	/**
	 * <p>Die entfernten Entities.</p>
	 * @var array
	 */
	protected $_aRemovedEntities = array();

	/**
	 * <p>Der aktuelle Index für foreach-Schleifen u.A.</p>
	 * @var int
	 */
	protected $_iPosition = 0;

	/**
	 * <p>Liefert die Anzahl der enthaltenen Entities zurück.</p>
	 * @return int
	 */
	public function count()
	{
		return count($this->_aEntities);
	}


	/**
	 *
	 * @return Dkplus_Model_Domain_Entity_IEntity
	 */
	public function current()
	{
		if ($this->valid()) {
			return $this->_aEntities[$this->_iPosition];
		}
		return null;
	}

	/**
	 *
	 * @return Dkplus_Model_Domain_Collection_CollectionAbstract
	 */
	public function next()
	{
		++$this->_iPosition;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->_iPosition;
	}

	/**
	 *
	 * @return boolean
	 */
	public function valid()
	{
		if(
			$this->_iPosition >= $this->count()
		){
			$this->rewind();
			return false;
		}
		return true;
	}

	/**
	 *
	 * @return Dkplus_Model_Domain_Collection_CollectionAbstract
	 */
	public function rewind()
	{
		$this->_iPosition = 0;
		return $this;
	}

	/**
	 * @param int $iOffset
	 * @return boolean
	 */
	public function offsetExists($iOffset)
	{
		//Ist der gegebene Index korrekt?
		if (!is_int($iOffset)) {
			throw new Dkplus_Model_Exception(
				sprintf('First Parameter has an invalid type "%s", must be an integer.',
    				getType($iOffset)));
		}
		return isset($this->_aEntities[$iOffset]);
	}

	/**
	 *
	 * @param int $offset
	 * @return Dkplus_Model_Domain_Entity_IEntity
	 */
	public function offsetGet($iOffset)
	{
		//Existiert das Entity?
		if (!$this->offsetExists($iOffset)) {
			throw new Dkplus_Model_Exception('There is no element with an offset '.$iOffset);
		}
		return $this->_aEntities[$iOffset];
	}

	/**
	 * @param mixed $iOffset
	 * @param mixed $oValue
	 * @return void
	 */
	public function offsetSet($iOffset, $oValue)
	{
		//Löschen des alten Entities
		if ($this->offsetExists($iOffset)) {
			$this->offsetUnset($iOffset);
		}
		//Prüfen, ob es sich wirklich um ein Entity handelt.
		if (!($oValue instanceOf Dkplus_Model_Domain_Entity_IEntity)) {
			throw new Dkplus_Model_Exception(
				sprintf('First Parameter has an invalid type "%s", must be an '
					. 'instance of Dkplus_Model_Domain_Entity_IEntity.',
    				getType($iOffset)));
		}
		//Hinzufügen des neuen Entities
		$this->_addEntity($oValue, $iOffset);
	}

	/**
	 * @param int $iOffset
	 * @return Dkplus_Model_Domain_Collection_CollectionAbstract
	 */
	public function offsetUnset($iOffset)
	{
		$this->_removeEntity($iOffset);
		return $this;
	}

	/**
	 * <p>Fügt einen Entity zur Collection hinzu.</p>
	 * @param Dkplus_Model_Domain_Entity_IEntity $oEntity
	 * @param int $iOffset
	 * @return Dkplus_Model_Domain_Collection_CollectionAbstract
	 */
	protected function _addEntity(Dkplus_Model_Domain_Entity_IEntity $oEntity,
			$iOffset = null
	) {
		//Prüfen, ob das Entity schon in der Collection enthalten ist.
		foreach($this->_aEntities AS $oEntity){
			if ($oEntity->equals($oValue)) {
				throw new Dkplus_Model_Exception('Entity is already in the collection.');
			}
		}
		//Key bestimmen
		$iOffset = is_null($iOffset)
			? $this->count()
			: (int) $iOffset;

		//Entity hinzufügen
		$this->_aEntities[$iOffset] = $oValue;

		//Änderungen notieren
		if ($this->_bIsChangeMarked) {
			$this->_aAddedEntities[] = $oValue;
		}
		return $this;
	}

	/**
	 * <p>Entfernt ein Entity aus der Collection.</p>
	 * @param Dkplus_Model_Domain_Entity_IEntity|int $mEntity
	 * @return Dkplus_Model_Domain_Collection_CollectionAbstract
	 */
	protected function _removeEntity($mEntity)
	{
		//Suchen des Indexes, wenn eine Instance gegeben wurde.
		if ($mEntity instanceOf Dkplus_Model_Domain_Entity_IEntity) {
			for($i = 0; $i < count($this->_aEntities); ++$i){
				if ($mEntity->equals($this->_aEntities[$i])){
					$mEntity = $i;
					break;
				}
			}
		}

		//Index nicht gefunden oder kein Integer übergeben.
		if (!is_int($mEntity)){
			throw new Dkplus_Model_Exception(
				sprintf('First parameter has an invalid type "%s", must be an '
						. 'integer or an instance of '
						. 'Dkplus_Model_Domain_Entity_IEntity that is already '
						. 'in the collection.',
    				getType($iOffset)
				)
			);
		}

		//Falscher Index übergeben.
		if (!isset($this->_aEntities[$mEntity])) {
			throw new Dkplus_Model_Exception('There is no Entity with offset '
					. $mEntity . '.');
		}

		//Soll die Entfernung gemeldet werden?
		if ($this->_bIsChangeMarked) {
			$this->_aRemovedEntities[] = $this->_aEntities[$mEntity];
		}

		//Entfernen und neu-ordnen.
		unset($this->_aEntities[$mEntity]);
		$this->_aEntities = array_values($this->_aEntities);
		return $this;
	}

	/**
	 * <p>Gibt alle Entities zurück, die hinzugefügt wurden.</p>
	 * @return array
	 */
	public function getAddedEntities()
	{
		//Einzigartig machen der hinzugefügten Entities.
		$this->_aAddedEntities = array_unique($this->_aAddedEntities);

		//Prüfung, ob jedes hinzugefügte Entity auch wirklich noch hinzugefügt ist
		foreach($this->_aAddedEntities AS $iKey => $oEntity){
			$bIsAdded = false;
			foreach($this->_aEntities AS $oEqEntity){
				if ($oEntity->equals($oEqEntity)) {
					$bIsAdded = true;
					break;
				}
			}
			if (!$bIsAdded) {
				unset($this->_aAddedEntities[$iKey]);
			}
		}

		//Array schön machen
		$this->_aAddedEntities = array_value($this->_aAddedEntities);

		return $this->_aAddedEntities;
	}

	/**
	 * <p>Gibt alle Entities zurück, die entfernt wurden.</p>
	 * @return array
	 */
	public function getRemovedEntities()
	{
		//Einzigartig machen der hinzugefügten Entities.
		$this->_aRemovedEntities = array_unique($this->_aRemovedEntities);

		//Prüfung, ob jedes entfernte Entity auch wirklich noch hinzugefügt ist
		foreach($this->_aRemovedEntities AS $iKey => $oEntity){
			$bIsAdded = false;
			foreach($this->_aEntities AS $oEqEntity){
				if ($oEntity->equals($oEqEntity)) {
					$bIsAdded = true;
					break;
				}
			}
			if ($bIsAdded) {
				unset($this->_aRemovedEntities[$iKey]);
			}
		}

		//Array schön machen
		$this->_aRemovedEntities = array_value($this->_aRemovedEntities);

		return $this->_aRemovedEntities;
	}

	/**
	 * <p>Gibt alle Entities zurück, die verändert wurden.</p>
	 * @return array
	 */
	public function getDirtyEntities()
	{
		$aReturn = array();
		foreach($this->_aEntities AS $oEntity){
			if ($oEntity->isDirty()) {
				$aReturn[] = $oEntity;
			}
		}
		return $aReturn;
	}

	/**
	 * <p>Prüft, ob bei der Collection irgendwelche Änderungen vorgenommen wurden.</p>
	 * @return boolean
	 */
	public function isDirty()
	{
		if ((count($this->_aRemovedEntities) > 0)
			&& (count($this->getRemovedEntities()) > 0)
		) {
			return true;
		}

		if ((count($this->_aAddedEntities) > 0)
			&& (count($this->getAddedEntities()) > 0)
		) {
			return true;
		}

		if (count($this->getDirtyEntities()) > 0) {
			return true;
		}
		
		return false;
	}

	/**
	 * <p>Lässt die Collection alle gemerkten Änderungen vergessen.</p>
	 * <p>Gibt die Methode auch an alle enthaltenen Entities weiter.</p>
	 * @return Dkplus_Model_Domain_Collection_CollectionAbstract
	 */
	public function forgetChanges()
	{
		$this->_aAddedEntities = array();
		$this->_aRemovedEntities = array();
		foreach($this->getDirtyEntities() AS $oEntity){
			$oEntity->forgetChanges();
		}
		return $this;
	}
}