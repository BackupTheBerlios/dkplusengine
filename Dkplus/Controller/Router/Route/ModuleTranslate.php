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
 * @package    Dkplus_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: ModuleTranslate.php,v 1.1 2009/12/29 18:12:02 dkplus Exp $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Abstract */
//require-once 'Zend/Controller/Router/Route/Abstract.php';

/**
 * Module Route
 *
 * Default route for module functionality
 *
 * @package    Dkplus_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Dkplus_Controller_Router_Route_ModuleTranslate extends Zend_Controller_Router_Route_Abstract
{
    /**
     * Default translator
     *
     * @var Zend_Translate
     */
    protected static $_defaultTranslator;

    /**
     * Translator
     *
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * Default locale
     *
     * @var mixed
     */
	protected static $_defaultLocale;

    /**
     * Locale
     *
     * @var mixed
     */
    protected $_locale;

    /**
     * Translate everything or not
     *
     * @var array
     */
    protected $_translatable = false;

    /**
      * URI delimiter
      */
     const URI_DELIMITER = '/';

    /**
     * Default values for the route (ie. module, controller, action, params)
     * @var array
     */
    protected $_defaults;

    protected $_values      = array();
    protected $_moduleValid = false;
    protected $_keysSet     = false;

    /**#@+
     * Array keys to use for module, controller, and action. Should be taken out of request.
     * @var string
     */
    protected $_moduleKey     = 'module';
    protected $_controllerKey = 'controller';
    protected $_actionKey     = 'action';
    /**#@-*/

    /**
     * @var Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    public function getVersion() {
        return 1;
    }

    /**
     * Instantiates route based on passed Zend_Config structure
     */
    public static function getInstance(Zend_Config $config)
    {
		$frontController = Zend_Controller_Front::getInstance();
		
        $defs         = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        $dispatcher   = $frontController->getDispatcher();
        $request      = $frontController->getRequest();
        $translatable = $config->get('translatable', false);
        if(
			$config->get('translator', null) instanceOf Zend_Config
		){
			$options = $config->get('translator')->toArray();
			if (!isset($options['data'])) {
                throw new Zend_Controller_Router_Exception('No translation source data provided.');
            }

            $adapter = isset($options['adapter']) ? $options['adapter'] : Zend_Translate::AN_ARRAY;
            $locale  = isset($options['locale'])  ? $options['locale']  : null;
            $translateOptions = isset($options['options']) ? $options['options'] : array();

            $translator = new Zend_Translate(
                $adapter, $options['data'], $locale, $translateOptions
            );
		}
		else{
			$translator = $config->get('translator', null);
		}
        $locale       = $config->get('locale', null);

        return new self($defs, $dispatcher, $request, $translatable, $translator, $locale);
    }

    /**
     * Constructor
     *
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param Zend_Controller_Dispatcher_Interface $dispatcher Dispatcher object
     * @param Zend_Controller_Request_Abstract $request Request object
     * @param bool $translatable Translate everything or not
     * @param Zend_Translate $translator Translator to use for this instance
     * @param Zend_Locale $locale
     */
    public function __construct(array $defaults = array(),
                Zend_Controller_Dispatcher_Interface $dispatcher = null,
                Zend_Controller_Request_Abstract $request = null,
                $translatable = false,
                Zend_Translate $translator = null, $locale = null)
    {
        $this->_defaults     = $defaults;

        $this->_translator   = $translator;
        $this->_locale       = $locale;
        $this->_translatable = $translatable;

        if (isset($request)) {
            $this->_request = $request;
        }

        if (isset($dispatcher)) {
            $this->_dispatcher = $dispatcher;
        }
    }

    /**
     * Set request keys based on values in request object
     *
     * @return void
     */
    protected function _setRequestKeys()
    {
        if (null !== $this->_request) {
            $this->_moduleKey     = $this->_request->getModuleKey();
            $this->_controllerKey = $this->_request->getControllerKey();
            $this->_actionKey     = $this->_request->getActionKey();
        }

        if (null !== $this->_dispatcher) {
            $this->_defaults += array(
                $this->_controllerKey => $this->_dispatcher->getDefaultControllerName(),
                $this->_actionKey     => $this->_dispatcher->getDefaultAction(),
                $this->_moduleKey     => $this->_dispatcher->getDefaultModule()
            );
        }

        $this->_keysSet = true;
    }

    /**
     * Matches a user submitted path. Assigns and returns an array of variables
     * on a successful match.
     *
     * If a request object is registered, it uses its setModuleName(),
     * setControllerName(), and setActionName() accessors to set those values.
     * Always returns the values as an array.
     *
     * @param string $path Path used to match against this routing map
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($path, $partial = false)
    {
        $this->_setRequestKeys();


        if ($this->hasTranslator()) {
			$translateMessages = $this->getTranslator()->getMessages();
        }

        $values = array();
        $params = array();

        if (!$partial) {
            $path = trim($path, self::URI_DELIMITER);
        } else {
            $matchedPath = $path;
        }

        if ($path != '') {
            $path = explode(self::URI_DELIMITER, $path);

            if(
				$this->_dispatcher
				&& (
					$this->_dispatcher->isValidModule($path[0])
					|| (
						$this->hasTranslator()
						&& !empty($translateMessages)
						&& false !== ($moduleTranslated = array_search($path[0], $translateMessages))
						&& $this->_dispatcher->isValidModule($moduleTranslated)
					)
				)
			){
                $values[$this->_moduleKey] = array_shift($path);

                if ($this->hasTranslator() && !empty($translateMessages)) {
                    $moduleTranslated = array_search($values[$this->_moduleKey], $translateMessages);
                    if (!empty($moduleTranslated))
                        $values[$this->_moduleKey] = $moduleTranslated;
                }
                $this->_moduleValid = true;
            }

            if (count($path) && !empty($path[0])) {
				$values[$this->_controllerKey] = array_shift($path);

                if ($this->hasTranslator() && !empty($translateMessages)) {
					
					$controllerTranslated = array_search($values[$this->_controllerKey], $translateMessages);
					if (!empty($controllerTranslated))
						$values[$this->_controllerKey] = $controllerTranslated;
                }
            }

            if (count($path) && !empty($path[0])) {
                $values[$this->_actionKey] = array_shift($path);

                if ($this->hasTranslator() && !empty($translateMessages)) {
                    $actionTranslated = array_search($values[$this->_actionKey], $translateMessages);
                    if (!empty($actionTranslated))
                        $values[$this->_actionKey] = $actionTranslated;
                }
            }

            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
					if (
						$this->hasTranslator() && !empty($translateMessages)
						&& false != ($keyTranslated = array_search($key, $translateMessages))
					){
						$key = $keyTranslated;
					}
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = (isset($params[$key]) ? (array_merge((array) $params[$key], array($val))): $val);
                }
            }
        }

        if ($partial) {
            $this->setMatchedPath($matchedPath);
        }

        $this->_values = $values + $params;

        return $this->_values + $this->_defaults;
    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @param bool $reset Weither to reset the current params
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = true, $partial = false)
    {
        if ($this->hasTranslator()) {
            $translator = $this->getTranslator();
            if (isset($data['@locale'])) {
                $locale = $data['@locale'];
                unset($data['@locale']);
            } else {
                $locale = $this->getLocale();
            }

        }

        if (!$this->_keysSet) {
            $this->_setRequestKeys();
        }

        $params = (!$reset) ? $this->_values : array();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $params[$key] = $value;
            } elseif (isset($params[$key])) {
                unset($params[$key]);
            }
        }

        $params += $this->_defaults;

        $url = '';

        if ($this->_moduleValid || array_key_exists($this->_moduleKey, $data)) {
            if ($params[$this->_moduleKey] != $this->_defaults[$this->_moduleKey]) {
                $module = $params[$this->_moduleKey];
                if ($this->hasTranslator()) {
                    $module = $translator->translate($module, $locale);
                }
            }
        }
        unset($params[$this->_moduleKey]);

        $controller = $params[$this->_controllerKey];
        if ($this->hasTranslator()) {
            $controller = $translator->translate($controller, $locale);
        }
        unset($params[$this->_controllerKey]);

        $action = $params[$this->_actionKey];
        if ($this->hasTranslator()) {
            $action = $translator->translate($action, $locale);
        }
        unset($params[$this->_actionKey]);

        foreach ($params as $key => $value) {
			if(
				$this->hasTranslator()
			){
				$key = $translator->translate($key, $locale);
			}
            if (is_array($value)) {
                foreach ($value as $arrayValue) {
                    if ($encode) $arrayValue = urlencode($arrayValue);
                    $url .= '/' . $key;
                    $url .= '/' . $arrayValue;
                }
            } else {
                if ($encode) $value = urlencode($value);
                $url .= '/' . $key;
                $url .= '/' . $value;
            }
        }

        if (!empty($url) || $action !== $this->_defaults[$this->_actionKey]) {
            if ($encode) $action = urlencode($action);
            $url = '/' . $action . $url;
        }

        if (!empty($url) || $controller !== $this->_defaults[$this->_controllerKey]) {
            if ($encode) $controller = urlencode($controller);
            $url = '/' . $controller . $url;
        }

        if (isset($module)) {
            if ($encode) $module = urlencode($module);
            $url = '/' . $module . $url;
        }

        return ltrim($url, self::URI_DELIMITER);
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

    /**
     * Check if translator is set
     *
     * @return bool
     */
    public function hasTranslator()
    {
        if (!$this->_translatable)
            return false;

        if ($this->_translator !== null) {
            return true;
        }

        try {
            return ($this->getTranslator() instanceof Zend_Translate);
        } catch (Zend_Controller_Router_Exception $e) {
            return false;
        }
    }

    /**
     * Set a translator
     *
     * @param  Zend_Translate $translator
     * @return void
     */
    public function setTranslator(Zend_Translate $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * Get the translator
     *
     * @throws Zend_Controller_Router_Exception When no translator can be found
     * @return Zend_Translate
     */
    public function getTranslator()
    {
        if ($this->_translator !== null) {
            return $this->_translator;
        } else if (($translator = Zend_Controller_Router_Route::getDefaultTranslator()) !== null) {
            return $translator;
        } else {
            try {
                $translator = Zend_Registry::get('Zend_Translate');
            } catch (Zend_Exception $e) {
                $translator = null;
            }

            if ($translator instanceof Zend_Translate) {
                return $translator;
            }
        }

        //require_once 'Zend/Controller/Router/Exception.php';
        throw new Zend_Controller_Router_Exception('Could not find a translator');
    }

    /**
     * Set a locale
     *
     * @param  mixed $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
    }

    /**
     * Get the locale
     *
     * @return mixed
     */
    public function getLocale()
    {
        if ($this->_locale !== null) {
            return $this->_locale;
        } else if (($locale = Zend_Controller_Router_Route::getDefaultLocale()) !== null) {
            return $locale;
        } else {
            try {
                $locale = Zend_Registry::get('Zend_Locale');
            } catch (Zend_Exception $e) {
                $locale = null;
            }

            if ($locale !== null) {
                return $locale;
            }
        }

        return null;
    }

}
