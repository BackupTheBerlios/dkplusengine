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
 * @version     $Id: LabelTooltip.php,v 1.1 2009/12/29 18:12:03 dkplus Exp $
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
class Dkplus_JQuery_Form_Decorator_LabelTooltip extends Dkplus_JQuery_Form_Decorator_UiWidgetDecorator
{
    protected $_helper = "tooltip";

	/**
     * Render an jQuery UI Widget element using its associated view helper
     *
     * @param  string $content
     * @return string
     * @throws Zend_Form_Decorator_Exception if element or view are not registered
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
           ////require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('UiWidgetElement decorator cannot render without a registered view object');
        }

        $jQueryParams = $this->getJQueryParams();

        $helper    = $this->getHelper();
        $separator = $this->getSeparator();
        $value     = $this->getValue($element);
		$attribs   = $this->getOptions();
        $name      = $element->getFullyQualifiedName();

		$id = $element->getName() . '-label';
        $attribs['id'] = $id;

        $elementContent = $view->$helper($name, $value, $jQueryParams, $attribs);
        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $separator . $elementContent;
            case self::PREPEND:
                return $elementContent . $separator . $content;
            default:
                return $elementContent;
        }
    }
}