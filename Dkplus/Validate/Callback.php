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
 * @package    Dkplus_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Callback.php,v 1.1 2009/12/29 18:12:06 dkplus Exp $
 */

/** Zend_Validate_Abstract */
//require-once 'Zend/Validate/Abstract.php';

/**
 * @category   Dkplus
 * @package    Dkplus_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Validate_Callback extends Zend_Validate_Abstract
{
    /**
     * Error codes
     * @const string
     */
    const CALLBACK_FAILED	= 'callbackFailed';
    const MISSING_CALLBACK	= 'missingCallback';

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::CALLBACK_FAILED	=> "This field seems not to be valid",
        self::MISSING_CALLBACK	=> 'No callback was provided to match against',
    );

    /**
     * callback
     * @var callback
     */
    protected $_callback;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     * @return void
     */
    public function __construct($callback = null)
    {
        if (null !== $callback) {
            $this->setCallback($callback);
        }
    }

    /**
     * Set callback against which to compare
     *
     * @param  callback $callback
     * @return Zend_Validate_Callback
     */
    public function setCallback($callback)
    {
        $this->_callback = $callback;
        return $this;
    }

    /**
     * Retrieve callback
     *
     * @return callback
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
		$callback = $this->getCallback();

        if ($callback === null) {
            $this->_error(self::MISSING_CALLBACK);
            return false;
        }

        if(
			!call_user_func_array($callback, array($value))
		){
            $this->_error(self::CALLBACK_FAILED);
            return false;
        }

        return true;
    }
}
