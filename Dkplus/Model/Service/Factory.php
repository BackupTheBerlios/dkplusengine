<?php
class Dkplus_Model_Service_Factory{
	protected static $_instances = array();
	
	protected static $_prefix = array();

	/**
	 * <p>Fügt ein Prefix hinzu.</p>
	 * <p>Das Prefix sollte, wenn es auf ein Verzeichnis verweisen soll, mit _
	 * enden.</p>
	 * @param string $prefix
	 */
	public static function addPrefix($prefix){
		$prefix = (string) $prefix;
		if(
			in_array($prefix, self::$_prefix)
		){
			throw new Dkplus_Model_Exception('Prefix '.$prefix.' has already been added.');
		}
		self::$_prefix[] = $prefix;
	}
	
	/**
	 * 
	 * @param string $type
	 * @return Dkplus_Model_Interface
	 */
	public static function get($type){
		$args = func_get_args();
		unset($args[0]);
		$args = array_values($args);
		foreach($args AS $arg){
			if(
				!is_int($arg)
				&& !is_string($arg)
				&& !is_bool($arg)
				&& !is_null($arg)
			){
				throw new Dkplus_Model_Exception('Arguments can be only strings, ints, booleans or NULL.');
			}
		}
		
		$type = (string) $type;
		$blnPrefixNotNeeded = false;
		foreach(self::$_prefix AS $prefix){
			if(
				strPos($type, $prefix) === 0
			){
				$blnPrefixNotNeeded = true;
			}			
		}
		
		if(
			$blnPrefixNotNeeded
		){			
			return self::_getObject($type, $args);
		}
		else{
			return self::_loadClass($type, $args);
		}
	}
	
	protected static function _loadClass($class, array $args){
		foreach(self::$_prefix AS $prefix){
			if(
				@class_exists($prefix.$class, TRUE)
			){
				return self::_getObject($prefix.$class, $args);
			}
		}
		throw new Dkplus_Model_Exception('Class '.$class.' could not be found.');
	} 
	
	protected static function _getObject($class, array $args){
		$instances = isset(self::$_instances[$class])
			? self::$_instances[$class]
			: array();

		//Instanzen durchgehen
		foreach($instances AS $instance){			
			if(
				count(array_diff($instance['args'], $args)) == 0
				&& count(array_diff($args, $instance['args'])) == 0
			){
				//Instanz mit identischen Werten existiert? Dann wird sie zurückgegeben
				return $instance['object'];
			}
		}
	
		//Es existiert keine Instanz, wir versuchen eine mit den Parametern anzulegen:
		if(
			method_exists($class, 'getInstance')
		){
			$instance = call_user_func_array(array($class, 'getInstance'));
		}
		elseif(
			count($args) > 1
		){
			$ref = new ReflectionClass($class);
			$instance = $ref->newInstanceArgs($args);
		}
		elseif(
			count($args) == 1
		){
			$instance = new $class($args[0]);
		}
		else{
			$instance = new $class();
		}
		
		if(
			!isset(self::$_instances[$class])
			|| count(self::$_instances[$class]) == 0
		){
			self::$_instances[$class] = array();
		}
		self::$_instances[$class][] = array(
			'args' 		=> $args,
			'object' 	=> $instance
		);
		return $instance;
	}
}