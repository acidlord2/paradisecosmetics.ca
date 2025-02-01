<?php
/**
 * WooCommerce Product Settings
 *
 * @package WooCommerce\Admin
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Settings_Favorites', false ) ) {
	return new WC_Settings_Favorites();
}

/**
 * WC_Settings_Favorites.
 */
class WC_Settings_Favorites extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'favorites';
		$this->label = 'Избранные';

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''             => __( 'General', 'woocommerce' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		if ( '' === $current_section ) {
			$fields = array(
				array(
					'title' => __( 'Page setup', 'woocommerce' ),
					'desc'  => 'Эти страницы нужны для того, чтобы система могла перенаправлять пользователей.',
					'type'  => 'title',
					'id'    => 'favorites_page_options',
				),
					array(
						'title'    => 'Страница избранных',
						'desc'     => '',
						'id'       => 'woocommerce_favorites_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'wc-enhanced-select-nostd',
						'css'      => 'min-width:300px;',
						'args'     => array(
							'exclude' =>
								array(
									wc_get_page_id( 'cart' ),
									wc_get_page_id( 'checkout' ),
									wc_get_page_id( 'myaccount' ),
								),
						),
						'desc_tip' => false,
						'autoload' => false,
					),
				array(
					'type' => 'sectionend',
					'id'   => 'favorites_page_options',
				),

				array(
					'title' => 'Место вставки',
					'type'  => 'title',
					'id'    => 'favorites_button_position',
				),
					array(
						'title'    => 'Cтраница товара',
						'id'       => 'favorites_single_product_position',
						'default'  => 'all',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width: 300px;',
						'desc_tip' => false,
						'options'  => array(
							'no'                        => 'Нет',
							'before_add_to_cart_button' => 'Перед кнопкой в корзину',
							'after_add_to_cart_button'  => 'После кнопки в корзину'
						),
					),
					array(
						'title'    => 'Порядок вывода',
						'desc'     => 'Не изменяйте, если не знаете зачем это нужно.',
						'id'       => 'favorites_single_product_order',
						'type'     => 'text',
						'default'  => '10',
						'desc_tip' => true,
					),
					array(
						'title'    => 'Текст кнопки',
						'id'       => 'favorites_single_product_text',
						'type'     => 'text',
						'default'  => 'В избранные',
					),
					array(
						'title'    => 'Cтраница категории',
						'id'       => 'favorites_category_product_position',
						'default'  => 'all',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width: 300px;',
						'desc_tip' => false,
						'options'  => array(
							'no'                    => 'Нет',
							'after_shop_loop_item'  => 'После кнопки в корзину'
						),
					),
					array(
						'title'    => 'Порядок вывода',
						'desc'     => 'Не изменяйте, если не знаете зачем это нужно.',
						'id'       => 'favorites_category_product_order',
						'type'     => 'text',
						'default'  => '10',
						'desc_tip' => true,
					),
					array(
						'title'    => 'Текст кнопки',
						'id'       => 'favorites_category_product_text',
						'type'     => 'text',
						'default'  => 'В избранные',
					),
				array(
					'type' => 'sectionend',
					'id'   => 'favorites_button_position',
				),
			);
			
			$settings = apply_filters('woocommerce_favorites_settings',$fields);
		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}

	/**
	 * Form method.
	 *
	 * @deprecated 3.4.4
	 *
	 * @param  string $method Method name.
	 *
	 * @return string
	 */
	public function form_method( $method ) {
		return 'post';
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;
		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );

		// do_action( 'woocommerce_update_options_' . $this->id );
		// WCMULTICURRENCY()->update_currencies();
	}
}

return new WC_Settings_Favorites();
