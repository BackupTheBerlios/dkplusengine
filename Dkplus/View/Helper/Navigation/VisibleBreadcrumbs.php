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
 * @package    Dkplus_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_View_Helper_Navigation_Breadcrumbs
 */
//require-once 'Zend/View/Helper/Navigation/Breadcrumbs.php';

/**
 * Helper for printing breadcrumbs.
 * Displays sites that are invisible.
 *
 * @category   Dkplus
 * @package    Dkplus_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_View_Helper_Navigation_VisibleBreadcrumbs extends Zend_View_Helper_Navigation_Breadcrumbs{
    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zend_Navigation_Container $container     [optional] container to
     *                                                  operate on
     * @return Zend_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                  returns self
     */
	public function visibleBreadcrumbs(Zend_Navigation_Container $container = null){
		return $this->breadcrumbs($container);
	}

    /**
     * Determines whether a page should be accepted when iterating
     *
     * Rules:
     * - If a page is not visible, it is not accepted
     * - If helper has no ACL, page is accepted
     * - If helper has ACL, but no role, page is not accepted
     * - If helper has ACL and role:
     *  - Page is accepted if it has no resource or privilege
     *  - Page is accepted if ACL allows page's resource or privilege
     * - If page is accepted by the rules above and $recursive is true, the page
     *   will not be accepted if it is the descendant of a non-accepted page.
     *
     * @param  Zend_Navigation_Page $page      page to check
     * @param  bool                $recursive  [optional] if true, page will not
     *                                         be accepted if it is the
     *                                         descendant of a page that is not
     *                                         accepted. Default is true.
     * @return bool                            whether page should be accepted
     */
    public function accept(Zend_Navigation_Page $page, $recursive = true)
    {
        // accept by default
        $accept = true;

        if ($this->getUseAcl() && !$this->_acceptAcl($page)) {
            // acl is not amused
            $accept = false;
        }

        if ($accept && $recursive) {
            $parent = $page->getParent();
            if ($parent instanceof Zend_Navigation_Page) {
                $accept = $this->accept($parent, true);
            }
        }

        return $accept;
    }
}