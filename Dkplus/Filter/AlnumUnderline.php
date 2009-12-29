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
class Dkplus_Filter_AlnumUnderline extends Zend_Filter_Alnum{
	protected $_meansEnglishAlphabetNonStatic = false;
	
	/**
	 *
	 * @param boolean $value
	 * @return Dkplus_Filter_AlnumUnderline
	 */
	public function setMeansEnglishAlphabet($value){
		$this->_meansEnglishAlphabetNonStatic = (boolean) $value;
		return $this;
	}

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, removing all but alphabetic and digit characters
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $whiteSpace = $this->allowWhiteSpace ? '\s' : '';
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9_' . $whiteSpace . ']/';
        } else if (self::$_meansEnglishAlphabet || $this->_meansEnglishAlphabetNonStatic) {
            //The Alphabet means english alphabet.
            $pattern = '/[^a-zA-Z0-9_'  . $whiteSpace . ']/u';
        } else {
            //The Alphabet means each language's alphabet.
            $pattern = '/[^\p{L}\p{N}_' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}