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
 * @package    Dkplus_Validate
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    07.04.2009 21:39:01
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Validate_Abstract
 */
//require-once 'Zend/Validate/Abstract.php'; 


/**
 * Class to compare two fields whether they have equal values.<br /><br /> 
 *  
 * <code> 
 * $form = new Zend_Form(); 
 *  
 * $element_1 = $form->createElement('text', 'f_name_element_1'); 
 * $element_2 = $form->createElement('text', 'f_name_element_2'); 
 * $element_2->addValidator('Equal', false, array('f_name_element_1')); 
 * </code> 
 * 
 * @category   Dkplus
 * @package    Dkplus_Validate
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Validate_Unique extends Zend_Validate_Abstract{

	protected static $_identicalValues = 0;
	protected static $_values = array();

    /** 
     * Validation failure message key for when ...
     * @var		string 
     */ 
	const NOT_UNIQUE = 'notUnique';

    /** 
     * Validation failure message template definitions 
     * 
     * @var     array 
     */
	protected $_messageTemplates = array( 
		self::NOT_UNIQUE => 'The entered value must be unique.'
	); 
     
    /**
     * Returns false if the fields have different values, otherwise true.  
     * 
     * @param   string $value Value of the field that should be compared. 
     * @param   string|array $context The values of the other fields. 
     * @return  boolean
     * @see Validate/Zend_Validate_Interface#isValid()
     */ 
    public function isValid($value, $context = null){ 
		$value = (string) $value; 
		$this->_setValue($value);

		self::$_values[] = $value;
		self::$_identicalValues = (count(self::$_values) - count(array_unique(self::$_values)));
		if(
			self::$_identicalValues > 0
		){
			$this->_error(self::NOT_UNIQUE);
				return false; 
		}

		/*if(
			is_array($context)
		){ 
			$i = 0;
			foreach($context AS $k => $v){
				if(
					$v == $value
				){
					++$i;
				}
			}
			if(
				$i > 1
			){
				$this->_error(self::NOT_UNIQUE);
				return false; 
			} 
		}  
		elseif(
			is_string($context) 
			&& ($value == $context)
		){ 
			$this->_error(self::NOT_UNIQUE);
			return false; 
		} */

		return true; 
	}
} 

?>