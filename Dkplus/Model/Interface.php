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
 * <p>Ein "Model" dient als Abstraktionslayer für die Datenschicht, so können 
 * Datenbanken, Textdateien, CSV-Dateien oder ähnliches als Datenschicht genutzt
 * werden, ohne dass auf ein einheitliches Interface verzichtet werden müsste.</p>
 * 
 * <p>
 * Da jedes Model nur einmal benötigt wird, empfiehlt es sich, {@link Dkplus_Model_Factory}
 * zur Verwaltung der Instanzen zu nutzen.
 * </p>
 * 
 * <p>
 * Jedes Model kann ein "inneres Objekt" besitzen. Es handelt sich dabei um ein
 * {@link Dkplus_Model_Row_Interface Dkplus_Model_Row-Objekt}. Dies kann 
 * beispielsweise mit Sessions gespeichert werden. Das Model dient hierbei als 
 * zentrale Zugriffsstelle zum ansprechen dieses inneren Objektes. Auf diese Weise 
 * ist es z.B. möglich, die Userdaten an einer zentralen Stelle anzusprechen.
 * <code>
 * //User-Model über die Factory holen:
 * $user = Dkplus_Model_Factory::get('User');
 * 
 * //Greift mittels der __get Methode auf die Eigenschaften des inneren Objektes zu.
 * print $user->name;
 * </code>
 * </p> 
 *
 * <p>
 * Zum selektieren der Daten dienen die Methoden {@link fetchRow()} und 
 * {@link fetchRowset()}. Es wird allerdings empfohlen, eigene Methoden aufbauend 
 * auf diesen Methoden zu implementieren.
 * <code>
 * public function fetchRowsetByName($name){
 * 	return $this->fetchRowset((string) $name, 'user_name');
 * }
 * </code>
 * Alternativ kann auch auf andere Art und Weise für eine Unabhängigkeit von der 
 * Datenquelle gesorgt werden, z.B. mittels Inflection, wie es bei den 
 * Kindklassen von {@link Dkplus_Model_Abstract} der Fall ist.
 * </p>
 * 
 * <p>
 * Eine weitere, oft benötigte Funktionalität ist das Suchen nach ähnlichen Werten.
 * Dafür stehen die Methoden {@link fetchRowsetLike()} und {@link fetchRowLike} zur
 * Verfügung, die nach Ähnlichen Werten suchen d.h.
 * @todo Werden die Like-Methodne eingeführt? Dkplus_Model_Criteria?
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Model_Interface extends ArrayAccess{
	/**
	 * <p>Sets a property of "inner row".</p>
	 * <p>Setzt eine Eigenschaft des "inneren Objektes".</p> 
	 * @param string $property
	 * @param mixed $value
	 * @return void
	 */
	public function __set($property, $value);
	
	/**
	 * Returns a property of "inner row".
	 * Gibt die Eigenschaft des "inneren Objektes" zurück.
	 * @param string $property
	 * @return mixed
	 */
	public function __get($property);

	/**
	 * Checks whether a property of "inner row" exists or not.
	 * Überprüft, ob eine Eigenschaft des "inneren Objektes" existiert.
	 * @param string $property
	 * @return boolean
	 */
	public function __isset($property);
	
	/**
	 * <p>Leitet einen Methodenaufruf weiter an das "innere Objekt".</p>
	 * 
	 * <p>Zudem ermöglicht es auch den Zugriff auf einige Methoden, die auf
	 * die Inflection-Möglichkeiten zugreifen (s.o.).</p> 
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($method, array $arguments);
	
	/**
	 * <p>Gibt einen {@link Dkplus_Model_Rowset_Interface Rowset} an Einträgen 
	 * zurück, die mit den gegebenen Parametern übereinstimmen.</p>
	 * 
	 * <p>Wird keine Übereinstimmung gefunden, wird ein leeres 
	 * {@link Dkplus_Model_Rowset_Interface Rowset} zurückgegeben.</p>
	 * 
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function fetchRowset(Dkplus_Model_Criteria_Interface $crit = null);
	
	/**
	 * <p>Gibt eine einzelne {@link Dkplus_Model_Row_Interface Row} zurück.</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Row_Interface Wurde kein Eintrag gefunden, so wird null
	 * zurückgegeben.
	 */
	public function fetchRow(Dkplus_Model_Criteria_Interface $crit = null);	
	
	/**
	 * Gibt das "innere Objekt" des Models (falls vorhanden) zurück.
	 * @return Dkplus_Model_Row_Interface
	 */
	public function fetchInnerRow();
	
	/**
	 * <p>Gibt ein neues {@link Dkplus_Model_Row_Interface Row-Objekt} zurück, das vom User 
	 * mit Daten "gefüllt" werden kann.</p>
	 * @return Dkplus_Model_Row_Interface
	 */
	public function fetchNewRow();
	
	/**
	 * <p>Holt die verknüpften Einträge des Models.</p>
	 * <p>Wird in erster Linie von {@link DKplus_Model_Row::fetchDependent()} 
	 * aufgerufen.</p> 
	 * @param Dkplus_Model_Interface $model
	 * @param scalar $modelValue
	 * @param Dkplus_Model_Critera_Interface $crit
	 * @return Dkplus_Model_Rowset_Interface
	 */
	public function fetchDependent(Dkplus_Model_Interface $model, $modelValue, $crit = null);
	
	/**
	 * <p>Gibt die Abhängigkeiten des Models als Array zurück.</p>
	 * @return array
	 */
	public function getDependencies();
	
	/**
	 * Fügt Daten zu der Datenquelle hinzu.
	 * @param array $data
	 * @return Dkplus_Model_Interface
	 */
	public function insert(array $data);
	
	/**
	 * Aktualisiert Daten aus der Datenquelle.
	 * Achtung, einige Datenquellen (wie z.B. einige Webservices) können Read-Only sein!
	 * @param array $data
	 * @param Dkplus_Model_Criteria_Interface $crit Es werden nur die Where-Argumente des Kriteriums angenommen.
	 * @return int The number of updated records. Die Anzahl der aktualisierten Datensätze. 
	 */
	public function update(array $data, Dkplus_Model_Criteria_Interface $crit = null);
	
	/**
	 * <p>Löscht Daten aus der Datenquelle.</p>
	 * <p>Achtung, einige Datenquellen (wie z.B. einige Webservices) können Read-Only sein!</p>
	 * @param Dkplus_Model_Criteria_Interface $crit Es werden nur die Where-Argumente des Kriteriums angenommen.
	 * @return int The number of deleted records. Die Anzahl der gelöschten Datensätze. 
	 */
	public function delete(Dkplus_Model_Criteria_Interface $crit = null);
	
	/**
	 * <p>Gibt die Row-Klasse zurück.</p>
	 * @return string
	 */
	public function getRowClass();
	
	/**
	 * <p>Gibt die Anzahl der Datensätze zurück.</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return int
	 */
	public function count(Dkplus_Model_Criteria_Interface $crit = null);

	/**
	 * <p>Gibt einen Paginator zurück.</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Zend_Paginator
	 */
	public function getPaginator(Dkplus_Model_Criteria_Interface $crit = null);
}