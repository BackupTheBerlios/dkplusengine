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
 * @package    Dkplus_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Plugin_Abstract */
//require-once 'Zend/Controller/Plugin/Abstract.php';

/** Zend_Db */
//require-once 'Zend/Db.php';

/** Zend_Db_Table_Abstract */
//require-once 'Zend/Db/Table/Abstract.php';

/** Zend_Registry */
//require-once 'Zend/Registry.php';

/**
 * Connects to the database.
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Dkplus
 * @package    Dkplus_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    12.03.2009
 */
class Dkplus_Controller_Plugin_Database extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @var string
	 */
	private $_configName = 'database';
	
	/**
	 * Sets the name of the config-entry.
	 * @param string $strConfig the name of the config-entry.
	 * @return Dkplus_Controller_Plugin_Database
	 * @throws Zend_Controller_Exception on wrong parameter.
	 */
	public function setConfigParameter($strConfig){
		if(
			!is_string($strConfig)
		){
			throw new Zend_Controller_Exception('$strConfig is an '.getType($strConfig).', must be an string');
		}
		$this->_configName = $strConfig;
	}
	
	/**
	 * Returns the name of the config-entry that configueres the database.
	 * @return string
	 */
	protected function _getConfigParameter(){
		return $this->_configName;
	}
	
	/**
	 * routeStartup() plugin hook - connects to the database.
	 * Database must be defined in the config-file.
	 * 
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 * @see Controller/Plugin/Zend_Controller_Plugin_Abstract#routeStartup()
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request){
		$dbAdapter = Zend_Db::factory(Zend_Registry::getInstance()->get('Zend_Config')->get($this->_getConfigParameter()));
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		Zend_Registry::getInstance()->set('Zend_Db', $dbAdapter);
	}
}