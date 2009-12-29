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
 * @version     $Id: Form.php,v 1.1 2009/12/29 18:12:02 dkplus Exp $
 */

//require_once "Zend/JQuery/Form.php";

/**
 * Form Wrapper for dkplus extended jQuery-enabled forms
 *
 * @package    Dkplus_JQuery
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class Dkplus_JQuery_Form extends ZendX_JQuery_Form
{
    /**
     * Constructor
     *
     * @param  array|Zend_Config|null $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('Dkplus_JQuery_Form_Decorator', 'Dkplus/JQuery/Form/Decorator', Zend_Form::DECORATOR)
             ->addPrefixPath('Dkplus_JQuery_Form_Element', 'Dkplus/JQuery/Form/Element', Zend_Form::ELEMENT)
             ->addElementPrefixPath('Dkplus_JQuery_Form_Decorator', 'Dkplus/JQuery/Form/Decorator', Zend_Form::DECORATOR)
             ->addDisplayGroupPrefixPath('Dkplus_JQuery_Form_Decorator', 'Dkplus/JQuery/Form/Decorator');
        parent::__construct($options);
    }

    /**
     * Set the view object
     *
     * Ensures that the view object has the jQuery view helper path set.
     *
     * @param  Zend_View_Interface $view
     * @return Dkplus_JQuery_Form
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