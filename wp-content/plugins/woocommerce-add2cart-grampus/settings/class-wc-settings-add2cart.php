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

if ( class_exists( 'WC_Settings_Add2cart', false ) ) {
	return new WC_Settings_Add2cart();
}

/**
 * WC_Settings_Add2cart.
 */
class WC_Settings_Add2cart extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'add2cart';
		$this->label = 'Add2cart';

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
					'title' => 'Настройки',
					'type'  => 'title',
					'id'    => 'add2cart_settings',
				),
					array(
						'title'    => 'Сообщение',
						'id'       => 'add2cart_result_type',
						'default'  => 'all',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width: 300px;',
						'desc_tip' => false,
						'options'  => array(
							'no'     => 'Нет',
							'notice' => 'Уведомление',
							'modal'  => 'Всплывающее окно',
						),
					),
					array(
						'title'   => 'Выбор количества',
						'id'      => 'add2cart_qty_input',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'   => 'Кнопки ±',
						'id'      => 'add2cart_qty_buttons',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'    => 'Сумма',
						'id'       => 'add2cart_qty_summ_position',
						'default'  => 'all',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width: 300px;',
						'desc_tip' => false,
						'options'  => array(
							'no'                          => 'Нет',
							'before_add_to_cart_button'   => 'Перед кнопкой в корзину',
							'after_add_to_cart_button'    => 'После кнопки в корзину',
							'before_add_to_cart_quantity' => 'Перед полем количества',
							'after_add_to_cart_quantity'  => 'После поля количества',
						),
					),
					array(
						'title'    => 'Порядок вывода суммы',
						'desc'     => 'Не изменяйте, если не знаете зачем это нужно.',
						'id'       => 'add2cart_qty_summ_order',
						'type'     => 'text',
						'default'  => '20',
						'desc_tip' => true,
					),
					array(
						'title'    => 'Текст суммы',
						'id'       => 'add2cart_qty_summ_text',
						'type'     => 'textarea',
						'default'  => 'Общий итог:',
					),
				array(
					'type' => 'sectionend',
					'id'   => 'add2cart_settings',
				),
			);
			
			$settings = apply_filters('woocommerce_add2cart_settings',$fields);
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

return new WC_Settings_Add2cart();
