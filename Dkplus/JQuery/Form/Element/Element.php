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
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright   Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: Element.php,v 1.1 2009/12/29 18:12:03 dkplus Exp $
 */

/**
 * @see ZendX_JQuery_Form_Element_UiWidget
 */
//require_once "ZendX/JQuery/Form/Element/UiWidget.php";

/**
 * Base-Element
 *
 * @package    ZendX_JQuery
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class Dkplus_JQuery_Form_Element_Element extends ZendX_JQuery_Form_Element_UiWidget
{
    public $helper;

	
	/**
     * Set the view object
     *
     * Ensures that the view object has the jQuery view helper path set.
     *
     * @param  Zend_View_Interface $view
     * @return Dkplus_JQuery_Form_Element_Abstract
     */
    public function setView(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            if (false === $view->getPluginLoader('helper')->getPaths('Dkplus_JQuery_View_Helper')) {
                $view->addHelperPath('Dkplus/JQuery/View/Helper', 'Dkplus_JQuery_View_Helper');
            }
        }
        return parent::setView($view);
    }
}