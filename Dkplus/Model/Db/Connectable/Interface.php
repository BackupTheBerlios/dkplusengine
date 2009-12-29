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
 * @version    06.05.2009 21:24:22
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Das Dkplus_Model_Db_Connectable-Interface dient zur eindeutigen Einordnung
 * von Klassen, die eine Verbindung zur Datenbank besitzen.
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @subpackage Db
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see Dkplus_Model_Row_Db
 * @see Dkplus_Model_Rowset_Db
 */
interface Dkplus_Model_Db_Connectable_Interface{
	/**
	 * Verbindet das Objekt mit der Datenbank.
	 * Dies ist nötig um Rowset-Objekte oder Row-Objekte zur Laufzeit mit Tabellen 
	 * zu verbinden.
	 * @param Zend_Db_Table_Abstract $table
	 * @return mixed
	 */
	public function connect(Zend_Db_Table_Abstract $table);
	
	/**
	 * Gibt die "Verbindung" eines Objektes zur Datenbank in Form eines 
	 * Zend_Db_Table_Abstract-Objektes zurück.
	 * 
	 * <p>
	 * Diese Methode dient nur zur Verbindung mit einem anderen 
	 * {@link Dkplus_Model_Db_Connectable_Interface Connectable-Objekt}.
	 * Sie sollte nicht dazu genutzt werden, die Datenbank-Verbindung 
	 * außerhalb des Models zu nutzen. 
	 * </p>
	 * 
	 * @return Zend_Db_Table_Abstract
	 */
	public function getConnection();
	
	/**
	 * Prüft, ob ein {@link Dkplus_Model_Db_Connectable_Interface Connectable-Objekt}
	 * mit der Datenbank verbunden ist.
	 * @return boolean
	 */
	public function isConnected();
}