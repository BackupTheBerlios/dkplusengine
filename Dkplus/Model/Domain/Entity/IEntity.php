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
 * @version    18.12.2009 10:38:45
 */

/**
 * @category
 * @package
 * @subpackage
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Model_Domain_Entity_IEntity extends ArrayAccess
{
	/**
	 * <p>Prüft, ob das Entity-Objekt mit dem Entity-Objekt identisch ist.</p>
	 * @param	Dkplus_Model_Domain_Entity_IEntity $oEntity
	 * @return	boolean
	 */
	public function equals(Dkplus_Model_Domain_Entity_IEntity $oEntity);

	/**
	 * <p>Gibt die geänderte Werte zurück.</p>
	 * @return array
	 */
	public function getChangedValues();

	/**
	 * <p>Gibt einen eindeutigen Identifier für dieses Domain-Objekt zurück.</p>
	 * @return string|int
	 */
	public function getUniqueIdentifier();

	/**
     * <p>Setzt die Werte anhand eines Array oder eines Zend_Config
	 * Objektes.</p>
     *
     * @param	array|Zend_Config				$aoOptions
     * @return	Dkplus_Model_Domain_Abstract    Provides a fluent interface
     */
    public function setOptions($aoOptions);

	/**
	 * <p>Prüft, ob bei dem Entity irgendwelche Änderungen vorgenommen wurden.</p>
	 * @return boolean
	 */
	public function isDirty();

	/**
	 * <p>Lässt das Entity alle gemerkten Änderungen vergessen.</p>
	 * @return Dkplus_Model_Domain_Entity_IEntity
	 */
	public function forgetChanges();
}