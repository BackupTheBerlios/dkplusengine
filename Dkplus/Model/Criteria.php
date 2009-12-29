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
 * @see Dkplus_Model_Criteria_ICriteria
 */
//require-once 'Dkplus/Model/Criteria/Interface.php';

/**
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Criteria implements Dkplus_Model_Criteria_ICriteria
{
	/**
	 * 
	 * @var int
	 */
	protected $_iLimitStart = null;
	
	/**
	 * 
	 * @var int
	 */
	protected $_iLimitCount = null;
	
	/**
	 * 
	 * @var array
	 */
	protected $_aOrders = array();
	
	/**
	 * 
	 * @var array
	 */
	protected $_aWheres = array();
	
	/**
	 * 
	 * @var string
	 */
	protected $_sWhereConnector = null;

	/**
	 * 
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhere($sPropery, $snbaValue)
	{
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_IS, self::WHERE_AND);
	}
	
	/**
	 * 
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhere($sPropery, $snbaValue)
	{
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_IS, self::WHERE_OR);
	}

	/**
	 *
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereBigger($sPropery, $snbaValue, $bIncluded = false)
	{
		$snbaValue = (int) $snbaValue;
		if ($bIncluded) {
			$snbaValue--;
		}
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_BIGGER, self::WHERE_AND);
	}

	/**
	 *
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereSmaller($sPropery, $snbaValue, $bIncluded = false)
	{
		$snbaValue = (int) $snbaValue;
		if ($bIncluded) {
			$snbaValue++;
		}
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_SMALLER, self::WHERE_AND);
	}

	/**
	 *
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereBigger($sPropery, $snbaValue, $bIncluded = false)
	{
		$snbaValue = (int) $snbaValue;
		if ($bIncluded) {
			$snbaValue--;
		}
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_BIGGER, self::WHERE_OR);
	}

	/**
	 *
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereSmaller($sPropery, $snbaValue, $bIncluded = false)
	{
		$snbaValue = (int) $snbaValue;
		if ($bIncluded){
			$snbaValue++;
		}
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_SMALLER, self::WHERE_OR);
	}
	
	/**
	 * 
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereNot($sPropery, $snbaValue)
	{
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_IS_NOT, self::WHERE_AND);
	}
	
	/**
	 * 
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereNot($sPropery, $snbaValue)
	{
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_IS_NOT, self::WHERE_OR);
	}
	
	/**
	 * 
	 * @param string $sPropery
	 * @param scalar $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereLike($sPropery, $snbaValue)
	{
		$snbaValue = is_scalar($snbaValue) ? $snbaValue : (string) $snbaValue;
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_LIKE, self::WHERE_AND);
	}
	
	/**
	 * 
	 * @param string $sPropery
	 * @param scalar $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereLike($sPropery, $snbaValue)
	{
		$snbaValue = is_scalar($snbaValue) ? $snbaValue : (string) $snbaValue;
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_LIKE, self::WHERE_OR);
	}
	
	/**
	 * 
	 * @param string $sPropery
	 * @param scalar $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function andWhereNotLike($sPropery, $snbaValue)
	{
		$snbaValue = is_scalar($snbaValue) ? $snbaValue : (string) $snbaValue;
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_NOT_LIKE, self::WHERE_AND);
	}
	
	/**
	 * 
	 * @param string $sPropery
	 * @param scalar $snbaValue
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function orWhereNotLike($sPropery, $snbaValue)
	{
		$snbaValue = is_scalar($snbaValue) ? $snbaValue : (string) $snbaValue;
		return $this->_addWhere($sPropery, $snbaValue, self::WHERE_NOT_LIKE, self::WHERE_OR);
	}
	
	/**
	 * <p>Fügt eine Bedingung hinzu.</p>
	 * @param string $sPropery
	 * @param scalar|array $snbaValue
	 * @param string $sType
	 * @param string $sConnector
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	protected function _addWhere($sPropery, $snbaValue, $sType, $sConnector)
	{
		if (is_null($this->_sWhereConnector)) {
			$this->_sWhereConnector = (string) $sConnector;
			$sConnector = '';
		}
		$this->_aWheres[] = array(
			'col' 		=> (string) $sPropery,
			'value' 	=> is_scalar($snbaValue) || is_array($snbaValue) ? $snbaValue : (string) $snbaValue,
			'type'		=> $sType,
			'connector' => (string) $sConnector
		);
		return $this;
	}
	
	
	/**
	 * 
	 * @param int $count
	 * @param int $start
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function setLimit($count, $start = 0)
	{
		$this->_iLimitCount = abs($count);
		$this->_iLimitStart = abs($start);
		return $this;
	}
	
	/**
	 * 
	 * @param string $crit
	 * @param string $direction
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function addOrder($crit, $direction = self::ORDER_ASC)
	{
		if (($direction != self::ORDER_ASC)
			&& ($direction != self::ORDER_DESC)
		) {
			/**
			 * @see Dkplus_Model_Exception
			 */
			//require-once 'Dkplus/Model/Exception.php';
			throw new Dkplus_Model_Exception('Second parameter must be' 
				.'Dkplus_Model_Criteria::ORDER_ASC or Dkplus_Model_Criteria::ORDER_DESC.');
		}
		$this->_aOrders[(string) $crit] = $direction;
		return $this;
	}

	/**
	 *
	 * @param string $crit
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function addOrderAsc($crit)
	{
		return $this->addOrder($crit, self::ORDER_ASC);
	}

	/**
	 *
	 * @param string $crit
	 * @return Dkplus_Model_Criteria_ICriteria
	 */
	public function addOrderDesc($crit)
	{
		return $this->addOrder($crit, self::ORDER_DESC);
	}
	
	/**
	 * @return array
	 */
	public function getOrders()
	{
		return $this->_aOrders;
	}
	
	/**
	 * @return int
	 */
	public function getLimitStart()
	{
		return $this->_iLimitStart;
	}
	
	/**
	 * @return int|null
	 */
	public function getLimitCount()
	{
		return $this->_iLimitCount;
	}
	
	/**
	 * @return array
	 */
	public function getWheres()
	{
		return $this->_aWheres;
	}
	
	/**
	 * @return string
	 */
	public function getWhereConnector()
	{
		return $this->_sWhereConnector;
	}
}