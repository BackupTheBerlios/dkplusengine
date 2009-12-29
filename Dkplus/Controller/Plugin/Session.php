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

/** Zend_Session */
//require-once 'Zend/Session.php';

/** Zend_Registry */
//require-once 'Zend/Registry.php';

/**
 * Loads the translation.
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Dkplus
 * @package    Dkplus_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    12.03.2009
 */
class Dkplus_Controller_Plugin_Session extends Zend_Controller_Plugin_Abstract{
	public function routeStartup(Zend_Controller_Request_Abstract $request){
		Zend_Session::setOptions(
			Zend_Registry::getInstance()->get('Zend_Config')->get('session')->get('params')->toArray()
		);
	}	
}
