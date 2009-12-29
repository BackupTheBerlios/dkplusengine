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
 * @version    07.04.2009 17:00:07
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Bei einer "Row" handelt es sich um einen Datensatz, der von einer Quelle
 * (einem Model) geliefert wird. Er kann dabei verschiedenste Datenquellen 
 * nutzen (siehe {@link Dkplus_Model_Interface}).
 * 
 * <p>
 * Jedes Objekt, dass dieses Interface implementiert, stellt eine Row aus
 * des Models da. Die Eigenschaften lassen sich so auf einfache Art und Weise 
 * verändern:
 * <code>
 * //Holen eines Objektes aus dem Model 
 * $row = Model_User::getInstance()->fetchEntry(1);
 * 
 * //Setzen des Namens
 * $row->name = 'Oskar';
 * 
 * //Speichern
 * $row->save();
 * </code>
 * </p>
 * 
 * <p>
 * Genauso einfach wie man einen Eintrag ändern kann, kann man auch einen neuen 
 * Eintrag anlegen oder löschen:
 * <code>
 * //Holen eines leeren Row-Objektes
 * $newRow = Model_User::getInstance()->fetchNew();
 * 
 * //Setzen des Namens
 * $newRow->name = 'Hasi';
 * 
 * //Speichern, ein neuer Eintrag wurde nun angelegt.
 * $newRow->save();
 * 
 * //Und nun das ganze wieder löschen
 * $newRow->delete();
 * </code>
 * </p>
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Model_Row_Interface extends ArrayAccess{
	/**
	 * <p>Dem Kontruktor müssen die Daten des Objektes mit übergeben werden.</p>
	 * 
	 * <p>Was die einzelnen Row-Objekte für Parameter verarbeiten können, ist 
	 * je nach Row-Klasse unterschiedlich.</p>
	 * @param Dkplus_Model_Interface $model
	 * @param mixed $row
	 * @param boolean $new TRUE, wenn es sich um eine nicht gespeicherte, neue Reihe handelt, 
	 * andererseits FALSE. Ist $row nicht gesetzt, wird $new auf TRUE gesetzt.
	 * @return void
	 */
	public function __construct(Dkplus_Model_Interface $model, $row = null, $new = false);
	
	/**
	 * Gibt eine Eigenschaft des Objektes zurück.
	 * @param string $property
	 * @return mixed
	 */
	public function __get($property);
	
	/**
	 * Prüft, ob eine Eigenschaft in dem Objekt vorhanden ist.
	 * @param string $property
	 * @return boolean
	 */
	public function __isset($property);
	
	/**
	 * Setzt eine Eigenschaft des Objektes.
	 * @param string $property
	 * @param mixed $value
	 * @return void
	 */
	public function __set($property, $value);

	/**
	 * Gibt eine Eigenschaft des Objektes zurück.
	 * @param string $property
	 * @return mixed
	 */
	public function get($property);

	/**
	 * Setzt eine Eigenschaft des Objektes.
	 * @param string $property
	 * @param mixed $value
	 * @return Dkplus_Model_Row_Interface
	 */
	public function set($property, $value);
	
	/**
	 * Prüft, ob das Objekt "readOnly" ist.
	 * 
	 * <p>
	 * Auf "readOnly"-objekte darf nur lesend zugegriffen werden, das Speichern,
	 * Löschen oder Ändern von Einträge ist nicht möglich.
	 * </p>
	 * @return boolean
	 */
	public function isReadOnly();
	
	/**
	 * Setzt das Objekt "readOnly".
	 * @return Dkplus_Model_Row_Interface
	 * @see isReadOnly()
	 */
	public function setReadOnly();	
	
	/**
	 * Changes the row-data by an array after the data has stored first.
	 * Empfängt die Daten zum Ändern mittels eines Arrays.
	 * @param array $data
	 * @return Dkplus_Model_Row_Interface
	 */
	public function setFromArray(array $data);
	
	/**
	 * Saves the row (update or insert) if the row is not read-only.
	 * <p>
	 * Speichert das Objekt durch ein Update oder ein Insert. Das Speichern ist
	 * nur möglich, wenn das Objekt nicht "readOnyl" ist.
	 * </p>
	 * @return Dkplus_Model_Row_Interface
	 */
	public function save();

	/**
	 * Deletes an row if she is not read-only.
	 * <p>
	 * Löscht das Objekt aus dem Datenspeicher. Das Löschen ist nur möglich
	 * wenn das Objekt nicht "readOnyl" ist.
	 * </p>
	 * @return Dkplus_Model_Row_Interface
	 */
	public function delete();
	
	/**
	 * Gibt die Daten des des Objektes als assoziativen Array zurück.
	 * @return array
	 */
	public function toArray();
	
	/*
	 * Prüft, ob das Objekt schonmal in der Datenquelle gespeichert wurde oder nicht.
	 * @return boolean
	 */
	public function isSaved();
	
	/**
	 * <p>Gibt einen abhängigen Rowset zurück.</p>
	 * @param string $type
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function fetchDependentRowset($type, Dkplus_Model_Criteria_Interface $crit = null);
	
	/**
	 * <p>Gibt eine abhängige Row zurück.</p> 
	 * @param string $type
	 * @return Dkplus_Model_Row_Interface
	 */
	public function fetchParentRow($type);
}