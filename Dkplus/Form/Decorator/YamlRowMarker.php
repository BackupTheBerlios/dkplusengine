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
class Dkplus_Form_Decorator_YamlRowMarker extends Zend_Form_Decorator_HtmlTag{

	public function render($content){
		//Mark-As-Error
		if(
			$this->getElement()->hasErrors()
		){
			$this->setOption('class', $this->getOption('class').' error');
		}

		if(
			FALSE === striPos('type-', $this->getOption('class').'')
		){
			$arrElementClasses = array(
				'Zend_Form_Element_Button'			=> 'button',
				'Zend_Form_Element_Captcha'			=> 'text',
				'Zend_Form_Element_Checkbox'		=> 'check',
				'Zend_Form_Element_File'			=> 'text',
				'Zend_Form_Element_Image'			=> 'button',
				'Zend_Form_Element_MultiCheckbox'	=> 'check',
				'Zend_Form_Element_Multiselect'		=> 'select',
				'Zend_Form_Element_Password'		=> 'text',
				'Zend_Form_Element_Radio'			=> 'check',
				'Zend_Form_Element_Reset'			=> 'button',
				'Zend_Form_Element_Select'			=> 'select',
				'Zend_Form_Element_Submit'			=> 'button',
				'Zend_Form_Element_Text'			=> 'text',
				'Zend_Form_Element_Textarea'		=> 'text',
			);
			foreach($arrElementClasses AS $class => $type){
				if(
					$this->getElement() instanceOf $class
				){
					$this->setOption('class', $this->getOption('class').' type-'.$type);
					break;
				}
			}
		}
		return parent::render($content);
	}
}