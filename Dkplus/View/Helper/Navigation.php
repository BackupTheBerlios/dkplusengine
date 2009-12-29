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
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_View_Helper_Navigation
 */
//require-once 'Zend/View/Helper/Navigation.php';

/**
 * Proxy helper for retrieving navigational helpers and forwarding calls
 *
 * @category   Dkplus
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_View_Helper_Navigation
    extends Zend_View_Helper_Navigation
{
    /**
     * View helper namespace
     *
     * @var string
     */
    const NS = 'Dkplus_View_Helper_Navigation';


    /**
     * Returns the helper matching $proxy
     *
     * The helper must implement the interface
     * {@link Zend_View_Helper_Navigation_Helper}.
     *
     * @param string $proxy                        helper name
     * @param bool   $strict                       [optional] whether
     *                                             exceptions should be
     *                                             thrown if something goes
     *                                             wrong. Default is true.
     * @return Zend_View_Helper_Navigation_Helper  helper instance
     * @throws Zend_Loader_PluginLoader_Exception  if $strict is true and
     *                                             helper cannot be found
     * @throws Zend_View_Exception                 if $strict is true and
     *                                             helper does not implement
     *                                             the specified interface
     */
    public function findHelper($proxy, $strict = true)
    {
		
        if (isset($this->_helpers[$proxy])) {
            return $this->_helpers[$proxy];
        }

		if (!$this->view->getPluginLoader('helper')->getPaths(parent::NS)) {
            $this->view->addHelperPath(
                    str_replace('_', '/', parent::NS),
                    parent::NS);
        }


		if (!$this->view->getPluginLoader('helper')->getPaths(self::NS)) {
            $this->view->addHelperPath(
                    str_replace('_', '/', self::NS),
                    self::NS);
        }

		return parent::findHelper($proxy, $strict);
    }

}