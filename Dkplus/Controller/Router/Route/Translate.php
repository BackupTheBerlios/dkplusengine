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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Translate.php,v 1.1 2009/12/29 18:12:02 dkplus Exp $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Abstract */
//require-once 'Zend/Controller/Router/Route/Abstract.php';

/**
 * Route
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Dkplus_Controller_Router_Route_Translate extends Zend_Controller_Router_Route
{
    /**
     * Default translator
     *
     * @var Zend_Translate
     */
    protected static $_defaultTranslator;
    
    /**
     * Default locale
     *
     * @var mixed
     */
    protected static $_defaultLocale;
    
    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        $reqs = ($config->reqs instanceof Zend_Config) ? $config->reqs->toArray() : array();
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs, $reqs);
    }

    

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path, $partial = false)
    {
        if ($this->_isTranslated) {
            $translateMessages = $this->getTranslator()->getMessages();
        }
        
        $pathStaticCount = 0;
        $values          = array();
        $matchedPath     = '';
        
        if (!$partial) {
            $path = trim($path, $this->_urlDelimiter);
        }
        
        if ($path !== '') {
            $path = explode($this->_urlDelimiter, $path);

            foreach ($path as $pos => $pathPart) {
                // Path is longer than a route, it's not a match
                if (!array_key_exists($pos, $this->_parts)) {
                    if ($partial) {
                        break;
                    } else {
                        return false;
                    }
                }
                
                $matchedPath .= $pathPart . $this->_urlDelimiter;
                
                // If it's a wildcard, get the rest of URL as wildcard data and stop matching
                if ($this->_parts[$pos] == '*') {
                    $count = count($path);
                    for($i = $pos; $i < $count; $i+=2) {
                        $var = urldecode($path[$i]);
						if(
							!empty($translateMessages)
							&& ($newVar = array_search($var, $translateMessages))
								!== false
						){
							$var = $newVar;
						}
                        if (!isset($this->_wildcardData[$var]) && !isset($this->_defaults[$var]) && !isset($values[$var])) {
                            $this->_wildcardData[$var] = (isset($path[$i+1])) ? urldecode($path[$i+1]) : null;
							$matchedPath .= $i == $pos
								? $path[$i+1] . $this->_urlDelimiter
								: $path[$i] . $this->_urlDelimiter . $path[$i+1] . $this->_urlDelimiter;
                        }
                    }
                    break;
                }

                $name     = isset($this->_variables[$pos]) ? $this->_variables[$pos] : null;
                $pathPart = urldecode($pathPart);

                // Translate value if required
                $part = $this->_parts[$pos];
                if ($this->_isTranslated && (substr($part, 0, 1) === '@' && $name === null) || $name !== null && in_array($name, $this->_translatable)) {
                    if (substr($part, 0, 1) === '@') {
                        $part = substr($part, 1);
                    }

                    if (($originalPathPart = array_search($pathPart, $translateMessages)) !== false) {
                        $pathPart = $originalPathPart;
                    }
                }

                // If it's a static part, match directly
                if ($name === null && $part != $pathPart) {
                    return false;
                }

                // If it's a variable with requirement, match a regex. If not - everything matches
                if ($part !== null && !preg_match($this->_regexDelimiter . '^' . $part . '$' . $this->_regexDelimiter . 'iu', $pathPart)) {
                    return false;
                }

                // If it's a variable store it's value for later
                if ($name !== null) {
                    $values[$name] = $pathPart;
                } else {
                    $pathStaticCount++;
                }  
            }
        }

        // Check if all static mappings have been matched
        if ($this->_staticCount != $pathStaticCount) {
            return false;
        }

        $return = $values + $this->_wildcardData + $this->_defaults;

        // Check if all map variables have been initialized
        foreach ($this->_variables as $var) {
            if (!array_key_exists($var, $return)) {
                return false;
            }
        }
        
        $this->setMatchedPath(rtrim($matchedPath, $this->_urlDelimiter));

        $this->_values = $values;
        
        return $return;

    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param  array $data An array of variable and value pairs used as parameters
     * @param  boolean $reset Whether or not to set route defaults with those provided in $data
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false)
    {
        if ($this->_isTranslated) {
            $translator = $this->getTranslator();
            
            if (isset($data['@locale'])) {
                $locale = $data['@locale'];
                unset($data['@locale']);
            } else {
                $locale = $this->getLocale();
            }
        }
        
        $url  = array();
        $flag = false;
        foreach ($this->_parts as $key => $part) {
            $name = isset($this->_variables[$key]) ? $this->_variables[$key] : null;

            $useDefault = false;
            if (isset($name) && array_key_exists($name, $data) && $data[$name] === null) {
                $useDefault = true;
            }

            if (isset($name)) {
                if (isset($data[$name]) && !$useDefault) {
                    $value = $data[$name];
                    unset($data[$name]);
                } elseif (!$reset && !$useDefault && isset($this->_values[$name])) {
                    $value = $this->_values[$name];
                } elseif (!$reset && !$useDefault && isset($this->_wildcardData[$name])) {
                    $value = $this->_wildcardData[$name];
                } elseif (isset($this->_defaults[$name])) {
                    $value = $this->_defaults[$name];
                } else {
                    //require-once 'Zend/Controller/Router/Exception.php';
                    throw new Zend_Controller_Router_Exception($name . ' is not specified');
                }
                
                if ($this->_isTranslated && in_array($name, $this->_translatable)) {
                    $url[$key] = $translator->translate($value, $locale);
                } else {
                    $url[$key] = $value;
                } 
            } elseif ($part != '*') {
                if ($this->_isTranslated && substr($part, 0, 1) === '@') {
                    $url[$key] = $translator->translate(substr($part, 1), $locale);
                } else {
                    $url[$key] = $part;
                }
            } else {
                if (!$reset) $data += $this->_wildcardData;
                foreach ($data as $var => $value) {
                    if ($value !== null) {
						//Übersetzen der Keys
                        $url[$key++] = $this->getTranslator()->translate($var);
                        $url[$key++] = $value;
                        $flag = true;
                    }
                }
            }
        }

        $return = '';

        foreach (array_reverse($url, true) as $key => $value) {
            $defaultValue = null;
            
            if (isset($this->_variables[$key])) {
                $defaultValue = $this->getDefault($this->_variables[$key]);
               
                if ($this->_isTranslated && $defaultValue !== null && isset($this->_translatable[$this->_variables[$key]])) {
                    $defaultValue = $translator->translate($defaultValue, $locale);
                }
            }
                
            if ($flag || $value !== $defaultValue) {
                if ($encode) $value = urlencode($value);
                $return = $this->_urlDelimiter . $value . $return;
                $flag = true;
            }
        }

        return trim($return, $this->_urlDelimiter);

    }

}
