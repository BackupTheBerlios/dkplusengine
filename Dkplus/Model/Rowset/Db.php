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
 * @subpackage Db
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    07.04.2009 19:09:29
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Dkplus_Model_Rowset_Abstract **/
//require-once 'Dkplus/Model/Rowset/Abstract.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Model
 * @subpackage Db
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Model_Rowset_Db extends Dkplus_Model_Rowset_Abstract{
	/**
	 * @var string
	 */
	protected $_rowClass = 'Dkplus_Model_Row_Db';

}