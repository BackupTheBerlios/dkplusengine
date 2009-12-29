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
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Image.php,v 1.1 2009/12/29 18:12:06 dkplus Exp $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Abstract.php */
//require-once 'Zend/View/Helper/Abstract.php';

/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_View_Helper_Image extends Zend_View_Helper_Abstract
{
    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function image($source, array $options = array(), $translate = true)
    {
		if(
			(boolean) $translate
		){
			if(isset($options['title']))
				$options['title'] = 
					$this->view->translate($options['title']);
			if(isset($options['alt']))
				$options['alt'] = 
					$this->view->translate($options['alt']);
		}
		$source = (string) $source;
		$return = '<img src="'.$source.'"';
		foreach($options AS $option => $value){
			$value = (string) $value;
			$return .= ' ' . $option . '="' . $this->view->escape($value) . '"';
		}
		$return .= '/>';
        return $return;
    }
}
