<?php
/**
 * Plugin Name: 	Checkout Field Editor for WooCommerce (Pro)
 * Plugin URI:  	https://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/
 * Description: 	Design woocommerce checkout form in your own way, customize checkout fields(Add, Edit, Delete and re arrange fields).
 * Version:     	3.3.0
 * Author:      	ThemeHigh
 * Author URI:  	https://www.themehigh.com
 * Update URI: 		https://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/
 *
 * Text Domain: 	woocommerce-checkout-field-editor-pro
 * Domain Path: 	/languages
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 6.5
 */
 
if(!defined('WPINC')){	die; }

if (!function_exists('is_woocommerce_active')){
	function is_woocommerce_active(){
	    $active_plugins = (array) get_option('active_plugins', array());
	    if(is_multisite()){
		   $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	    }
	    
	    if(in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins) || class_exists('WooCommerce')){
	        return true;
	    }else{
	        return false; 
	    }
	}
}

if(is_woocommerce_active()) {	
	define('THWCFE_VERSION', '3.3.0');
	!defined('THWCFE_SOFTWARE_TITLE') && define('THWCFE_SOFTWARE_TITLE', 'WooCommerce Checkout Field Editor');
	!defined('THWCFE_FILE_') && define('THWCFE_FILE_', __FILE__);
	!defined('THWCFE_PATH') && define('THWCFE_PATH', plugin_dir_path( __FILE__ ));
	!defined('THWCFE_URL') && define('THWCFE_URL', plugins_url( '/', __FILE__ ));
	!defined('THWCFE_BASE_NAME') && define('THWCFE_BASE_NAME', plugin_basename( __FILE__ ));

	/**
	 * The code that runs during plugin activation.
	 */
	function activate_thwcfe($network_wide) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-thwcfe-activator.php';
		THWCFE_Activator::activate($network_wide);
	}
	
	/**
	 * The code that runs during plugin deactivation.
	 */
	function deactivate_thwcfe() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-thwcfe-deactivator.php';
		THWCFE_Deactivator::deactivate();
	}
	
	register_activation_hook( __FILE__, 'activate_thwcfe' );
	register_deactivation_hook( __FILE__, 'deactivate_thwcfe' );


	function init_auto_updater_thwcfe(){
		if(!class_exists('THWCFE_License_Manager') ) {

			require_once( plugin_dir_path( __FILE__ ) . 'class-thwcfe-license-manager.php' );
			$helper_data = array(
				'api_url' => 'https://www.themehigh.com',
				'product_id' => 12,
				'product_name' => 'Checkout Field Editor for WooCommerce',
				'license_page_url' => admin_url('admin.php?page=th_checkout_field_editor_pro&tab=license_settings'), // license page URL
			);

			THWCFE_License_Manager::instance(__FILE__, $helper_data);
		}
	}
	init_auto_updater_thwcfe();


	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-thwcfe.php';
	
	/**
	 * Begins execution of the plugin.
	 */
	function run_thwcfe() {
		$plugin = new THWCFE();
		$plugin->run();
	}
	run_thwcfe();

	/**
	 * Returns helper class instance.
	 */
	function get_thwcfe_helper(){
		return new THWCFE_Functions();
	}	
}


function thwcfe_lm_to_edd_license_migration() {
	$edd_license_key = 'th_checkout_field_editor_for_woocommerce_license_data';
	$edd_license_data = get_option($edd_license_key, array());
	if(empty($edd_license_data)){
		$lm_software_title = "WooCommerce Checkout Field Editor";
		$lm_prefix = str_ireplace(array( ' ', '_', '&', '?', '-' ), '_', strtolower($lm_software_title));
		$lm_license_key = $lm_prefix . '_thlmdata';
		$lm_license_data = thwcfe_get_thlm_saved_license_data($lm_license_key);
		if($lm_license_data){
			$status = isset($lm_license_data['status']) ? $lm_license_data['status'] : '';
			if($status = 'active'){
				$new_data = array(
					'license_key' => isset($lm_license_data['license_key']) ? $lm_license_data['license_key'] : '',
					'expiry' => isset($lm_license_data['expiry_date']) ? $lm_license_data['expiry_date'] : '',
					'status' => 'valid',
				);
				$result = thwcfe_update_edd_license_data($edd_license_key, $new_data);
				if($result){
					thwcfe_delete_lm_license_data($lm_license_key);
				}
			}
		}
	}
	
}
add_action( 'admin_init', 'thwcfe_lm_to_edd_license_migration' );

function thwcfe_get_thlm_saved_license_data($key){
	$license_data = '';
	if(is_multisite()){
		$license_data = get_site_option($key);
	}else{
		$license_data = get_option($key);
	}
	return $license_data;
}

function thwcfe_update_edd_license_data($edd_license_key, $data){
	$result = false;
	if(is_multisite()){
		$result = update_site_option($edd_license_key, $data, 'no');
	}else{
		$result = update_option($edd_license_key, $data, 'no');
	}
	return $result;
}

function thwcfe_delete_lm_license_data($key){
	if(is_multisite()){
		delete_site_option($key);
	}else{
		delete_option($key);
	}
}