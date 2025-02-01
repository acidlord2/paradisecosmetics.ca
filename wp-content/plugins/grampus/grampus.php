<?php
/*
Plugin Name: Grampus Studio 
Description: Environment package.
Version: 2.4.2
Author: Grampus Studio
Author URI: https://grampus-studio.ru/
*/

require_once 'classes/Core.php';

define('GSE_VERSION', '2.4.2');

function GSE()
{
	return GSE\Core::instance();
}

global $GS;
$GLOBALS['GSE'] = GSE();
$GS = GSE();

register_activation_hook( __FILE__, array(GSE\Core::class, 'activation') );
register_deactivation_hook( __FILE__, array(GSE\Core::class, 'deactivation') );

require_once 'classes/_export.php';
?>