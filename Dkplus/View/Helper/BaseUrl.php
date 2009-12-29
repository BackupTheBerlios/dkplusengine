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
 * Proxy helper for retrieving navigational helpers and forwarding calls
 *
 * @category   Dkplus
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_View_Helper_BaseUrl extends Zend_View_Helper_BaseUrl
{
	/**
     * Get BaseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->_baseUrl === null) {
            /** @see Zend_Controller_Front */
            //require-once 'Zend/Controller/Front.php';
            $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

            // Remove scriptname, eg. index.php from baseUrl
            $baseUrl = $this->_removeScriptName($baseUrl);
			$protocol = Zend_Controller_Front::getInstance()->getRequest()->getScheme() . '://';

            $this->setBaseUrl(
				$protocol
				. Zend_Controller_Front::getInstance()->getRequest()->getServer('SERVER_NAME')
				. $baseUrl);
        }

        return $this->_baseUrl;
    }

}