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
interface Dkplus_Model_Criteria_Placeholder_IPlaceholder
	extends Dkplus_Model_Criteria_ICriteria
{
	/**
	 * <p>Sollte gesetzt werden, wenn der Wert im Nachhinein erst eingefügt
	 * werden soll.</p>
	 * @var string
	 */
	const PLACEHOLDER = 'dkplus_model_criteria_icriteria_placeholder';


	/**
	 * <p>Setzt einen vorher definierten Platzhalter.</p>
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_Placeholder_IPlaceholder
	 */
	public function setPlaceholder($snbaValue);
}