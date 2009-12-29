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
 * @version    22.09.2009 00:12:20
 */

/**
 * @category
 * @package
 * @subpackage
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Domain_Filter_DateObjectToSQLDatetime implements Dkplus_Model_Domain_Filter_IFilter{
    public function filter($value){
		if(
			!($value instanceOf Zend_Date)
		){
			throw new Dkplus_Model_Domain_Filter_Exception('First parameter is no valid date-object.');
		}
		return $value->setTimezone('Europe/London')
			->toString('yyyy-MM-dd hh-mm-ss');
	}
}