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
 * @version     $Id: UiWidgetDecorator.php,v 1.1 2009/12/29 18:12:03 dkplus Exp $
 */

/**
 * @see ZendX_JQuery_Form_Decorator_UiWidgetElement
 */
//require_once "ZendX/JQuery/Form/Decorator/UiWidgetElement.php";

/**
 * Abstract Form Decorator for all jQuery UI Form Elements
 *
 * @package    ZendX_JQuery
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_JQuery_Form_Decorator_UiWidgetDecorator
    extends ZendX_JQuery_Form_Decorator_UiWidgetElement
{
	/**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
		return array_merge($this->_options, array('justDecorate' => true));
    }

	protected function _retrieveJQueryParams(){
		if(
			count($this->_jQueryParams) == 0
		){
			if(
				array_key_exists('jQueryParams', $this->getOptions())
			){
				$this->_jQueryParams = $this->getOption('jQueryParams');
				unset($this->_options['jQueryParams']);
			}			
		}
	}

	/**
     * Retrieve a single jQuery option parameter
     *
     * @param  string $key
     * @return mixed|null
     */
    public function getJQueryParam($key)
    {
		$this->_retrieveJQueryParams();
        $key = (string) $key;
        if (array_key_exists($key, $this->_jQueryParams)) {
            return $this->_jQueryParams[$key];
        }

        return null;
    }

	/**
     * Get jQuery option parameters
     *
     * @return array
     */
    public function getJQueryParams()
    {
		$this->_retrieveJQueryParams();
        return $this->_jQueryParams;
    }
	
    /**
     * Retrieve element attributes
     *
     * Set id to element name and/or array item.
     *
     * @return array
     */
    public function getElementAttribs()
    {
        if (null === ($element = $this->getElement())) {
            return null;
        }

        $attribs = $element->getAttribs();
        if (isset($attribs['helper'])) {
            unset($attribs['helper']);
        }

        if (method_exists($element, 'getSeparator')) {
            if (null !== ($listsep = $element->getSeparator())) {
                $attribs['listsep'] = $listsep;
            }
        }

        if (isset($attribs['id'])) {
            return $attribs;
        }

        $id = $element->getName();

        if ($element instanceof Zend_Form_Element) {
            if (null !== ($belongsTo = $element->getBelongsTo())) {
                $belongsTo = preg_replace('/\[([^\]]+)\]/', '-$1', $belongsTo);
                $id = $belongsTo . '-' . $id;
            }
        }

        $element->setAttrib('id', $id);
        $attribs['id'] = $id;

        return $attribs;
    }

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

        $id = $element->getId();
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