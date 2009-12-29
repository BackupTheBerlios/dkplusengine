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
 * @package    Dkplus_Filter
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    29.04.2009 08:56:59
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Strips the slashes from an input.
 *
 * @category   Dkplus
 * @package    Dkplus_Filter
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Filter_StripSlashes implements Zend_Filter_Interface{
	/**
	 * @see Filter/Zend_Filter_Interface#filter()
	 */
	public function filter($value){
		return stripslashes($value);
	}
}