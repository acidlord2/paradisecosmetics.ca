<?php

/*
Plugin Name: Advanced Custom Fields: Woocommerce Attribute Values
Description: Requires "Advanced Custom Fields: Woocommerce Attributes" plugin
Version: 1.0.0
Author: Grampus Studio
Author URI: https://grampus-studio.ru
*/

add_action('acf/include_field_types', 'include_field_types_acf_attribute_by_tax');

function include_field_types_acf_attribute_by_tax( $version )
{
	include_once __DIR__.'/acf-woocommerce-attribute-by-tax-v5.php';
}

?>
