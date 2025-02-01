<?php

/*
Plugin Name: Advanced Custom Fields: Woocommerce Attributes
Version: 1.0.0
Author: Grampus Studio
Author URI: https://grampus-studio.ru
*/

add_action('acf/include_field_types', 'include_field_types_acf_wc_attributes');

function include_field_types_acf_wc_attributes( $version )
{
	include_once __DIR__.'/acf-woocommerce-attributes-v5.php';
}

?>
