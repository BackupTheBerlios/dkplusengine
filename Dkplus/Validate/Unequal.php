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
 * $element_2->addValidator('Unequal', false, array('f_name_element_1')); 
 * </code> 
 * 
 * @category   Dkplus
 * @package    Dkplus_Validate
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Validate_Unequal extends Zend_Validate_Abstract{
    /** 
     * Validation failure message key for when ...
     * @var		string 
     */ 
	const EQUAL = 'equal'; 

    /** 
     * Validation failure message template definitions 
     * 
     * @var     array 
     */ 
	protected $_messageTemplates = array( 
		self::EQUAL => 'The entered values are equal.' 
	); 
     
    /** 
     * Name of second field to validate with
     * 
     * @var     string 
     */ 
    protected $_field; 

     
    /** 
     * Constructor, sets the name of se second field that should be compared. 
     * 
     * @param	string	$field	Name of field. 
     * @return	void 
     */ 
    public function __construct($field = null){ 
        $this->setField($field); 
    } 

    /** 
     * Returns the name of the second field that should be compared with the first. 
     * 
     * @return  string Name of field.
     */ 
	public function getField(){
		return $this->_field; 
	} 

    /** 
     * Sets the name of the second field that should be compared with the first. 
     * 
     * @param   string $field Name of field
     * @return  Dkplus_Validate_Equal Provides a fluent interface. 
     */ 
    public function setField($field){ 
        $this->_field = $field; 
        return $this; 
    } 

    /**
     * Returns true if the fields have different values, otherwise false.  
     * 
     * @param   string $value Value of the field that should be compared. 
     * @param   string|array $context The values of the other fields. 
     * @return  boolean
     * @see Validate/Zend_Validate_Interface#isValid()
     */
    public function isValid($value, $context = null){ 
		$value = (string) $value; 
		$this->_setValue($value); 

		if(
			is_array($context)
		){ 
			if(
				isset($context[$this->_field])
				&& ($value == $context[$this->_field])
			){
				$this->_error(self::EQUAL); 
				return false; 
			} 
		}  
		elseif(
			is_string($context) 
			&& ($value == $context)
		){ 
			$this->_error(self::EQUAL); 
			return false; 
		} 

		return true; 
	}
} 

?>
