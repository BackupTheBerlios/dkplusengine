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
 * @version    25.12.2009 14:08:27
 */

/**
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Domain_EmbeddedValue
	implements Dkplus_Model_Domain_Embedded_Value_IEmbeddedValue
{
	protected $_sEntityClass = '';

	protected $_aOptions = array();

    public function  __construct($sEntityClass, array $aNeededOptions)
	{
		if (!class_exists($sEntityClass)) {
			throw new Dkplus_Model_Domain_Exception('There is no class '
				. $sEntityClass . '.');
		}
		$this->_sEntityClass = $sEntityClass;
		$this->_aOptions = $aNeededOptions;
		$this->_init();
	}

	/**
	 * <p>Hook, wird nach dem Konstruktor ausgeführt.</p>
	 */
	protected function _init()
	{
	}

	/**
	 * <p>Sucht sich aus den gegebenen Options die benötigten heraus und
	 * entwirft aus ihnen ein neues Domain_Entity Objekt.</p>
	 * @param array $aOptions
	 */
	public function getEntity(array $aOptions)
	{
		$aValues = array();
		foreach($this->_aOptions AS $sOption) {
			if (!isset($aOptions[$sOption])) {
				throw new Dkplus_Model_Domain_Exception('There must be an key '
					. $sOption . ' in the first parameter.');
			}
			$aValues[$sOption] = $aOptions[$sOption];
		}
		return new $this->_sEntityClass($aValues);
	}
}