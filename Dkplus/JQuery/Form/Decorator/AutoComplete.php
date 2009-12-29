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
 * @version     $Id: AutoComplete.php,v 1.1 2009/12/29 18:12:02 dkplus Exp $
 */

/**
 * @see ZendX_JQuery_Form_Decorator_UiWidgetContainer
 */
//require_once "UiWidgetPane.php";

/**
 * Form Decorator for jQuery Accordion Pane View Helper
 *
 * @package    ZendX_JQuery
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_JQuery_Form_Decorator_AutoComplete extends Dkplus_JQuery_Form_Decorator_UiWidgetDecorator
{
    protected $_helper = "autoComplete";
}