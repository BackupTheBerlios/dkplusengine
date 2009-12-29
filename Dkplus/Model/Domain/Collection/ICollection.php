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
 * @version    22.12.2009 00:47:07
 */

/**
 * @category
 * @package
 * @subpackage
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Model_Domain_Collection_ICollection
	extends Iterator, ArrayAccess, Countable
{
	/**
	 * <p>Gibt alle Entities zurück, die entfernt wurden.</p>
	 * @return array
	 */
	public function getRemovedEntities();

	/**
	 * <p>Gibt alle Entities zurück, die hinzugefügt wurden.</p>
	 * @return array
	 */
	public function getAddedEntities();

	/**
	 * <p>Prüft, ob bei der Collection irgendwelche Änderungen vorgenommen wurden.</p>
	 * @return boolean
	 */
	public function isDirty();

	/**
	 * <p>Lässt die Collection alle gemerkten Änderungen vergessen.</p>
	 * <p>Gibt die Methode auch an alle enthaltenen Entities weiter.</p>
	 * @return Dkplus_Model_Domain_Collection_ICollection
	 */
	public function forgetChanges();
}