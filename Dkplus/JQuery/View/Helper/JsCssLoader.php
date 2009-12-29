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
 * @version     $Id: JsCssLoader.php,v 1.1 2009/12/29 18:12:03 dkplus Exp $
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
class Dkplus_JQuery_View_Helper_JsCssLoader extends ZendX_JQuery_View_Helper_UiWidget
{
/**
	 *
	 * @var array
	 */
	protected static $_cssFiles = array();

	/**
	 * Fügt eine CSS-Datei hinzu,
	 * @param string $file
	 */
	public static function addCssFile($file){
		self::$_cssFiles[] = (string) $file;
	}

	/**
	 * Gibt die CSS-Dateien zurück.
	 * @return array
	 */
	public static function getCssFiles(){
		return self::$_cssFiles;
	}

	/**
	 * Wurden die CSS-Dateien schon geladen?
	 * @var boolean
	 */
	protected static $_loadedCssFiles = false;

	/**
	 * Bindet die CSS-Dateien ein.
	 */
	protected static function _loadCssFiles(){
		if(
			self::$_loadedCssFiles
		){
			return;
		}
		$headLink = Zend_Layout::getMvcInstance()
			->getView()
			->headLink();
		foreach(self::getCssFiles() AS $cssFile){
			$headLink->appendStylesheet($cssFile);
		}
		self::$_loadedCssFiles = true;
	}

	/**
	 *
	 * @var array
	 */
	protected static $_jsFiles = array();

	/**
	 * Fügt eine JS-Datei hinzu.
	 * @param string $file
	 */
	public static function addJsFile($file){
		self::$_jsFiles[] = (string) $file;
	}

	/**
	 * Gibt die JS-Dateien zurück.
	 * @return array
	 */
	public static function getJsFiles(){
		return self::$_jsFiles;
	}

	/**
	 * Wurden die JS-Dateien schon geladen?
	 * @var boolean
	 */
	protected static $_loadedJsFiles = false;

	/**
	 * Bindet die JS-Dateien ein.
	 */
	protected static function _loadJsFiles(){
		if(
			self::$_loadedJsFiles
		){
			return;
		}
		$jQuery = Zend_Layout::getMvcInstance()
			->getView()
			->jQuery();
		foreach(self::getJsFiles() AS $jsFile){
			$jQuery->addJavascriptFile($jsFile);
		}
		self::$_loadedJsFiles = true;
	}

	/**
     * Constructor
     *
     * @return void
     */
    public function __construct(){
		self::_loadJsFiles();
		self::_loadCssFiles();
	}
}