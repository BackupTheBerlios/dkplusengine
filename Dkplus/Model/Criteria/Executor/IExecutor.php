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
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Model_Criteria_Executor_IExecutor{
	/**
	 * <p>Setzt die Kriterien, die später ausgeführt werden sollen.</p>
	 * @param Dkplus_Model_Criteria_Interface $crit
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setCriteria(Dkplus_Model_Criteria_Interface $crit);
	
	/**
	 * <p>Setzt die Daten, die später sortiert und gefiltert werden sollen.</p>
	 * @param array $data
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setArray(array $data);
	
	/**
	 * <p>Setzt die Daten, die später sortiert und gefiltert werden sollen.</p>
	 * @param Traversable $data
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setTraversable(Traversable $data);
	
	/**
	 * <p>Setzt die Alias' die genutzt werden können.</p>
	 * @param array $alias
	 * @return Dkplus_Model_Criteria_Executor_Interface
	 */
	public function setAlias(array $alias);
	
	/**
	 * <p>Führt den Executor mit den Aktuell gesetzen Daten aus.
	 * @return array
	 */
	public function execute();

	/**
	 * <p>Sortierungs-Funktion zur Nutzung mittels 
	 * {@link http://de.php.net/manual/de/function.usort.php}.</p>
	 * @param array|ArrayAccess $a
	 * @param array|ArrayAccess $b
	 * @return int <p>-1, wenn das $a vor $b im Array stehen soll, ansonsten 1.</p>
	 */
	public function sort($a, $b);
}