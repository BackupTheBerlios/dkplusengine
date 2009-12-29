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
 * @version    28.12.2009 21:21:08
 */

/**
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Domain_IdentityMap
	implements Dkplus_Model_Domain_IdentityMap_IIdentityMap
{
	protected $_aEntities = array();
	
	/**
	 * <p>Liefert ein gespeichertes Entity zurück.</p>
	 * <p>Wurde ein Entity übergeben und es war noch kein Entity gespeichert,
	 * so wird dieses Entity gespeichert und zurückgegeben.</p>
	 * @param Dkplus_Model_Domain_Entity_IEntity|string|int $soiEntity
	 * @return Dkplus_Model_Domain_Entity_IEntity|null <p>Wurde kein Entity
	 * gefunden, so wird null zurückgegeben.</p>
	 */
	public function getEntity($soiEntity)
	{
		//Ein Entity-Objekt wurde übergeben
		if ($soiEntity instanceOf Dkplus_Model_Domain_Entity_IEntity) {
			$siEntity = $soiEntity->getUniqueIdentifier();
			if (!isset($this->_aEntities[$siEntity])) {
				$this->_aEntities[$siEntity] = $soiEntity;
			}
			return $this->_aEntities[$siEntity];
		}

		if ((is_int($soiEntity))
			|| (is_string($soiEntity))
		) {
			return isset($this->_aEntities[$soiEntity])
				? $this->_aEntities[$soiEntity]
				: null;
		}

		throw new Dkplus_Model_Exception(
				sprintf('First Parameter has an invalid type "%s", must be an '
						. 'Dkplus_Model_Domain_Entity_IEntity, integer or string.',
    				getType($iOffset)));
	}
}