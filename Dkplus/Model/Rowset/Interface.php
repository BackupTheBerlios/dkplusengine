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
 * @version    07.04.2009 17:00:55
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * <p>
 * Ein Rowset lässt sich am einfachsten als Liste beschreiben. Es handelt sich
 * dabei um eine Liste von {@link Dkplus_Model_Row_Interface Dkplus_Model_Row}-Objekten.
 * </p>
 * 
 * <p>
 * Um auf die Elemente zuzugreifen, lässt sich z.B. mittels foreach iterieren. Alsternativ
 * ist auch ein Zugriff anhand des Indizes möglich.
 * <code>
 * <?php $rowset = Model_User::getInstance()->fetchEntries(); ?>
 * Ein Überblick über alle Elemente:
 * <ul>
 * 	<?php foreach($rowset AS $row): ?>
 * 		<li><?php print $row->name; ?></li>
 * 	<?php endforeach; ?>
 * </ul>
 * Das zweite Element:
 * <?php 
 * $secondRow = $rowset[1];
 * print $secondRow->name;
 * ?>
 * </code>
 * </p>
 * 
 * <p>
 * Genau wie ein {@link Dkplus_Model_Row_Interface::isReadOnly() Row-Objekt} 
 * kann auch ein Rowset als "readOnly" markiert werden. Dabei ist zu unterscheiden
 * zwischen den Methoden {@link setReadOnly()} und {@link setRowsReadOnly()}.
 * Erstere bewirkt, dass keine Row-Objekte zu dem Rowset hinzugefügt oder entfernt
 * werden können, letzteres setzt alle Row-Objekte readOnly, so dass sie nicht 
 * verändert werden können.
 * </p>
 * 
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Model_Rowset_Interface extends Iterator, ArrayAccess, Countable{
	/**
	 * <p>Wenn ein Parameter übergeben wird, so wird dieser mittels {@link addRows()}
	 * oder mittels {@link addRow()} an den Rowset übergeben.</p>
	 * @param Dkplus_Model_Interface $model
	 * @param mixed $rows
	 * @return void
	 */
	public function __construct(Dkplus_Model_Interface $model, $rows = null);
	
	/**
	 * <p>Sets data for each row of the rowset.</p>
	 * <p>Setzt die Daten für jedes Row-Element des Rowsets.</p>
	 * @param string $property
	 * @param mixed $value
	 * @return void
	 */	
	public function __set($property, $value);
	
	/**
	 * <p>Saves each entry of an rowset.</p>
	 * <p>Speichert jeden Eintrag des Rowsets.</p>
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function save();
	
	/**
	 * <p>Adds a row to the rowset.</p>
	 * <p>Fügt dem Rowset ein Row-Objekt hinzu.</p>
	 * @param mixed $row
	 * @return Dkplus_Model_Rowset_Interface
	 * @todo Zusammenlegen mit addRows()?
	 */
	public function addRow($row);
	
	/**
	 * <p>Adds some rows to the rowset.</p>
	 * <p>Fügt dem Rowset einige Row-Objekte hinzu.</p>
	 * @param mixed $rows
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function addRows($rows);
	
	/**
	 * Filters the rows of the rowset.
	 * 
	 * 
	 * <p>Filtert den Rowset und gibt einen neuen Rowset zurück, der nur 
	 * Row-Objekte beinhaltet, die mit den Kriterien der Filterung übereinstimmen.</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function filter(Dkplus_Model_Criteria_Interface $crit);
	
	/**
	 * <p>Creates an new row.</p>
	 * 
	 * <p>Gibt ein neues Row-Objekt zurück, das mit der Datenquelle verbunden ist.</p>
	 * @return Dkplus_Model_Row_Interface
	 */
	public function fetchNewRow();
	
	/**
	 * <p>Checks whether the hole rowset is read-only. To an read-only rowset 
	 * you cannot add an row.</p>
	 * 
	 * <p>
	 * Prüft, ob der Rowset "readOnly" ist. Aus einem "readOnly"-Rowset kann man 
	 * keine Row-Objekte entfernen und auch keine Hinzufügen. Das Updaten der
	 * einzelnen Row-Objekte ist weiterhin möglich.
	 * </p>
	 * @return boolean
	 * @see setReadOnly()
	 */
	public function isReadOnly();
	
	/**
	 * <p>Sets the rowset read-only.</p>
	 * 
	 * <p>Markiert den Rowset als "{@link isReadOnly() readOnly}".</p>
	 * 
	 * <p>Um alle Row-Objekte als "readOnly" zu markieren, nutzen sie 
	 * {@link setRowsReadOnly()}.</p>
	 * 
	 * @return Dkplus_Model_Rowset_Interface
	 * @see isReadOnly()
	 * @see setRowsReadOnly()
	 */
	public function setReadOnly();
	
	/**
	 * 
	 * <p>Markiert alle Row-Objekte des Rowsets als "readOnly". Es lassen sich dem dennoch
	 * weitere Row Objekte hinzufügen oder entfernen. Nutzen sie 
	 * {@link setReadOnly()} um dies zu verhindern.</p>
	 * 	 
	 * @return Dkplus_Model_Rowset_Interface
	 * @see setRowsReadOnly()
	 */
	public function setRowsReadOnly();
	
	/**
	 * <p>
	 * Gibt den Rowset als numerischen Array zurück. Die einzelnen Rows 
	 * werden dabei zu assoziativen Arrays umgewandelt.
	 * </p> 
	 * @return array
	 * @see Dkplus_Model_Row_Interface::toArray()
	 */
	public function toArray();
}