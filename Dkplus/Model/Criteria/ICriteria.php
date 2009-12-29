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
interface Dkplus_Model_Criteria_ICriteria
{
	const PLACEHOLDER = 'dkplus_model_criteria_icriteria_placeholder';
	const ORDER_ASC 		= 'asc';
	const ORDER_DESC 		= 'desc';
	const WHERE_AND 		= 'and';
	const WHERE_OR 			= 'or';
	const WHERE_LIKE 		= 'like';
	const WHERE_NOT_LIKE 	= 'not like';
	const WHERE_IS 			= '=';
	const WHERE_IS_NOT		= '!=';
	const WHERE_BIGGER 		= '>';
	const WHERE_SMALLER 	= '<';

	/**
	 * <p>Fügt eine und-ist-gleich Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhere($sProperty, $snbaValue);

	/**
	 * <p>Fügt eine und-ist-größer-als Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @param boolean $bIncluded
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereBigger($sProperty, $snbaValue, $bIncluded = false);

	/**
	 * <p>Fügt eine und-ist-kleiner-als Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @param boolean $bIncluded
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereSmaller($sProperty, $snbaValue, $bIncluded = false);
	
	/**
	 * <p>Fügt eine oder-ist-gleich Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @param boolean $bIncluded
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhere($sProperty, $snbaValue);


	/**
	 * <p>Fügt eine oder-ist-größer-als Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @param boolean $bIncluded
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereBigger($sProperty, $snbaValue, $bIncluded = false);

	/**
	 * <p>Fügt eine oder-ist-kleiner-als Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereSmaller($sProperty, $snbaValue, $bIncluded = false);
	
	/**
	 * <p>Fügt eine und-ist-ungleich Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereNot($sProperty, $snbaValue);
	
	/**
	 * <p>Fügt eine oder-ist-ungleich Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereNot($sProperty, $snbaValue);
	
	/**
	 * <p>Fügt eine und-ist-wie Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereLike($sProperty, $snbaValue);
	
	/**
	 * <p>Fügt eine oder-ist-wie Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereLike($sProperty, $snbaValue);
	
	/**
	 * <p>Fügt eine und-ist-nicht-wie Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereNotLike($sProperty, $snbaValue);
	
	/**
	 * <p>Fügt eine oder-ist-nicht-wie Bedingung hinzu.</p>
	 * @param string $sProperty
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereNotLike($sProperty, $snbaValue);
	
	/**
	 * <p>Setzt das Limit für die Abfrage.</p>
	 * @param int $iCount
	 * @param int $iStart
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function setLimit($iCount, $iStart = 0);

	/**
	 * <p>Fügt eine Sortierungsreihenfolge hinzu.</p>
	 * @param string $sProperty
	 * @param string $sDirection
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function addOrder($sProperty, $sDirection = 'asc');

	/**
	 * <p>Fügt eine aufsteigende Sortierungsreihenfolge hinzu.</p>
	 * @param string $sProperty
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function addOrderAsc($sProperty);

	/**
	 * <p>Fügt eine absteigende Sortierungsreihenfolge hinzu.</p>
	 * @param string $sProperty
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function addOrderDesc($sProperty);
	
	/**
	 * <p>Gibt die Sortierungsreihenfolge als Array zurück.</p>
	 * @return array
	 */
	public function getOrders();
	
	/**
	 * <p>Gibt den Startwert des Limits zurück.</p>
	 * @return int
	 */
	public function getLimitStart();
	
	/**
	 * <p>Gibt die Anzahl der maximalen Entities zurück.</p>
	 * @return int|null
	 */
	public function getLimitCount();
	
	/**
	 * <p>Gibt die Bedingungen als Array zurück.</p>
	 * @return array
	 */
	public function getWheres();
	
	/**
	 * <p>Gibt den ersten Connector (and, or) zurück.</p>
	 * @return string
	 */
	public function getWhereConnector();
}