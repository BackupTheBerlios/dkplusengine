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
 * @package    Dkplus_Acl
 * @subpackage Adaptable
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @version    11.04.2009 11:41:35
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

 /**
  * @see Dkplus_Acl_Adaptable_Role_Registry
  */
//require-once 'Dkplus/Acl/Adaptable/Role/Registry.php';

 /**
  * @see Dkplus_Acl_Adaptable_Interface
  */
//require-once 'Dkplus/Acl/Adaptable/Interface.php';

 /**
  * @see Zend_Acl
  */
//require-once 'Zend/Acl.php';

/**
  * @see Zend_Acl_Resource
  */
//require-once 'Zend/Acl/Resource.php';

/**
  * @see Zend_Acl_Role
  */
//require-once 'Zend/Acl/Role.php';


/**
 * 
 *
 * @category   Dkplus
 * @package    Dkplus_Acl
 * @subpackage Adaptable
 * @copyright  Copyright (c) 2009 Oskar Bley <oskar@steppenhahn.de>
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dkplus_Acl_Adaptable extends Zend_Acl implements Dkplus_Acl_Adaptable_Interface{

	/**
	 * Überprüft, ob schon eine Überprüfung der zu ändernden Rollen / Resourcen
	 * stattgefunden hat.
	 * @var boolean
	 */
	protected $_cachingCalled = false;

	/**
	 * @var Dkplus_Acl_Caching_Interface
	 */
	protected $_cachingAdapter = null;

	/**
	 * @var Dkplus_Acl_Adapter_Interface
	 */
	protected $_adapter = null;

	/**
	 * <p>Enthält die geladenen Regeln.</p>
	 * @var array
	 */
	protected $_loaded = array();

	/**
	 * <p>Enthält die geladenen Rollen.</p>
	 * @var array
	 */
	protected $_loadedRoles = array();

	/**
	 * <p>Enthält die geladenen Ressourcen.</p>
	 * @var array
	 */
	protected $_loadedResources = array();
	
	/**
	 *
	 * @var Zend_Log
	 */
	protected $_logger = null;

	public function setLogger(Zend_Log $log){
		$this->_logger = $log;
	}

	/**
	 *
	 * @param string $text
	 */
	protected function _log($message, $priority){
		if(
			!is_null($this->_logger)
		){
			$this->_logger->log($message, $priority);
		}
		return $this;
	}

	/**
     * Returns the Role registry for this ACL
     *
     * If no Role registry has been created yet, a new default Role registry
     * is created and returned.
     *
     * @return Dkplus_Acl_Adaptable_Role_Registry
     */
    protected function _getRoleRegistry(){
        if (null === $this->_roleRegistry){
            $this->_roleRegistry = new Dkplus_Acl_Adaptable_Role_Registry();
        }
        return $this->_roleRegistry;
    }

	/**
	 * <p>Hook, der z.B. zum Hinzufügen des Adapters genutzt werden kann.</p>
	 * @return Dkplus_Acl_Adaptable
	 */
	protected function _init(){}

	/**
	 * <p>Liefert den Adapter zurück, der intern genutzt wird.</p>
	 * @return Dkplus_Acl_Adapter_Interface
	 */
	protected function _getAdapter(){
		if(
			!$this->hasAdapter()
		){
			throw new Zend_Acl_Exception('Adapter must be set before.');
		}
		return $this->_adapter;
	}

	/**
	 * <p>Liefert den Caching Adapter zurück, der intern genutzt wird.</p>
	 * @return Dkplus_Acl_Caching_Interface
	 */
	protected function _getCachingAdapter(){
		if(
			!$this->hasCachingAdapter()
		){
			throw new Zend_Acl_Exception('Caching-Adapter must be set before.');
		}
		return $this->_cachingAdapter;
	}

	/**
	 * Prüft, ob schon überprüft wurde, ob es etwas zu cachen gibt, und holt
	 * dies im Zweifallsfall nach.
	 * @return Dkplus_Acl_Adaptable
	 */
	protected function _checkCaching(){
		if(
			!$this->_cachingCalled
		){
			if(
				$this->hasCachingAdapter()
			){
				$changes = $this->_getCachingAdapter()->getChanges();
				foreach($changes AS $change){
					//Role und Resource wurden geändert (Regeländerung)
					if(
						!is_null($change['role'])
						AND !is_null($change['resource'])
					){
						if(
							$this->hasLoaded($change['role'], $change['resource'])
						){
							$this->_setUnloaded($change['role'], $change['resource']);
						}
					}
					//Rolle wurden geändert
					elseif(
						!is_null($change['role'])
					){
						if(
							$this->hasRoleLoaded($change['role'])
						){
							$this->_loaded = array();
							$this->_loadedResources = array();
							$this->_loadedRoles = array();
							parent::removeAll();
							parent::removeRoleAll();
						}
					}
					//Resource wurden geändert
					elseif(
						!is_null($change['resource'])
					){
						if(
							$this->hasResourceLoaded($change['resource'])
						){
							$this->_loaded = array();
							$this->_loadedResources = array();
							$this->_loadedRoles = array();
							parent::removeAll();
							parent::removeRoleAll();
						}
					}
				}
			}
		}
		$this->_cachingCalled = true;
		return $this;
	}

	/**
	 * <p>Markiert eine Regel (Rollen/Resource-Kombination) als geladen.</p>
	 * @param string $role
	 * @param string $resource
	 * @return Dkplus_Acl_Adaptable
	 */
	protected function _setLoaded($role = null, $resource = null){
		if(
			$this->hasLoaded($role, $resource)
		){
			throw new Zend_Acl_Exception('The rule with role "'.$role.'" and resource "'.$resource.'" has already been marked as loaded.');
		}
		$this->_loaded[$role][$resource] = true;
		return $this;
	}

	/**
	 * <p>Markiert eine Regel (Rollen/Resource-Kombination) als ungeladen.</p>
	 * @param string $role
	 * @param string $resource
	 * @return Dkplus_Acl_Adaptable
	 */
	protected function _setUnloaded($role = null, $resource = null){
		if(
			!$this->hasLoaded($role, $resource)
		){
			throw new Zend_Acl_Exception('The rule with role "'.$role.'" and resource "'.$resource.'" has not been marked as loaded.');
		}
		$this->_loaded[$role][$resource] = false;
		return $this;
	}

	/**
	 * <p>Markiert eine Resource als ungeladen.</p>
	 * @param string $resource
	 * @return Dkplus_Acl_Adaptable
	 * @throws {@link Zend_Acl_Exception} if the resource has not been loaded.
	 */
	protected function _setResourceUnloaded($resource){
		if(
			!$this->hasResourceLoaded($resource)
		){
			throw new Zend_Acl_Exception('Resource "'.$resource.'" has not been loaded.');
		}
		unset($this->_loadedResources[array_search($resource, $this->_loadedResources)]);
		foreach($this->_loaded AS $role => $arr){
			if(
				isset($this->_loaded[$role][$resource])
			){
				unset($this->_loaded[$role][$resource]);
			}
		}
		return $this;
	}

	/**
	 * <p>Markiert eine Rolle als ungeladen.</p>
	 * @param string $role
	 * @return Dkplus_Acl_Adaptable
	 * @throws {@link Zend_Acl_Exception} if the role has not been loaded.
	 */
	protected function _setRoleUnloaded($role){
		if(
			!$this->hasRoleLoaded($role)
		){
			throw new Zend_Acl_Exception('Role "'.$role.'" has not been loaded.');
		}
		unset($this->_loadedRoles[array_search($role, $this->_loadedRoles)]);
		if(
			isset($this->_loaded[$role])
		){
			unset($this->_loaded[$role]);
		}
		return $this;
	}

	/**
	 * <p>Markiert eine Rolle als geladen.</p>
	 * @param string $role
	 * @return Dkplus_Acl_Adaptable
	 * @throws {@link Zend_Acl_Exception} if the role has already been loaded.
	 */
	protected function _setRoleLoaded($role){
		if(
			$this->hasRoleLoaded($role)
		){
			throw new Zend_Acl_Exception('Role has already been loaded.');
		}
		$this->_loadedRoles[] = $role;
		return $this;
	}

	/**
	 * <p>Markiert eine Resource als geladen.</p>
	 * @param string $resource
	 * @return Dkplus_Acl_Adaptable
	 * @throws {@link Zend_Acl_Exception} if the resource has already been loaded.
	 */
	protected function _setResourceLoaded($resource){
		if(
			$this->hasResourceLoaded($resource)
		){
			throw new Zend_Acl_Exception('Resource has already been loaded.');
		}
		$this->_loadedResources[] = $resource;
		return $this;
	}

	/**
	 * <p>Sorgt dafür, dass der Adapter nicht mitgespeichert wird.</p>
	 * @return array
	 */
	public function __sleep(){
		return array('_resources', '_roleRegistry', '_loaded', '_loadedResources', 
			'_loadedRoles', '_rules', '_cachingAdapter');
	}

	/**
	 * <p>Ruft die {@link _init()}-Methode auf.</p>
	 * @return void
	 */
	public function __wakeup(){
		$this->_adapter = null;
		$this->_cachingCalled = false;
		$this->_init();
	}

	/**
	 * <p>Ruft die {@link _init()}-Methode auf.</p>
	 */
	public function __construct(){
		$this->_init();
		$this->_loaded = array();
		$this->_loadedResources = array();
		$this->_loadedRoles = array();
	}

	/**
	 * <p>Überprüft, ob ein Adapter gesetzt ist.</p>
	 * @return boolean
	 */
	public function hasAdapter(){
		return !is_null($this->_adapter);
	}

	/**
	 * <p>Überprüft, ob ein Caching-Adapter gesetzt ist.</p>
	 * @return boolean
	 */
	public function hasCachingAdapter(){
		return !is_null($this->_cachingAdapter);
	}
	
	/**
	 * <p>Setzt den Adapter der Klasse.</p>
	 * @param Dkplus_Acl_Adapter_Interface $adapter
	 * @return Dkplus_Acl_Adaptable_Interface
	 */
	public function setAdapter(Dkplus_Acl_Adapter_Interface $adapter){
		$this->_adapter = $adapter;
		$adapter->setAdapted($this);
		return $this;
	}

	/**
	 * <p>Setzt den Caching-Adapter der Klasse.</p>
	 * @param Dkplus_Acl_Caching_Interface $adapter
	 * @return Dkplus_Acl_Adaptable_Interface
	 */
	public function setCachingAdapter(Dkplus_Acl_Caching_Interface $adapter){
		$this->_cachingAdapter = $adapter;
		return $this;
	}
	
	/**
	 * <p>Überprüft, ob eine Rolle bereits geladen wurde.</p>
	 * @param string $role
	 * @return boolean
	 */
	public function hasRoleLoaded($role){
		return in_array($role, $this->_loadedRoles);
	}
	
	/**
	 * <p>Überprüft, ob eine Resource bereits geladen wurde.</p>
	 * @param string $resource
	 * @return boolean
	 */
	public function hasResourceLoaded($resource){
		return in_array($resource, $this->_loadedResources);
	}
	
	/**
	 * <p>Überprüft, ob eine Regel (Rollen/Resourcen-Kombination) bereits geladen wurde.</p>
	 * @param string $role
	 * @param string $resource
	 * @return boolean
	 */
	public function hasLoaded($role = null, $resource = null){
		return isset($this->_loaded[$role])
			&& isset($this->_loaded[$role][$resource])
			&& ($this->_loaded[$role][$resource] === true);
	}
	
    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     *
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend_Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     *
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  Zend_Acl_Role_Interface|string     $role
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  string                             $privilege
     * @uses   Zend_Acl::isAllowed()
     * @return boolean
     */
	public function isAllowed($role = null, $resource = null, $privilege = null){
		if(
			$this->hasCachingAdapter()
		){
			$this->_checkCaching();
		}

		$roleId = is_null($role)
			? null
			: (
				$role instanceOf Zend_Acl_Role_Interface
				? $role->getRoleId()
				: (string) $role
			);
			
		$resourceId = is_null($resource)
			? null
			: (
				$resource instanceOf Zend_Acl_Resource_Interface
				? $resource->getResourceId()
				: (string) $resource
			);
				
		//Nachladen
		if(
			!$this->hasLoaded($roleId, $resourceId)
		){
			//Laden der Daten
			$arrLoaded = $this->_getAdapter()->load($roleId, $resourceId);			
			
			//Hinzufügen der Roles
			$arrRoles = $arrLoaded[0];
			foreach($arrRoles AS $mixRole){
				if(
					is_array($mixRole)
					&& !parent::hasRole($mixRole[0])
				){
					$this->_log('ACL: Role added: '.$mixRole[0], Zend_Log::INFO);
					if(
						is_array($mixRole[1])
					){
						foreach($mixRole[1] AS $parent){
							$this->_log('ACL: Parent: '.$parent, Zend_Log::INFO);
						}
					}
					$this->_setRoleLoaded($mixRole[0]);
					parent::addRole(new Zend_Acl_Role($mixRole[0]), $mixRole[1]);			
				}
				elseif(
					!is_array($mixRole)
					&& !parent::hasRole($mixRole)
				){
					$this->_log('ACL: Role added: '.$mixRole, Zend_Log::INFO);
					$this->_setRoleLoaded($mixRole);
					parent::addRole(new Zend_Acl_Role($mixRole));
				}
			}			
			
			//Hinzufügen der Resources
			$arrResources = $arrLoaded[1];
			foreach($arrResources AS $mixResource){
				if(
					is_array($mixResource)
					&& !parent::has($mixResource[0])
				){
					$this->_log('ACL: Resource added: '.$mixResource[0], Zend_Log::INFO);
					if(
						is_array($mixResource[1])
					){
						foreach($mixResource[1] AS $parent){
							$this->_log('ACL: Parent: '.$parent, Zend_Log::INFO);
						}
					}
					$this->_setResourceLoaded($mixResource[0]);
					parent::add(new Zend_Acl_Resource($mixResource[0]), $mixResource[1]);
				}
				elseif(
					!is_array($mixResource)
					&& !parent::has($mixResource)
				){
					$this->_log('ACL: Resource added: '.$mixResource, Zend_Log::INFO);
					$this->_setResourceLoaded($mixResource);
					parent::add(new Zend_Acl_Resource($mixResource));
				}
			}
			
			//Hinzufügen der Regeln
			$arrRules = $arrLoaded[2];
			foreach($arrRules AS $arrRule){				
				if(
					!$this->hasLoaded($arrRule['role'], $arrRule['resource'])
				){
					$this->_setLoaded($arrRule['role'], $arrRule['resource']);
				}
				if(
					$arrRule['type'] == 1
				){
					/*Zend_Debug::dump(
						'allow( '
						. $arrRule['role']
						. ', '						
						. $arrRule['resource']
						. ', '
						. (isset($arrRule['privilege'])
							? $arrRule['privilege']
							: null)
						.' )'
					);*/
					$this->_log(
						'ACL: Allow: '
						. $arrRule['role'] . ', '
						. $arrRule['resource'] . ', '
						. (isset($arrRule['privilege']) ? $arrRule['privilege'] : 'null')
						. ', '
						. (empty($arrRule['assert']) ? 'null' : $arrRule['assert']),
						Zend_Log::INFO
					);
					parent::allow(
						$arrRule['role'],
						$arrRule['resource'],
						isset($arrRule['privilege']) ? $arrRule['privilege'] : null,
						empty($arrRule['assert']) ? null : new $arrRule['assert']()
					);
				}
				else{
					/*Zend_Debug::dump(
						'deny( '
						. $arrRule['role']
						. ', '
						. $arrRule['resource']
						. ', '
						. (isset($arrRule['privilege'])
							? $arrRule['privilege']
							: null)
						.' )'
					);*/
					$this->_log(
						'ACL: Allow: '
						. $arrRule['role'] . ', '
						. $arrRule['resource'] . ', '
						. (isset($arrRule['privilege']) ? $arrRule['privilege'] : 'null')
						. ', '
						. (empty($arrRule['assert']) ? 'null' : $arrRule['assert']),
						Zend_Log::INFO
					);
					parent::deny(
						$arrRule['role'],
						$arrRule['resource'],
						isset($arrRule['privilege']) ? $arrRule['privilege'] : null,
						empty($arrRule['assert']) ? null : new $arrRule['assert']()
					);
				}
			}
			if(
				!$this->hasLoaded($roleId, $resourceId)
			){
				$this->_setLoaded($roleId, $resourceId);
			}
		}
		return parent::isAllowed($role, $resource, $privilege);
	}

	/**
     * Returns true if and only if the Role exists in the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl::hasRole()
     * @return boolean
     */
	public function hasRole($role){
		if(
			$this->hasCachingAdapter()
		){
			$this->_checkCaching();
		}

		//Wenn die Role angegeben wurde und nicht im Acl-Object existiert 
		//und noch kein Ladeversuch unternommen wurde, wird versucht die Role zu laden.
		$roleId = $role instanceof Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (string) $role;
		if(
			!is_null($roleId)
			&& !parent::hasRole($roleId)
			&& !$this->hasRoleLoaded($roleId)
		){
			$arrRoles = $this->_getAdapter()->loadRole($roleId);
			if(
				count($arrRoles) > 0
			){
				foreach($arrRoles AS $arrRole){
					if(
						$this->hasRoleLoaded($arrRole[0])
					){
						continue;
					}
					$this->_setRoleLoaded($arrRole[0]);
					parent::addRole(new Zend_Acl_Role($arrRole[0]), $arrRole[1]);
				}
			}
			else{
				$this->_setRoleLoaded($roleId);
			}
		}
		return parent::hasRole($roleId);
	}
	
	/**
     * Returns true if and only if the Resource exists in the ACL
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @return boolean
     */
	public function has($resource){
		if(
			$this->hasCachingAdapter()
		){
			$this->_checkCaching();
		}

		//Wenn die Resource angegeben wurde und nicht im Acl-Object existiert 
		//und noch kein Ladeversuch unternommen wurde, wird versucht die Resource zu laden.
		$resourceId = $resource instanceof Zend_Acl_Resource_Interface
			? $resource->getResourceId()
			: (string) $resource; 
		if(
			!is_null($resourceId)
			&& !parent::has($resourceId)
			&& !$this->hasResourceLoaded($resourceId)
		){
			$arrResources = $this->_getAdapter()->loadResource($resourceId);
			if(
				count($arrResources) > 0
			){
				foreach($arrResources AS $arrResource){
					if(
						$this->hasResourceLoaded($arrResource[0])
					){
						continue;
					}
					$this->_setResourceLoaded($arrResource[0]);
					parent::add(new Zend_Acl_Resource($arrResource[0]), $arrResource[1]);
				}
			}
			else{
				$this->_setResourceLoaded($resourceId);
			}
		}
		return parent::has($resourceId);
	}

	/**
     * Adds a Resource having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing Resource from which the newly added Resource will inherit.
     *
     * @param  Zend_Acl_Resource_Interface        $resource
     * @param  Zend_Acl_Resource_Interface|string $parent
     * @return Zend_Acl Provides a fluent interface
	 * @throws {@link Zend_Acl_Exception} if the parent resource does not exist, if the resource has already been added or if the resource could not be added.
	 */
	public function add(Zend_Acl_Resource_Interface $resource, $parent = null){
		$resourceId = $resource->getResourceId();
		if(
			$this->has($resourceId)
		){
			throw new Zend_Acl_Exception('Resource "'.$resourceId.'" already exists in the ACL.');
		}
		if(
			!is_null($parent)
			&& !$this->has($parent)
		){
			throw new Zend_Acl_Exception('Parent Resource "'.($parent instanceOf Zend_Acl_Resource_Interface ? $parent->getResourceId() :((string) $parent)).'" does not exist.');
		}		
		$this->_getAdapter()->addResource($resource, $parent);
		$this->_setResourceUnloaded($resourceId);
		
		if(
			!$this->has($resource)
		){
			throw new Zend_Acl_Exception('Resource "'.$resourceId.'" could not be added to the ACL.');
		}

		//Resource zum Cachen freigeben:
		if(
			$this->hasCachingAdapter()
		){
			$this->_getCachingAdapter()->change(null, $resourceId);
		}
		return $this;
	}
	
    /**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  Zend_Acl_Role_Interface              $role
     * @param  Zend_Acl_Role_Interface|string|array $parents
     * @uses   Zend_Acl::add()
     * @return Zend_Acl Provides a fluent interface
     */
	public function addRole(Zend_Acl_Role_Interface $role, $parents = null){
		$roleId = $role->getRoleId();
		if(
			$this->hasRole($role)
		){
			throw new Zend_Acl_Exception('Role "'.$roleId.'" already exists in the ACL.');
		}
		
		if(
			is_array($parents)
		){
			throw new Zend_Acl_Exception('An array of parent roles is not yet supported by this adapter.');
		}
		
		if(
			!is_null($parents)
			&& !$this->hasRole($parents)
		){
			throw new Zend_Acl_Exception('Parent Role "'.($parents instanceOf Zend_Acl_Role_Interface ? $parents->getRoleId() :((string) $parents)).'" does not exist.');
		}		
		$this->_getAdapter()->addRole($role, $parents);
		$this->_setRoleUnloaded($roleId);
		
		if(
			!$this->hasRole($role)
		){
			throw new Zend_Acl_Exception('Role "'.$roleId.'" could not be added to the ACL.');
		}

		//Role zum Cachen freigeben:
		if(
			$this->hasCachingAdapter()
		){
			$this->_getCachingAdapter()->change($roleId, null);
		}
		return $this;
	}

	/**
     * Adds an "allow" rule to the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @uses   Zend_Acl::allow()
     * @return Zend_Acl Provides a fluent interface
     */
	public function allow($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null){
		$this->_getAdapter()->allow($roles, $resources, $privileges, $assert);
		$roles = is_array($roles) ? $roles : array($roles);
		$roles = count($roles) == 0 ? array(null) : $roles;
		foreach($roles AS $k => $v){
			$roles[$k] = $v instanceOf Zend_Acl_Role_Interface
				? $v->getRoleId() 
				: (
					is_null($v)
					? null
					: strVal($v)
				);
		}
		$resources = is_array($resources) ? $resources : array($resources);
		$resources = count($resources) == 0 ? array(null) : $resources;
		foreach($resources AS $k => $v){
			$resources[$k] = $v instanceOf Zend_Acl_Resource_Interface
				? $v->getResourceId() 
				: (
					is_null($v)
					? null
					: strVal($v)
				);
		}
		foreach($roles AS $role){
			foreach($resources AS $resource){
				//Zum Cachen freigeben:
				if(
					$this->hasCachingAdapter()
				){
					$this->_getCachingAdapter()->change($role, $resource);
				}

				//Als ungeladen markieren
				if(
					$this->hasLoaded($role, $resource)
				){
					$this->_setUnloaded($role, $resource);
				}
			}
		}
		return $this;
	}

    /**
     * Adds a "deny" rule to the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @uses   Zend_Acl::deny()
     * @return Zend_Acl Provides a fluent interface
     */
	public function deny($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null){
		$this->_getAdapter()->deny($roles, $resources, $privileges, $assert);
		$roles = is_array($roles) ? $roles : array($roles);
		$roles = count($roles) == 0 ? array(null) : $roles;
		foreach($roles AS $k => $v){
			$roles[$k] = $v instanceOf Zend_Acl_Role_Interface
				? $v->getRoleId() 
				: (
					is_null($v)
					? null
					: strVal($v)
				);
		}
		$resources = is_array($resources) ? $resources : array($resources);
		$resources = count($resources) == 0 ? array(null) : $resources;
		foreach($resources AS $k => $v){
			$resources[$k] = $v instanceOf Zend_Acl_Resource_Interface
				? $v->getResourceId() 
				: (
					is_null($v)
					? null
					: strVal($v)
				);
		}
		foreach($roles AS $role){
			foreach($resources AS $resource){
				//Zum Cachen freigeben:
				if(
					$this->hasCachingAdapter()
				){
					$this->_getCachingAdapter()->change($role, $resource);
				}

				//Als ungeladen markieren
				if(
					$this->hasLoaded($role, $resource)
				){
					$this->_setUnloaded($role, $resource);
				}
			}
		}
		return $this;
	}

	/**
     * Removes the Role from the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl::removeRole()
     * @return Zend_Acl Provides a fluent interface
     */
	public function removeRole($role){
		if(
			$this->hasCachingAdapter()
		){
			$this->_checkCaching();
		}
		
		$roleId = $role instanceOf Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (string) $role;
		$this->_setRoleUnloaded($roleId);
		$arrRoles = $this->_getAdapter()->removeRole($roleId);
		foreach($arrRoles AS $role){
			if(
				$this->hasRole($role)
				&& $role != $roleId
			){
				parent::removeRole($role);
			}
			if(
				$this->hasRoleLoaded($role)
			){
				$this->_setRoleUnloaded($role);
			}

			//Zum Cachen freigeben
			if(
				$this->hasCachingAdapter()
			){
				$this->_getCachingAdapter()->change($role, null);
			}
		}

		//Zum Cachen freigeben:
		if(
			$this->hasCachingAdapter()
		){
			$this->_getCachingAdapter()->change($roleId, null);
		}
		return parent::removeRole($roleId);
	}
	
    /**
     * Removes a Resource and all of its children
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @throws {@link Zend_Acl_Exception}
     * @return Zend_Acl Provides a fluent interface
     */
	public function remove($resource){
		if(
			$this->hasCachingAdapter()
		){
			$this->_checkCaching();
		}

		$resourceId = $resource instanceOf Zend_Acl_Resource_Interface
			? $resource->getResourceId()
			: (string) $resource;
		$this->_setResourceUnloaded($resourceId);
		$arrResources = $this->_getAdapter()->removeResource($resourceId);
		foreach($arrResources AS $resource){
			if(
				$this->has($resource)
				&& $resource != $resourceId
			){
				parent::remove($role);
			}
			if(
				$this->hasResourceLoaded($resource)
			){
				$this->_setResourceUnloaded($resource);
			}

			//Zum Cachen freigeben:
			if(
				$this->hasCachingAdapter()
			){
				$this->_getCachingAdapter()->change(null, $resource);
			}
		}

		//Zum Cachen freigeben:
		if(
			$this->hasCachingAdapter()
		){
			$this->_getCachingAdapter()->change(null, $resourceId);
		}
		return parent::remove($resourceId);		
	}

	/**
	 * @throws {@link Zend_Acl_Exception}
	 */
	public function removeAll(){
		/**
		 * @see Zend_Acl_Exception
		 */
		//require-once 'Zend/Acl/Exception.php';

		throw new Zend_Acl_Exception('Method '.__METHOD__.' is not yet implemented.');
	}

	/**
	 * @throws {@link Zend_Acl_Exception}
	 */
	public function removeRoleAll(){
		/**
		 * @see Zend_Acl_Exception
		 */
		//require-once 'Zend/Acl/Exception.php';

		throw new Zend_Acl_Exception('Method '.__METHOD__.' is not yet implemented.');
	}

	/**
	 * <p>Fügt einer Rolle eine neue Eltern-Rolle hinzu.</p>
	 * @param string|Zend_Acl_Role $role
	 * @param string|Zend_Acl_Role $parentRole
	 * @return Dkplus_Acl_Adaptable_Interface
	 * @throws {@link Zend_Acl_Exception}
	 */
	public function addParentRole($role, $parentRole){
		if(
			!$this->hasRole($role)
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require-once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('There is no role id '.$role);
		}

		if(
			!$this->hasRole($parentRole)
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require-once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('There is no role id '.$parentRole);
		}
		$role = $role instanceOf Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (string) $role;
		$parentRole = $parentRole instanceOf Zend_Acl_Role_Interface
			? $parentRole->getRoleId()
			: (string) $parentRole;
		$this->_getRoleRegistry()->addParentRole($role, $parentRole);
		$this->_getAdapter()->addParentRole($role, $parentRole);		
		unset($this->_loaded[$role]);

		//Zum Cachen freigeben:
		if(
			$this->hasCachingAdapter()
		){
			$this->_getCachingAdapter()->change($role, null);
		}
		return $this;
	}

	/**
	 * <p>Entfernt eine Eltern-Rolle von einer Rolle.</p>
	 * @param string|Zend_Acl_Role $role
	 * @param string|Zend_Acl_Role $parentRole
	 * @return Dkplus_Acl_Adaptable_Interface
	 * @throws {@link Zend_Acl_Exception}
	 */
	public function removeParentRole($role, $parentRole){
		if(
			!$this->hasRole($role)
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require-once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('There is no role id '.$role);
		}

		if(
			!$this->hasRole($parentRole)
		){
			/**
			 * @see Zend_Acl_Exception
			 */
			//require-once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception('There is no role id '.$parentRole);
		}

		$role = $role instanceOf Zend_Acl_Role_Interface
			? $role->getRoleId()
			: (string) $role;
		$parentRole = $parentRole instanceOf Zend_Acl_Role_Interface
			? $parentRole->getRoleId()
			: (string) $parentRole;
		$this->_getRoleRegistry()->removeParentRole($role, $parentRole);
		$this->_getAdapter()->removeParentRole($role, $parentRole);

		//Zum Cachen freigeben:
		if(
			$this->hasCachingAdapter()
		){
			$this->_getCachingAdapter()->change($role, null);
		}
		return $this;
	}
}
