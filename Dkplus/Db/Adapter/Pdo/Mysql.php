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
 * @package    Dkplus_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    08.04.2009 12:29:35
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Db_Adapter_Pdo_Mysql
 */
//require-once 'Zend/Db/Adapter/Pdo/Mysql.php';

/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql{
	protected function _connect(){
        // if we already have a PDO object, no need to re-connect.
        if ($this->_connection) {
        	return;
        }

        parent::_connect();

        // set connection to utf8
    	$this->query('SET NAMES utf8');
	}
}