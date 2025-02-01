<?php
/**
 * Plugin Name: WooCommerce Избранные товары
 * Description: Избранные товары
 * Version: 1.0.0
 * Author: Grampus Studio
 * Author URI: https://grampus-studio.ru/
 */
defined( 'ABSPATH' ) || exit;

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
{
	/**
	 * WC_Favorites_Grampus class.
	 */
	class WC_Favorites_Grampus
	{
		protected static $_instance = null;

		private static $FAVORITES;

		function __construct()
		{
			self::initSession();

			add_filter('woocommerce_get_settings_pages', array($this, 'add_woocommerce_settings_page') );

			add_action( 'wp_ajax_woocommerce_add_to_favorites', array( $this, 'favorites_items_add' ) );
			add_action( 'wp_ajax_nopriv_woocommerce_add_to_favorites', array( $this, 'favorites_items_add' ) );
			add_action( 'wc_ajax_add_to_favorites', array( $this, 'favorites_items_add' ) );

			add_action( 'wp_ajax_woocommerce_remove_from_favorites', array( $this, 'favorites_items_remove' ) );
			add_action( 'wp_ajax_nopriv_woocommerce_remove_from_favorites', array( $this, 'favorites_items_remove' ) );
			add_action( 'wc_ajax_remove_from_favorites', array( $this, 'favorites_items_remove' ) );

			add_action( 'wp_ajax_woocommerce_get_refreshed_favorites', array( $this, 'response' ) );
			add_action( 'wp_ajax_nopriv_woocommerce_get_refreshed_favorites', array( $this, 'response' ) );
			add_action( 'wc_ajax_get_refreshed_favorites', array( $this, 'response' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
			add_action( 'widgets_init', array($this, 'init_widget') );
			add_filter( 'display_post_states', array($this, 'register_post_states'), 10, 2);

			add_filter( 'woocommerce_add_to_favorites_fragments', array($this, 'add_notifications') );

			add_shortcode( 'favorite_items', array($this,'render_favorites_page') );
			add_shortcode( 'add_to_favorites', array($this,'add_to_favorites_shortcode') );

			$favorites_single_product_position = get_option('favorites_single_product_position','no');
			if($favorites_single_product_position != 'no')
			{
				$favorites_single_product_order = get_option('favorites_single_product_order',10);
				add_action('woocommerce_'.$favorites_single_product_position, array( $this, 'render_single_product'), $favorites_single_product_order);
			}

			$favorites_category_product_position = get_option('favorites_category_product_position','no');
			if($favorites_category_product_position != 'no')
			{
				$favorites_category_product_order = get_option('favorites_category_product_order',10);
				add_action('woocommerce_'.$favorites_category_product_position, array( $this, 'render_category_product'), $favorites_category_product_order);
			}

			add_filter( 'template_include', array($this, 'favorites_page_template'), 19 );


		}

		public static function instance()
		{
			if(is_null(self::$_instance))
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function get_plugin_path()
		{
			return untrailingslashit( plugin_dir_path(__FILE__) );
		}

		public function get_plugin_url()
		{
			return untrailingslashit( plugins_url('/',__FILE__) );
		}

		public function get_templates_path()
		{
			return $this->get_plugin_path().'/templates/';
		}

		public function add_notifications($fragments)
		{
			ob_start();
			wc_print_notices();
			$notifications = ob_get_clean();
			if($notifications)
			{
				$fragments['.woocommerce-notices-wrapper'] = $notifications;
			}
			return $fragments;
		}

		public function response()
		{
			$data = array(
				'fragments' => apply_filters( 'woocommerce_add_to_favorites_fragments', array() ),
				'favorites_hash' => md5( wp_json_encode(self::$FAVORITES) ),
			);
			wp_send_json( $data );
		}

		public function add_woocommerce_settings_page($settings)
		{
			$settings[] = include 'settings/class-wc-settings-favorites.php';
			return $settings;
		}

		public function register_post_states($post_states, $post)
		{
			$favs_id = wc_get_page_id('favorites');
			if($post->ID == $favs_id)
			{
				$post_states['favorites'] = 'Страница избранных';
			}
			return $post_states;
		}

		public function init_widget()
		{
			require 'widgets/favorites-widget.php';
			register_widget( 'WC_Favorites_Link' );
		}

		public function enqueue_scripts()
		{
			wp_enqueue_script( 'wc-cart-fragments' );
			wp_enqueue_script( 'grampus-add-to-favorites', plugins_url( 'assets/js/add2favorites.js', __FILE__ ), array('jquery','wc-cart-fragments'), false, true );
		}

		private static function initSession()
		{
			// if(!session_id())
			// {
			// 	session_start();
			// }
			if(!isset($_COOKIE['WC_FAVORITES']))
			{
				$_COOKIE['WC_FAVORITES'] = maybe_serialize(array());
				setcookie('WC_FAVORITES', $_COOKIE['WC_FAVORITES'],time() + (86400 * 30), '/');
				self::$FAVORITES = array();
			}
			else
			{
				self::$FAVORITES = maybe_unserialize($_COOKIE['WC_FAVORITES']);
			}
			return true;
		}

		private static function saveSession()
		{
			setcookie('WC_FAVORITES', serialize(self::$FAVORITES),time() + (86400 * 30), '/');
			return true;
		}

		public function _set_product_as_visible()
		{
			return true;
		}

		public function count_items()
		{
			return count(self::$FAVORITES);
		}

		public static function get_products()
		{
			self::initSession();
			return self::$FAVORITES;
		}

		public function favorites_items_add()
		{
			global $product;
			$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
			$product    = wc_get_product( $product_id );

			$key = array_search($product_id, self::$FAVORITES);
			if( $key === false)
			{
				self::$FAVORITES[] = $product_id;
			}
			$message = sprintf( '%s add to wishlist', $product->get_title() );
			wc_add_notice( $message, 'success' );

			$this->saveSession();

			$this->response();
		}

		public function favorites_items_remove()
		{
			global $product;
			$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
			$product    = wc_get_product( $product_id );

			$key = array_search($product_id, self::$FAVORITES);
			if( $key !== false)
			{
				unset(self::$FAVORITES[$key]);
			}
			$message = sprintf( '%s remove from wishlist', $product->get_title() );
			wc_add_notice( $message, 'success' );

			$this->saveSession();

			$this->response();
		}

		public function check_item($product_id)
		{
			$key = array_search($product_id, self::$FAVORITES);
			if($key !== false)
			{
				return true;
			}
			return false;
		}

		public function render_favorites_page()
		{
			// if(!defined('WC_Favorites_Grampus'))
			// {
			// 	define('WC_Favorites_Grampus',true);
			// }
			return do_shortcode('[products ids="'.implode(',', WCFAVORITES()::get_products()).'"]');
			// wc_get_template('favorites/page.php', array(), '', $this->get_templates_path());
		}

		public function render_single_product()
		{
			wc_get_template('single-product/add-to-favorites.php', array(), '', $this->get_templates_path());
		}

		public function render_category_product()
		{
			wc_get_template('loop/add-to-favorites.php', array(), '', $this->get_templates_path());
		}

		public function favorites_page_template($template)
		{
			global $post;
			if(!$post)
			{
				return $template;
			}
			$favs_id = wc_get_page_id('favorites');
			if($post->ID == $favs_id)
			{
				add_filter('document_title_parts', array($this, 'set_favorites_page_title_tag'));
				add_filter('woocommerce_page_title', array($this, 'set_favorites_page_title'));

				$products = WCFAVORITES()::get_products();

				if(count($products)>0)
				{
					add_action('woocommerce_before_shop_loop',function(){
						global $wp_query;
						$_page = $wp_query->post;
						$wp_query = new WP_Query(
							array(
								'post_type' => 'product',
								'nopaging'  => true,
								'post__in'  => WCFAVORITES()::get_products()
							)
						);
						wc_setup_loop(
							array(
								'total'        => $wp_query->found_posts,
								'total_pages'  => 1,
								'is_paginated' => false,
								'is_filtered'  => false,
								'name'         => 'favorites',
								'is_filtered'  => true,
								// 'columns'      => $columns,
							)
						);
						$wp_query->is_archive = false;
						$wp_query->is_post_type_archive = false;
						$wp_query->is_singular = true;
						$wp_query->post = $_page;
					}, 55);
				}
				else
				{
					add_action('woocommerce_before_shop_loop',function(){
						global $wp_query;
						$_page = $wp_query->post;
						$wp_query = new WP_Query(
							array(
								'post_type' => 'product',
								'nopaging'  => true,
								'post__in'  => [0]
							)
						);
						wc_setup_loop(
							array(
								'total'        => 0,
								'total_pages'  => 0,
								'is_paginated' => false,
								'is_filtered'  => false,
								'name'         => 'favorites',
								'is_filtered'  => true,
								// 'columns'      => $columns,
							)
						);
						$wp_query->post_count = 0;
						$wp_query->is_archive = false;
						$wp_query->is_post_type_archive = false;
						$wp_query->is_singular = true;
						$wp_query->post = $_page;
					}, 55);
				}

				$restore = $template;

				$template_name = 'favorites/page.php';
				$template_path = '';
				$default_path = $this->get_templates_path();

				$_template = wc_locate_template( $template_name, $template_path, $default_path );
				if(file_exists($_template))
				{
					$template = $_template;
					$__template = apply_filters( 'wc_get_template', $_template, $template_name, [], $template_path, $default_path );
					if($__template !== $_template and file_exists($__template))
					{
						$template = $__template;
					}
				}
				else
				{
					$template_name = 'archive-product.php';
					$default_path = '';
					$_template = wc_locate_template( $template_name, $template_path, $default_path );
					if(file_exists($_template))
					{
						$template = $_template;
						$__template = apply_filters( 'wc_get_template', $_template, $template_name, [], $template_path, $default_path );
						if($__template !== $_template and file_exists($__template))
						{
							$template = $__template;
						}
					}
					else
					{
						$template = $restore;
					}
				}
			}
			return $template;
		}

		public function set_favorites_page_title($title='')
		{
			// $title = 'Избранное';
			global $wp_query;
			$title = $wp_query->post->post_title;
			return $title;
		}

		public function set_favorites_page_title_tag($title)
		{
			// $title['title'] = 'Избранное';
			global $wp_query;
			$title['title'] = $wp_query->post->post_title;
			return $title;
		}

		public function add_to_favorites_custom($product_id,$text_off='',$text_on='')
		{
			wc_get_template('favorites/custom.php', array('product_id'=>$product_id,'text_off'=>$text_off,'text_on'=>$text_on), '', $this->get_templates_path());
		}

		public function remove_all_favorites()
		{
			self::$FAVORITES = array();

			$this->saveSession();
		}
	}

	function WCFAVORITES()
	{
		return WC_Favorites_Grampus::instance();
	}

	$_GLOBALS['WCFAVORITES'] = WCFAVORITES();
}
if(!function_exists('wc_get_favorites_url'))
{
	function wc_get_favorites_url()
	{
		return apply_filters( 'woocommerce_get_favorites_url', wc_get_page_permalink( 'favorites' ) );
	}
}
if(!function_exists('add_to_favorites_custom'))
{
	function add_to_favorites_custom($product_id,$text_off='',$text_on='')
	{
		return WCFAVORITES()->add_to_favorites_custom($product_id,$text_off,$text_on);
	}
}