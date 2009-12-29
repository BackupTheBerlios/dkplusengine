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
 * @version     $Id: DynamicForm.php,v 1.1 2009/12/29 18:12:03 dkplus Exp $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
//require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Autocomplete View Helper
 *
 * @uses 	   Zend_Json, Zend_View_Helper_FormText
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class Dkplus_JQuery_View_Helper_DynamicForm extends Dkplus_JQuery_View_Helper_JsCssLoader
{
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
	public function dynamicForm($id, $value = null, array $params = array(), array $attribs = array())
	{
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
		if(
			isset($attribs['setId'])
		){
			$attribs['id'] = $attribs['setId'];
			unset($attribs['setId']);
		}

		$parent = '';
		if(
			!empty($attribs['parentLevel'])
		){
			$parent = '';
			for($i = 0; $i < $attribs['parentLevel']; ++$i){
				$parent .= '.parent()';
			}
		}
			
        if(
			(
				!isset($attribs['img_add'])
				|| !isset($attribs['img_remove'])
			)
			&& (
				!isset($attribs['remove'])
				|| !isset($attribs['add'])
			)
		){
           ////require_once "ZendX/JQuery/Exception.php";
            throw new ZendX_JQuery_Exception("Cannot construct Duplicatable field without specifying Parameters img_add and img_remove or add and remove");
        }

		$js = '';
		if(
		    isset($attribs['add'])
		){
		    $add = $attribs['add'];
		    unset($attribs['add']);
		}
		else{
		    $add = 'duplicatable-add-' . mt_rand(0, 1000);				
		    $js .= sprintf('%s("#%s")'.$parent.'.append(\'<img src="%s" alt="add" id="%s"/>\'); '."\n",
				//."\n".'%s("#%s").css("cursor", "hand");',//.css("position","absolute").css("top", "0");'."\n",
				ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
				$attribs['id'],
				$attribs['img_add'],
			    $add,
			    ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
			    $add
			);
		}
		if(
		    isset($attribs['remove'])
		){
		    $remove = $attribs['remove'];
		    unset($attribs['remove']);
		}
		else{
		    $remove = 'duplicatable-remove-' . mt_rand(0, 1000);
		    $js .= sprintf('%s("#%s")'.$parent.'.append(\'<img src="%s" alt="remove" id="%s"/>\'); '."\n",
				//."\n".'%s("#%s").css("cursor", "hand");',//.css("position","absolute").css("top", "0");'."\n",
				ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
				$attribs['id'],
				$attribs['img_remove'],
			    $remove,
			    ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
			    $remove
			);
		}
        $params = ZendX_JQuery::encodeJson($params);

        $js .= sprintf('%s("#%s")'.$parent.'.dynamicForm("#%s", "#%s", %s);'."\n",
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $add,
            $remove,
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