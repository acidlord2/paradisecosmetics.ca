<?php
/**
 * Plugin Name: WooCommerce AJAX добавление в корзину
 * Description: AJAX добавление в корзину на странице товара
 * Version: 2.1.1
 * Author: Grampus Studio
 * Author URI: https://grampus-studio.ru/
 */
defined( 'ABSPATH' ) || exit;

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
{
	/**
	 * WC_Add2cart_Grampus class.
	 */
	class WC_Add2cart_Grampus
	{
		public $version = '2.1.1';
		public static $cache_key = 'wc_add2cart_grampus';

		protected static $_instance = null;

		private $need_wnumb = false;

		function __construct()
		{
			// add_filter( 'site_transient_update_plugins', array($this, 'check_for_updates') );
			// add_filter( 'transient_update_plugins', array($this, 'check_for_updates') );
			// add_action( 'upgrader_process_complete', array($this, 'clear_transients'), 10, 2 );
			add_filter( 'plugin_row_meta', array($this, 'show_view_details'), 30, 2 );
			add_filter( 'plugins_api', array($this, 'plugin_info'), 20, 3 );

			add_filter( 'woocommerce_get_settings_pages', array($this, 'add_woocommerce_settings_page') );

			add_action( 'wp_ajax_woocommerce_add2cart', array($this, 'add2cart'), 20 );
			add_action( 'wp_ajax_nopriv_woocommerce_add2cart', array($this, 'add2cart'), 20 );
			add_action( 'wc_ajax_add2cart', array($this, 'add2cart'), 20 );

			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts'), 20 );
			add_filter( 'woocommerce_cart_ready_to_calc_shipping', array($this, 'disable_cart_shipping_calculation'), 9998 );

			add_action( 'init', array($this,'__enable_qty_input'), 30 );
			add_action( 'init', array($this,'__enable_qty_buttons'), 40 );
			add_action( 'init', array($this,'__enable_qty_summ'), 50 );

		}

		public static function disable_cart_shipping_calculation($can_calc)
		{
			if( is_cart() || isset($_POST['update_cart']) )
			{
				$can_calc = false;
			}
			return $can_calc;
		}

		public static function instance()
		{
			if(is_null(self::$_instance))
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public static function check_for_updates( $transient )
		{
			if( empty($transient->checked) )
			{
				return $transient;
			}

			$remote = self::_load();

			if( $remote && version_compare( WCADD2CART()->version, $remote->version, '<' ) && version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' ) && version_compare( $remote->requires_php, PHP_VERSION, '<=' ) )
			{
				$res = (object)array(
					'slug' => 'woocommerce-add2cart-grampus',
					'plugin' => 'woocommerce-add2cart-grampus/woocommerce-add2cart-grampus.php',
					'new_version' => $remote->version,
					'tested' => $remote->tested,
					'package' => $remote->download_url,
				);

				$transient->response[ $res->plugin ] = $res;
			}

			return $transient;
		}

		private static function _load()
		{
			$remote = get_transient( self::$cache_key );

			if( false === $remote )
			{
				$remote = wp_remote_get(
					'https://updates.grampus-server.ru/plugins/woocommerce-add2cart-grampus/',
					array(
						'timeout' => 10,
						'headers' => array(
							'Accept' => 'application/json'
						)
					)
				);

				if( is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote) || empty( wp_remote_retrieve_body($remote) ) )
				{
					return false;
				}

				set_transient(self::$cache_key, $remote, 3600);
			}

			$remote = json_decode( wp_remote_retrieve_body($remote) );

			return $remote;
		}

		public static function plugin_info($res, $action, $args)
		{
			if('plugin_information' !== $action)
			{
				return $res;
			}

			if('woocommerce-add2cart-grampus' !== $args->slug)
			{
				return $res;
			}

			$remote = self::_load();

			if(!$remote)
			{
				return $res;
			}

			$res = new \stdClass();

			$res->name = $remote->name;
			$res->slug = $remote->slug;
			$res->author = $remote->author;
			$res->author_profile = '';
			$res->version = $remote->version;
			$res->tested = $remote->tested;
			$res->requires = $remote->requires;
			$res->requires_php = $remote->requires_php;
			$res->download_link = $remote->download_url;
			$res->trunk = $remote->download_url;
			$res->last_updated = $remote->last_updated;
			$res->sections = array(
				'description' => $remote->description,
				'installation' => $remote->installation,
				'changelog' => $remote->changelog
			);
			$res->banners = array();

			return $res;
		}

		public static function clear_transients($ignore, $payload)
		{
			if(!is_array($payload))
			{
				return;
			}
			if( 'update' === $payload['action'] && 'plugin' === $payload['type'] || $payload == 'deactivation')
			{
				delete_transient(self::$cache_key);
			}
		}

		public static function show_view_details($plugin_meta, $plugin_slug)
		{
			if('woocommerce-add2cart-grampus/woocommerce-add2cart-grampus.php' === $plugin_slug)
			{
				foreach($plugin_meta as $existing_link)
				{
					if (strpos($existing_link, 'tab=plugin-information') !== false)
					{
						return $plugin_meta;
					}
				}

				$plugin_info = get_plugin_data( GSE()::plugin_fullpath() );
				$plugin_meta[] = sprintf( '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
					esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce-add2cart-grampus&TB_iframe=true&width=600&height=550' ) ),
					esc_attr( sprintf( __( 'More information about %s' ), $plugin_info['Name'] ) ),
					esc_attr( $plugin_info['Name'] ),
					__( 'View details' )
				);
			}
			return $plugin_meta;
		}

		function __enable_qty_input()
		{
			$qty_input = get_option('add2cart_qty_input','yes');
			if( $qty_input == 'no' )
			{
				add_action( 'woocommerce_before_template_part', array($this, 'start_hide_qty_input'), 90, 4);
				add_action( 'woocommerce_after_template_part', array($this, 'end_hide_qty_input'), 90, 4);
			}
		}

		function __disable_qty_input()
		{
			remove_action( 'woocommerce_before_template_part', array($this, 'start_hide_qty_input'), 90, 4);
			remove_action( 'woocommerce_after_template_part', array($this, 'end_hide_qty_input'), 90, 4);
		}

		function __enable_qty_buttons()
		{
			$qty_btns = get_option('add2cart_qty_buttons','no');
			if( $qty_btns != 'no' )
			{
				add_action( 'woocommerce_before_add_to_cart_quantity', array($this, 'woocommerce_add_to_cart_quantity_wrapper_start'), 5  );
				add_action( 'woocommerce_before_add_to_cart_quantity', array($this, 'woocommerce_before_add_to_cart_quantity_btn'), 10  );
				add_action( 'woocommerce_after_add_to_cart_quantity', array($this, 'woocommerce_after_add_to_cart_quantity_btn'), 10  );
				add_action( 'woocommerce_after_add_to_cart_quantity', array($this, 'woocommerce_add_to_cart_quantity_wrapper_end'), 15  );
				add_filter( 'woocommerce_cart_item_quantity', array($this, 'woocommerce_cart_quantity'), 20, 3 );
			}
		}

		function __disable_qty_buttons()
		{
			remove_action( 'woocommerce_before_add_to_cart_quantity', array($this, 'woocommerce_add_to_cart_quantity_wrapper_start'), 5  );
			remove_action( 'woocommerce_before_add_to_cart_quantity', array($this, 'woocommerce_before_add_to_cart_quantity_btn'), 10  );
			remove_action( 'woocommerce_after_add_to_cart_quantity', array($this, 'woocommerce_after_add_to_cart_quantity_btn'), 10  );
			remove_action( 'woocommerce_after_add_to_cart_quantity', array($this, 'woocommerce_add_to_cart_quantity_wrapper_end'), 15  );
			remove_filter( 'woocommerce_cart_item_quantity', array($this, 'woocommerce_cart_quantity'), 20, 3 );
		}

		function __enable_qty_summ()
		{
			$qty_input = get_option('add2cart_qty_input','yes');
			$qty_summ_position = get_option('add2cart_qty_summ_position','no');
			if($qty_summ_position != 'no' && $qty_input != 'no')
			{
				$this->need_wnumb = true;
				$qty_summ_order = get_option('add2cart_qty_summ_order',20);
				add_action('woocommerce_'.$qty_summ_position, array($this, 'render_qty_summ'), $qty_summ_order);

				add_action('wp_print_footer_scripts', array($this, 'enqueue_scripts_product'), 1);
			}
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

		public function add_woocommerce_settings_page($settings)
		{
			$settings[] = include 'settings/class-wc-settings-add2cart.php';
			return $settings;
		}

		public function enqueue_scripts()
		{
			wp_deregister_script('wc-add-to-cart');
			wp_register_script( 'wc-add-to-cart', plugins_url( 'assets/js/add2cart.js', __FILE__ ), array('jquery','wc-cart-fragments'), @filemtime(__DIR__.'/assets/js/add2cart.js'), true );

			if($this->need_wnumb)
			{
				wp_register_script( 'wc-qty-control-wnumb', plugins_url( 'assets/js/wNumb.min.js', __FILE__ ), array('jquery'), false, true );
				wp_register_script( 'wc-qty-control', plugins_url( 'assets/js/qty-control.js', __FILE__ ), array('jquery','wc-qty-control-wnumb'), @filemtime(__DIR__.'/assets/js/qty-control.js'), true );
				// wp_enqueue_script( 'wc-qty-control-wnumb' );
			}
			else
			{
				wp_register_script( 'wc-qty-control', plugins_url( 'assets/js/qty-control.js', __FILE__ ), array('jquery'), @filemtime(__DIR__.'/assets/js/qty-control.js'), true );
			}

			wp_enqueue_script( 'wc-cart-fragments' );
			wp_enqueue_script( 'wc-add-to-cart' );
			wp_enqueue_script( 'wc-add-to-cart-variation' );

			$qty_btns = get_option('add2cart_qty_buttons','no');
			if($qty_btns != 'no')
			{
				wp_enqueue_script( 'wc-qty-control' );
			}
			elseif(is_cart())
			{
				$script = "jQuery('.woocommerce').on('change', 'input.qty', function(){ jQuery('[name=\"update_cart\"]').trigger('click'); });";
				wp_add_inline_script('wc-cart',$script,'after');
			}
			wp_add_inline_script('wc-qty-control','var _QtyControl_isCart = '.(is_cart()?'true':'false').';','before');
		}

		public function enqueue_scripts_product()
		{
			if(is_product())
			{
				wp_localize_script( 'wc-add-to-cart', 'qty_summ_args', $this->get_product_prices() );
			}
		}

		public function add2cart()
		{
			wc_maybe_define_constant( 'DOING_AJAX', true );
			wc_maybe_define_constant( 'WC_DOING_AJAX', true );
			ob_start();

			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( ! isset( $_POST['product_id'] ) ) {
				return;
			}

			$forced = false;

			$mode = get_option('add2cart_result_type','no');
			if(isset($_POST['force_a2c_mode']) && in_array($_POST['force_a2c_mode'], array('no','notice','modal')))
			{
				$forced = true;
				$mode = $_POST['force_a2c_mode'];
			}

			switch ($mode)
			{
				case 'notice':
					add_action('woocommerce_ajax_added_to_cart', array($this,'add_success_message'));
					add_filter('woocommerce_add2cart_fragments', array( $this, 'add_notifications' ), 30 );
					if($forced)
					{
						add_filter('woocommerce_add2cart_fragments', array( $this, 'add_cart_info' ), 30 );
					}
					break;

				case 'modal':
					add_filter('woocommerce_add2cart_fragments', array( $this, 'add_modal' ), 40, 2 );
					break;
				
				default:
					break;
			}

			$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( isset($_POST['variation_id']) ? $_POST['variation_id'] : $_POST['product_id'] ) );
			$product           = wc_get_product( $product_id );
			$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
			$product_status    = get_post_status( $product_id );
			$variation_id      = 0;
			$variation         = array();

			if ( $product && 'variation' === $product->get_type() ) {
				$variation_id = $product_id;
				$product_id   = $product->get_parent_id();
				$variation    = $product->get_variation_attributes();
			}

			if ( $variation ) {
				foreach ($variation as $key => $value) {
					if ( !$value ) {
						$variation[$key] = isset($_POST[$key]) ? $_POST[$key] : $value;
					}
				}
			}

			add_filter('woocommerce_cart_ready_to_calc_shipping', '__return_false', 9999);

			if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				// if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				// 	wc_add_to_cart_message( array( $product_id => $quantity ), true );
				// }

				ob_start();
				woocommerce_mini_cart();
				$mini_cart = ob_get_clean();

				$data = apply_filters(
					'woocommerce_add2cart_fragments',
					array(
						'fragments' => apply_filters(
							'woocommerce_add_to_cart_fragments',
							array(
								'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
							)
						),
						'cart_hash' => WC()->cart->get_cart_hash(),
					),
					$product
				);

				wp_send_json( $data );

			} else {

				ob_start();
				woocommerce_mini_cart();
				$mini_cart = ob_get_clean();

				$data = apply_filters(
					'woocommerce_add2cart_fragments',
					array(
						'fragments' => apply_filters(
							'woocommerce_add_to_cart_fragments',
							array(
								'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
							)
						),
						'cart_hash' => WC()->cart->get_cart_hash(),
					),
					$product
				);

				// If there was an error adding to the cart, redirect to the product page to show any errors.
				// $data = array(
				// 	'error'       => true,
				// 	'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
				// 	'passed_validation' => $passed_validation,
				// 	'product_status' => $product_status,
				// 	'add_to_cart' => WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ),
				// 	'variation_id' => $variation_id,
				// 	'product_id' => $product_id,
				// 	'variation' => $variation,
				// );

				wp_send_json( $data );
			}
		}

		function add_success_message($product_id)
		{
			$quantity = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
		}

		public function add_notifications($data)
		{
			ob_start();
			wc_print_notices();
			$notifications = ob_get_clean();
			if($notifications)
			{
				$data['fragments']['.woocommerce-notices-wrapper'] = $notifications;
			}
			return $data;
		}

		static public function get_cart_info()
		{
			ob_start();
			wc_get_template('add2cart/stats.php');
			return ob_get_clean();
		}

		public function add_cart_info($data)
		{
			$cart_info = self::get_cart_info();
			if($cart_info)
			{
				$data['fragments']['.cart-info'] = $cart_info;
			}
			return $data;
		}

		public function add_modal($data,$prd)
		{
			global $product;
			$product = $prd;
			$GLOBALS['product'] = $prd;
			$cross_sells = array_filter( array_map( 'wc_get_product', wp_parse_id_list( $prd->get_cross_sell_ids() ) ), 'wc_products_array_filter_visible' );
			if(!$cross_sells)
			{
				$cross_sells = array_filter( array_map( 'wc_get_product', wp_parse_id_list( WC()->cart->get_cross_sells() ) ), 'wc_products_array_filter_visible' );
			}
			ob_start();
			wc_get_template(
				'add2cart/modal.php',
				array(
					'cross_sells' => $cross_sells,
				)
			);
			$modal = ob_get_clean();
			if($modal)
			{
				$data['modal'] = $modal;
			}
			return $data;
		}

		public function woocommerce_add_to_cart_quantity_wrapper_start()
		{
			$qty_input = get_option('add2cart_qty_input','yes');
			if( $qty_input == 'yes' || is_cart() )
			{
				echo '<div class="qty-wrapper">';
			}
		}

		public function woocommerce_before_add_to_cart_quantity_btn($enabled=false)
		{
			$qty_input = get_option('add2cart_qty_input','yes');
			if( $qty_input == 'yes' || is_cart() )
			{
				echo sprintf('<button type="button" class="qty-button" qty-decrease %s>%s</button>', $enabled?'':'disabled', apply_filters('add2cart_qty_btn_minus_svg', apply_filters('add2cart_qty_btn_svg', '') ) );
			}
		}

		public function woocommerce_after_add_to_cart_quantity_btn()
		{
			$qty_input = get_option('add2cart_qty_input','yes');
			if( $qty_input == 'yes' || is_cart() )
			{
				echo sprintf('<button type="button" class="qty-button" qty-increase>%s</button>', apply_filters('add2cart_qty_btn_plus_svg', apply_filters('add2cart_qty_btn_svg', '') ) );
			}
		}

		public function woocommerce_add_to_cart_quantity_wrapper_end()
		{
			$qty_input = get_option('add2cart_qty_input','yes');
			if( $qty_input == 'yes' || is_cart() )
			{
				echo '</div>';
			}
		}

		public function render_qty_summ()
		{
			if(!is_product())
			{
				return;
			}

			global $product;
			$qty_summ_text = get_option('add2cart_qty_summ_text','');


			if($product->get_type() == 'variable')
			{
				$cprice = wc_price( 0 );
			}
			else
			{
				$cprice = wc_price( $product->get_price() );
			}
			
			echo '<div class="qty-summ">'.nl2br($qty_summ_text).'<strong>'.$cprice.'</strong></div>';
		}

		public function get_product_prices()
		{
			$args = apply_filters(
				'wc_price_args',
				array(
					'currency'           => '',
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
					'price_format'       => get_woocommerce_price_format(),
				)
			);
			$args['currency'] = '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $args['currency'] ) . '</span>';

			$config = array(
				'format' => array(
					'mark' => $args['decimal_separator'],
					'thousand' => $args['thousand_separator'],
					'template' => '<span class="woocommerce-Price-amount amount"><bdi>%s</bdi></span>',
					'trim_zeros' => apply_filters( 'woocommerce_price_trim_zeros', false )
				),
				'prices' => array(),
			);

			if($args['price_format'] == '%1$s%2$s')
			{
				$config['format']['suffix'] = '';
				$config['format']['prefix'] = $args['currency'];
			}
			else if($args['price_format'] == '%2$s%1$s')
			{
				$config['format']['suffix'] = $args['currency'];
				$config['format']['prefix'] = '';
			}
			else if($args['price_format'] == '%1$s&nbsp;%2$s')
			{
				$config['format']['suffix'] = '';
				$config['format']['prefix'] = $args['currency'].'&nbsp;';
			}
			else if($args['price_format'] == '%2$s&nbsp;%1$s')
			{
				$config['format']['suffix'] = '&nbsp;'.$args['currency'];
				$config['format']['prefix'] = '';
			}

			global $product;
			if($product->get_type() == 'variable')
			{
				$config['mode'] = 'variable';
				$config['prices'][ 0 ] = 0;
				$variations = $product->get_available_variations();
				foreach($variations as $variation)
				{
					$config['prices'][ $variation['variation_id'] ] = $variation['display_price'];
				}
			}
			else
			{
				$config['mode'] = 'simple';
				$config['prices'][ 0 ] = floatval($product->get_price());
			}
			return $config;
		}

		public function woocommerce_cart_quantity($product_quantity, $cart_item_key, $cart_item)
		{
			ob_start();
			$this->woocommerce_add_to_cart_quantity_wrapper_start();
			$this->woocommerce_before_add_to_cart_quantity_btn($cart_item['quantity']>0);
			echo $product_quantity;
			$this->woocommerce_after_add_to_cart_quantity_btn();
			$this->woocommerce_add_to_cart_quantity_wrapper_end();
			return ob_get_clean();
		}

		function start_hide_qty_input($template_name, $template_path, $located, $args)
		{
			if($template_name == 'global/quantity-input.php' && !is_cart())
			{
				ob_start();
			}
		}

		function end_hide_qty_input($template_name, $template_path, $located, $args)
		{
			if($template_name == 'global/quantity-input.php' && !is_cart())
			{
				ob_clean();
			}
		}
	}

	function WCADD2CART()
	{
		return WC_Add2cart_Grampus::instance();
	}

	$_GLOBALS['WCADD2CART'] = WCADD2CART();
}