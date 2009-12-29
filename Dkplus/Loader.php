<?php
class Dkplus_Loader extends Zend_Loader{
    public static function loadClass($class, $dirs = null){
        parent::loadClass($class, $dirs);
    }

    public static function autoload($class){    	
    	try{
    		if(
    			!(
    				is_file(APPLICATION_PATH.'/'.str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php')
    				&& defined('APPLICATION_PATH')
    			)
    		){
            	self::loadClass($class);
    		}
    		else{
    			self::loadClass($class, APPLICATION_PATH.'/');
    		}
            return $class;
        }
        catch(Exception $e){
           	return false;
        }
    }	
}