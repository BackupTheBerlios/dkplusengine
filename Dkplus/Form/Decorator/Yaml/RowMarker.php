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
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    30.07.2009 23:19:28
 */

/**
 * @category   
 * @package    
 * @subpackage 
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Form_Decorator_Yaml_RowMarker extends Zend_Form_Decorator_HtmlTag{
	public function render($content){
		$markAsError = (count($this->getElement()->getMessages()) > 0);
		if(
			$markAsError
		){
			$this->setOption('class', $this->getOption('class').' error');
		}
		return parent::render($content);
	}
}