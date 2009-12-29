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

/** Zend_Cache */
//require-once 'Zend/Cache.php';

/** Zend_Translate */
//require-once 'Zend/Translate.php';

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
class Dkplus_Controller_Plugin_Translation extends Zend_Controller_Plugin_Abstract{
	public function routeStartup(Zend_Controller_Request_Abstract $request){
		$backendOptions = array('cache_dir' => APPLICATION_PATH.'/../cache');
		$frontendOptions = array(
			'caching' => (
				Zend_Registry::getInstance()->get('Zend_Config')->get('cache', false) == false
				? false
				: Zend_Registry::getInstance()->get('Zend_Config')->get('cache')->get('enabled', false)
			),
			'automatic_serialization' => true, 
			'lifetime' => null,
			'automatic_serialization' => true
		);
		$translateCache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		Zend_Translate::setCache($translateCache);
		$translate = new Zend_Translate(Zend_Translate::AN_ARRAY, APPLICATION_PATH.'/languages', null, array('scan' => Zend_Translate::LOCALE_DIRECTORY));
		Zend_Registry::getInstance()->set('Zend_Translate', $translate);
	}	
}