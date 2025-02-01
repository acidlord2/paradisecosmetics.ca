<?php
/**
 * Plugin Name: WooCommerce Виджет: Фильтр товаров
 * Description: 
 * Version: 1.0.0
 * Author: Grampus Studio
 * Author URI: https://grampus-studio.ru/
 */
defined( 'ABSPATH' ) || exit;

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
{
	/**
	 * WC_Filters_Grampus class.
	 */
	class WC_Filters_Widget_Grampus
	{

		function __construct()
		{
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts'), 10 );
			add_action( 'widgets_init', array($this, 'init_widget') );

			add_filter( 'woocommerce_product_query', array($this, 'product_query') );
			add_filter( 'woocommerce_attributes_field', array($this, 'add_custom_to_attributes') );
			add_filter( 'acf/init', array($this, '_init_acf_fields') );
		}

		public function add_custom_to_attributes($attributes)
		{
			$attributes = array('product_cat' => 'Категория') + $attributes;
			$attributes = array('price' => 'Цена') + $attributes;
			return $attributes;
		}

		public function init_widget()
		{
			require __DIR__.'/widgets/filter-widget.php';
			register_widget( 'WC_Filters_Grampus' );
		}

		public function enqueue_scripts()
		{
			wp_register_style('widget-filters', plugins_url('assets/filters.css', __FILE__), array(), filemtime(__DIR__.'/assets/filters.css'));
			wp_register_script('nouislider', plugins_url('assets/nouislider.js', __FILE__), array('jquery'), filemtime(__DIR__.'/assets/nouislider.js'), true);
			wp_register_script('widget-list', plugins_url('assets/list.js', __FILE__), array('jquery'), false, true);
			wp_register_script('widget-filters', plugins_url('assets/filters.js', __FILE__), array('jquery'), filemtime(__DIR__.'/assets/filters.js'), true);
		}

		public function product_query($query)
		{
			if(!$query->is_main_query())
			{
				return;
			}
			if(is_product_taxonomy() or is_shop())
			{
				$http_request = $_REQUEST;
				$tax_query = $query->get('tax_query');
				$meta_query = $query->get('meta_query');

				foreach($http_request as $key => $data)
				{
					if(substr($key, 0, 2) == 'f_')
					{
						$slug = substr($key, 2);
						if(array_key_exists($key, $http_request))
						{
							if($slug != 'product_cat')
							{
								$tax_query[] = array(
									'taxonomy' => 'pa_'.$slug,
									'field'    => 'slug',
									'terms'    => $http_request[$key],
									'operator' => 'IN',
								);
							}
							else
							{
								$tax_query[] = array(
									'taxonomy' => $slug,
									'field'    => 'slug',
									'terms'    => $http_request[$key],
									'operator' => 'IN',
								);
							}
						}
					}
				}

				if(array_key_exists('mark', $http_request) && !empty($http_request['mark'])) {
					$tax_query[] = array(
						'taxonomy' => 'product_tag',
						'field'    => 'term_id',
						'terms'    => $http_request['mark'],
						'operator' => 'AND',
					);
				}

				$query->set('tax_query',$tax_query);
				$query->set('meta_query',$meta_query);
			}
		}

		public function _init_acf_fields()
		{
			acf_add_local_field_group(
				array(
					'key' => 'group_filter_params',
					'title' => 'Параметры фильтра',
					'fields' => array(
						array(
							'key' => 'field_use_attributes',
							'label' => 'Элементы фильтра (Если не выбраны - отобразятся все)',
							'name' => 'use_attributes',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'block',
							'button_label' => '',
							'sub_fields' => array(
								array(
									'key' => 'field_attribute',
									'label' => 'Атрибут',
									'name' => 'attribute',
									'type' => 'woocommerce_attributes',
									'instructions' => '',
									'required' => 1,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'data_type' => 'attributes',
									'field_type' => 'select',
								),
								array(
									'key' => 'field_attribute_name',
									'label' => 'Название',
									'name' => 'attribute_name',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => 'Пустое поле - название атрибута',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
								array(
									'key' => 'field_hierarchical',
									'label' => 'Иерархия',
									'name' => 'hierarchical',
									'type' => 'true_false',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '50',
										'class' => '',
										'id' => '',
									),
									'message' => 'Вложенность для категорий',
									'default_value' => 0,
									'ui' => 1,
									'ui_on_text' => '',
									'ui_off_text' => '',
								),
								array(
									'key' => 'field_use_search',
									'label' => 'Показывать поиск',
									'name' => 'search',
									'type' => 'true_false',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '50',
										'class' => '',
										'id' => '',
									),
									'message' => 'Отображается если в списке больше 10 элементов.',
									'default_value' => 1,
									'ui' => 1,
									'ui_on_text' => 'Есть',
									'ui_off_text' => 'Нет',
								),
							),
						),
						array(
							'key' => 'field_price_params',
							'label' => 'Параметры фильтра цен',
							'name' => 'price',
							'type' => 'group',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'layout' => 'block',
							'sub_fields' => array(
								array(
									'key' => 'field_price_params_type',
									'label' => 'Тип',
									'name' => 'type',
									'type' => 'select',
									'instructions' => '',
									'required' => 1,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'choices' => array(
										'slider' => 'Слайдер',
										'ranges' => 'Только диапазоны',
										'both' => 'Поля и диапазоны',
									),
									'default_value' => 'inputs',
									'allow_null' => 0,
									'multiple' => 0,
									'ui' => 0,
									'return_format' => 'value',
									'ajax' => 0,
									'placeholder' => '',
								),
								array(
									'key' => 'field_price_ranges',
									'label' => 'Готовые диапазоны цен',
									'name' => 'ranges',
									'type' => 'repeater',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => array(
										array(
											array(
												'field' => 'field_price_params_type',
												'operator' => '!=',
												'value' => 'inputs',
											),
										),
									),
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'collapsed' => '',
									'min' => 0,
									'max' => 0,
									'layout' => 'table',
									'button_label' => '',
									'sub_fields' => array(
										array(
											'key' => 'field_price_range_start',
											'label' => 'От',
											'name' => 'start',
											'type' => 'number',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
											'min' => '',
											'max' => '',
											'step' => '',
										),
										array(
											'key' => 'field_price_range_end',
											'label' => 'До',
											'name' => 'end',
											'type' => 'number',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'placeholder' => '',
											'prepend' => '',
											'append' => '',
											'min' => '',
											'max' => '',
											'step' => '',
										),
									),
								),
							),
						),
					),
					'location' => array(
						array(
							array(
								'param' => 'widget',
								'operator' => '==',
								'value' => 'woocommerce_filters_selectable',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'field',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				)
			);
		}
	}

	new WC_Filters_Widget_Grampus();
}