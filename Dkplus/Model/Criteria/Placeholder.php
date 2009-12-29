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
 * @version    29.12.2009 01:35:18
 */

/**
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Criteria_Placeholder extends Dkplus_Model_Criteria
	implements Dkplus_Model_Criteria_Placeholder_IPlaceholder
{
	/**
	 * <p>Die Indizes der Bedingungen mit Platzhalter.</p>
	 * @var array
	 */
	protected $_aPlaceholders = array();

	/**
	 * <p>Fügt eine Bedingung hinzu.</p>
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @param string $sType
	 * @param string $sConnector
	 * @return Dkplus_Model_Criteria_Placeholder
	 */
	protected function _addWhere($sPropery, $snbaValue, $sType, $sConnector)
	{		
		//Setzen des Placeholders
		if ($sPropery == self::PLACEHOLDER) {
			$this->_aPlaceholders[] = count($this->_aWheres);
		}
		//Setzen der Bedingung
		return parent::_addWhere($sPropery, $snbaValue, $sType, $sConnector);
	}

	/**
	 * <p>Setzt einen vorher definierten Platzhalter.</p>
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_Placeholder
	 */
	public function setPlaceholder($snbaValue)
	{
		//Gibt es noch freie Platzhalter?
		if (count($this->_aPlaceholders) == 0) {
			throw new Dkplus_Model_Exception('There is no free placeholder.');
		}

		//Setzen des freien Platzhalters:
		$iPlaceholder = array_shift($this->_aPlaceholders);

		$this->_aWheres[$iPlaceholder]['value'] =
			is_scalar($snbaValue)
			|| is_array($snbaValue)
			? $snbaValue
			: (string) $snbaValue;

		return $this;
	}

	/**
	 * <p>Gibt die Bedingungen als Array zurück.</p>
	 * @return array
	 */
	public function getWheres()
	{
		if (count($this->_aPlaceholders) > 0) {
			throw new Dkplus_Model_Exception('There are still free placeholders.');
		}
		return parent::getWheres();
	}
}