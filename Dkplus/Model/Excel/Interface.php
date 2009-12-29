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
 * @subpackage Excel
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    06.05.2009 21:24:22
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** 
 * @see Dkplus_Model_Interface  
 */
//require-once 'Dkplus/Model/Interface.php';

/**
 * @category   Dkplus
 * @package    Dkplus_Model
 * @subpackage Excel
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Dkplus_Model_Excel_Interface extends Dkplus_Model_Interface{
	/**
	 * @return boolean
	 */
	public function isReadOnly();
}