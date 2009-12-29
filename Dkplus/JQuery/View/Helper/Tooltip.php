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
 * @version     $Id: Tooltip.php,v 1.1 2009/12/29 18:12:03 dkplus Exp $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
//require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Tooltip View Helper
 *
 * @uses 	   Zend_Json, Zend_View_Helper_FormText
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class Dkplus_JQuery_View_Helper_Tooltip extends Dkplus_JQuery_View_Helper_JsCssLoader
{
	/**
	 * @var array
	 */
	protected static $_defaults = array();

	/**
	 * Setzt die Standard-Werte.
	 * @param array $defaults
	 */
	public static function setDefaults(array $defaults){
		self::$_defaults = $defaults;
	}


    /**
     * Builds an AutoComplete ready input field.
     *
     * This view helper builds an input field with the {@link Zend_View_Helper_FormText} FormText
     * Helper and adds additional javascript to the jQuery stack to initialize an AutoComplete
     * field. Make sure you have set one out of the two following options: $params['data'] or
     * $params['url']. The first one accepts an array as data input to the autoComplete, the
     * second accepts an url, where the autoComplete content is returned from. For the format
     * see jQuery documentation.
     *
     * @link   http://docs.jquery.com/UI/Autocomplete
     * @throws ZendX_JQuery_Exception
     * @param  String $id
     * @param  String $value
     * @param  array $params
     * @param  array $attribs
     * @return String
     */
	public function tooltip($id, $value = null, array $params = array(), array $attribs = array())
	{
		if(
			is_array($value)
			&& count($params) == 0
			&& count($attribs) == 0
		){
			$params = $value;
			$value = null;
		}
		elseif(
			is_string($value)
			AND !isset($params['content'])
		){
			$params['content'] = $value;
		}
		
		$params = array_merge(self::$_defaults, $params);

        $attribs = $this->_prepareAttributes($id, $value, $attribs);        

		if(
			( !isset($attribs['translate']) || $attribs['translate'] )
			&& isset($params['content'])
		){
			$params['content'] = $this->view->translate($params['content']);
		}

		$params = ZendX_JQuery::encodeJson($params);

		if(isset($attribs['translate']))
			unset($attribs['translate']);


        $js = sprintf('%s("#%s").wTooltip(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );

        $this->jquery->addOnLoad($js);
		if(
			isset($attribs['justDecorate'])
			&& $attribs['justDecorate']
		){
			return '';
		}
		if(isset($attribs['justDecorate']))
			unset($attribs['justDecorate']);
        return $this->view->formText($id, $value, $attribs);
	}
}