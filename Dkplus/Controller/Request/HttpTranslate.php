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
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    27.07.2009 13:59:36
 */

/** Zend_Controller_Request_Http */
//require-once 'Zend/Controller/Request/Http.php';

/**
 * @category   Dkplus
 * @package    Dkplus_Controller
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Controller_Request_HttpTranslate extends Zend_Controller_Request_Http{
    /**
     * Set a userland parameter
     *
     * Uses $key to set a userland parameter. If $key is an alias, the actual
     * key will be retrieved and used to set the parameter.
     *
     * @param mixed $key
     * @param mixed $value
     * @return Zend_Controller_Request_Http
     */
    public function setParam($key, $value)
    {
		if(
			Zend_Registry::isRegistered('Zend_Translate')
		){
			if(
				false !==
					($keySearch = array_search(
							$this->getPrefix().$key,
							Zend_Registry::get('Zend_Translate')->getMessages())
					)
			){
				$key = $keySearch;
			}
		}
        return parent::setParam($key, $value);
    }

	protected $_prefix = '';

	public function setPrefix($prefix){
		$this->_prefix = (string) $prefix;
	}

	public function getPrefix(){
		return $this->_prefix;
	}
}