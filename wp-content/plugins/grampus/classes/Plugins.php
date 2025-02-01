<?php

namespace GSE;

class Plugins
{
	protected static $_instance = null;

	function __construct()
	{
		$PD = Core::plugin_path().'plugins/';
		global $acf;
		if(!in_array('advanced-custom-fields/advanced-custom-fields.php', apply_filters('active_plugins', get_option('active_plugins'))))
		{
			include_once $PD.'acf/acf.php';
		}
		if(isset($acf))
		{
			include_once $PD.'acf-archive/acf.php';
			include_once $PD.'acf-post-type/acf.php';
			include_once $PD.'acf-taxonomy/acf.php';
			include_once $PD.'acf-yandex-map/acf.php';
			if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
			{
				include_once $PD.'acf-wc-attributes/acf.php';
				include_once $PD.'acf-attribute-by-tax/acf.php';
			}
		}
		// add_filter( 'block_editor_rest_api_preload_paths', array($this,'acf_filter_rest_api_preload_paths'), 10, 1 );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	// function acf_filter_rest_api_preload_paths( $preload_paths )
	// {
	// 	if ( ! get_the_ID() ) {
	// 		return $preload_paths;
	// 	}
	// 	$remove_path = '/wp/v2/' . get_post_type() . 's/' . get_the_ID() . '?context=edit';
	// 	$v1 =  array_filter(
	// 		$preload_paths,
	// 		function( $url ) use ( $remove_path ) {
	// 			return $url !== $remove_path;
	// 		}
	// 	);
	// 	$remove_path = '/wp/v2/' . get_post_type() . 's/' . get_the_ID() . '/autosaves?context=edit';
	// 	return array_filter(
	// 		$v1,
	// 		function( $url ) use ( $remove_path ) {
	// 			return $url !== $remove_path;
	// 		}
	// 	);
	// }

}
return true;