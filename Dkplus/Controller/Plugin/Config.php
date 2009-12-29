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

/** Zend_Registry */
//require-once 'Zend/Registry.php';

/** Zend_Cache */
//require-once 'Zend/Cache.php';

/** Zend_Config_Ini */
//require-once 'Zend/Config/Ini.php';

/**
 * Loads the Config-File.
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Dkplus
 * @package    Dkplus_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    12.03.2009
 */
class Dkplus_Controller_Plugin_Config extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @var string
	 */
	private $_cachePath = '';
	
	/**
	 * Retrieves the path to the cache-directory.
	 * @return string
	 */
	protected function _getPath2CacheDirectory(){
		return empty($this->_cachePath) 
			? APPLICATION_PATH.'/../cache'
			: $this->_cachePath;	
	}
	
	/**
	 * Sets the path to the cache-directory.
	 * @param string $strPath Path to cache-directory.
	 * @return Dkplus_Controller_Plugin_Config
	 * @throws Zend_Controller_Exception on wrong Parameter.
	 */
	public function setPath2CacheDirectory($strPath){
		if(
			!is_string($strPath)
		){
			throw new Zend_Controller_Exception('$strPath is an '.getType($strPath).', must be an string');
		}
		$this->_cachePath = $strPath;
		return $this;
	}	
	
	/**
	 * @var string
	 */
	private $_configPath = '';
	
	/**
	 * Retrieves the path to the config-file.
	 * @return string
	 */
	protected function _getPath2ConfigFile(){
		return empty($this->_configPath) 
			? APPLICATION_PATH.'/config/app.ini'
			: $this->_configPath;	
	}
	
	/**
	 * Sets the path to the config-file.
	 * @param string $strPath Path to config-file.
	 * @return Dkplus_Controller_Plugin_Config
	 * @throws Zend_Controller_Exception on wrong Parameter.
	 */
	public function setPath2ConfigFile($strPath){
		if(
			!is_string($strPath)
		){
			throw new Zend_Controller_Exception('$strPath is an '.getType($strPath).', must be an string');
		}
		$this->_configPath = $strPath;
		return $this;
	}
	
	/**
	 * routeStartup() plugin hook -- loads the config-file into the registry.
	 * 
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 * @see Controller/Plugin/Zend_Controller_Plugin_Abstract#routeStartup()
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request){
		$backendOptions = array('cache_dir' => $this->_getPath2CacheDirectory());
		$frontendOptions = array(
			'automatic_serialization' => true, 
			'master_file' => $this->_getPath2ConfigFile(),
			'lifetime' => null,
			'automatic_serialization' => true
		);
		$configCache = Zend_Cache::factory('File', 'File', $frontendOptions, $backendOptions);
		if(
			!$configuration = $configCache->load('Zend_Config')
		){
		    $configuration = new Zend_Config_Ini(
		    	APPLICATION_PATH . '/config/app.ini', 
		    	APPLICATION_ENV
		    );
		    $configCache->save($configuration, 'Zend_Config');
		}
		Zend_Registry::getInstance()->set('Zend_Config', $configuration);
	}	
}