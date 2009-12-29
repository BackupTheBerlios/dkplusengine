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
 * @subpackage Dkplus_Controller_Action_Helper
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    07.04.2009 21:38:49
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Controller_Action_Helper_FlashMessenger
 */
//require-once 'Zend/Controller/Action/Helper/FlashMessenger.php';

/**
 *
 *
 * @category   Dkplus
 * @package    Dkplus_Controller
 * @subpackage Dkplus_Controller_Action_Helper
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Controller_Action_Helper_StatusFlashMessenger extends Zend_Controller_Action_Helper_FlashMessenger{
	static protected $_namespaces = array();

	/**
	 *
	 * @param string $message
	 * @param string $status
	 * @return Dkplus_Controller_Action_Helper_StatusFlashMessenger
	 */
	public function direct($message, $status = null){
		if(
			is_null($status)
		){
			return $this->addMessage($message);
		}

		$status = (string) $status;
		if(
			!isset(self::$_namespaces[$status])
		){
			throw new Exception('Status '.$status.' does not exists.');
		}
		return $this->setNamespace(self::$_namespaces[$status])->addMessage($message);
    }

	/**
	 *
	 * @param string $status
	 * @return array
	 */
	public function getStatusMessages($status = null){
		if(
			is_null($status)
		){
			return $this->getMessages();
		}

		$status = (string) $status;
		if(
			!isset(self::$_namespaces[$status])
		){
			throw new Exception('Status '.$status.' does not exists.');
		}
		return $this->setNamespace(self::$_namespaces[$status])->getMessages();
	}

	/**
	 *
	 * @param string $status
	 */
	public static function addStatus($status){
		$status = (string) $status;
		if(
			isset(self::$_namespaces[$status])
		){
			throw new Exception('The status '.$type.' has already been added..');
		}
		self::$_namespaces[strToLower($status)] = 'FlashMessanger'.ucfirst(strToLower($status));
	}

	/**
	 *
	 * @param string $status
	 * @return Dkplus_Controller_Action_Helper_StatusFlashMessenger
	 */
	public function setStatus($status){
		$status = (string) $status;
		if(
			!isset(self::$_namespaces[$status])
		){
			throw new Exception('Status '.$status.' does not exists.');
		}
		$this->setNamespace(self::$_namespaces[$status]);
		return $this;
	}
}