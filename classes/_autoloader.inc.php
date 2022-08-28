<?php
if (!defined('ABSPATH')) 
    exit;

    
//
// Global Autoloader to pre-load classes on demand.
//
spl_autoload_register(function($class)
{
    static $classes  =  null;
    if ($classes === null)
    {
        $rootPath  =  dirname(__FILE__) . '/';
        $classes   =  array(
            
            // Internal Utility Classes
            'ChildThemeName'      => $rootPath . 'ChildThemeName.php',

        );
    }
	
    if (isset($classes[$class])) {
        require $classes[$class];
    }
});