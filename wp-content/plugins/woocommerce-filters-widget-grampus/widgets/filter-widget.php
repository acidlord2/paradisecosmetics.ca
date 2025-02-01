<?php

defined( 'ABSPATH' ) || exit;

class WC_Filters_Grampus extends WC_Widget
{
	// public $settings;
	// public $_ids;
	// public $filters;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->widget_cssclass    = 'woocommerce widget_filters';
		$this->widget_description = 'Фильтр товаров категории, применяется для одной/нескольких/всех категорий';
		$this->widget_id          = 'woocommerce_filters_selectable';
		$this->widget_name        = 'Фильтр товаров';
		$this->after_widget       = '';
		$this->before_widget      = '';
		$this->cache_time         = 0;

		$this->_ids = array();
		$this->filters = array();
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Фильтр товаров',
				'label' => __( 'Title', 'woocommerce' ),
			),
			'cache' => array(
				'type' => 'range',
				'std' => 300,
				'label' => 'Время кэша (0-300сек)',
			),
			'category' => array(
				'type' => 'mselect',
				'std' => [],
				'label' => 'Для категории',
				'options' => array()
			),
			'auto' => array(
				'type' => 'checkbox',
				'std' => false,
				'label' => 'Автоприменение',
			),
			// 'active_attributes' => array(
			// 	'type' => 'mselect2',
			// 	'std' => array(),
			// 	'label' => 'Использовать атрибуты',
			// 	'options' => $this->get_active_attributes()
			// ),
		);
		parent::__construct();
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? array_map('intval',$new_instance['category']) : [];
		$instance['auto'] = boolval($new_instance['auto']);
		$instance['cache'] = intval($new_instance['cache']);

		$this->clear_active_cache();
		return $instance;
	}

	public function form( $instance )
	{

		if ( empty( $this->settings ) ) {
			return;
		}


		$this->settings['category']['options'] = $this->get_all_categories($instance);

		foreach ( $this->settings as $key => $setting ) {

			$class = isset( $setting['class'] ) ? $setting['class'] : '';
			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			if($key == 'category')
			{
				if(!is_array($value))
				{
					$value = array($value);
				}
			}

			switch ( $setting['type'] ) {

				case 'text':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo wp_kses_post( $setting['label'] ); ?></label><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
					break;

				case 'range':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo wp_kses_post( $setting['label'] ); ?></label><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="range" step="10" min="0" max="300" value="<?php echo esc_attr( $value ); ?>" list="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>_list" />
						<datalist id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>_list">
							<option value="0" label="0">0</option>
							<option value="50" label="50">50</option>
							<option value="100" label="100">100</option>
							<option value="150" label="150">150</option>
							<option value="200" label="200">200</option>
							<option value="250" label="250">250</option>
							<option value="300" label="300">300</option>
						</datalist>
						<style>datalist{display:flex;width:100%;justify-content:space-between;flex-direction:row;}datalist option {display:inline-flex;justify-content:center;flex-basis:22px;padding:0px;}</style>
					</p>
					<?php
					break;

				case 'number':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
					break;

				case 'select':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
						<select class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
					break;

				case 'mselect':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
						<select class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>[]" multiple>
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
							<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( in_array($option_key, $value) ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
					break;

				case 'textarea':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
						<textarea class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
					break;

				case 'checkbox':
					?>
					<p>
						<input class="checkbox <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
					</p>
					<?php
					break;

				// Default: run an action.
				default:
					do_action( 'woocommerce_widget_field_' . $setting['type'], $key, $value, $setting, $instance );
					break;
			}
		}
	}

	public function widget( $args, $instance )
	{
		if(woocommerce_get_loop_display_mode() == 'subcategories')
		{
			return;
		}

		$this->cache_time = $instance['cache'];

		if(count($instance['category']) > 0)
		{
			if(!is_tax('product_cat',$instance['category']))
			{
				return;
			}
		}
		else
		{
			if(!is_product_taxonomy() && !is_search() && !is_shop())
			{
				return;
			}
		}

		if(is_search() || is_shop())
		{
			$active_page = null;
		}
		else
		{
			$active_page = get_queried_object();
		}

		$this->get_filter_elements($args['widget_id']);
		$this->get_filter_elements_data($active_page,$args['widget_id']);

		if($this->filters)
		{
			foreach ($this->filters as $attr => $values)
			{
				if($attr != 'price')
				{
					if(empty($this->filters[$attr]['values']))
					{
						unset($this->filters[$attr]);
					}
				}
				else
				{
					if($this->filters[$attr]['price_min'] == $this->filters[$attr]['price_max'])
					{
						unset($this->filters[$attr]);
					}
				}
			}
		}

		if(is_search())
		{
			$instance['current_url'] = get_search_link();
		}
		elseif(is_shop())
		{
			$instance['current_url'] = get_permalink(wc_get_page_id('shop'));
		}
		else
		{
			$instance['current_url'] = get_term_link(get_queried_object());
		}

		if(!empty($this->filters))
		{
			wp_enqueue_style('widget-filters');
			if(array_key_exists('price', $this->filters) and $this->filters['price']['type'] != 'ranges')
			{
				wp_enqueue_script('nouislider');
			}
			wp_enqueue_script('widget-list');
			wp_enqueue_script('widget-filters');

			echo '<div class="filters-widget">';
			@include dirname(__DIR__).'/templates/filter-widget.php';
			echo '</div>';
		}
	}

	/*
	 * DATA
	 */

	private function get_all_categories()
	{
		$args = array(
		  'hierarchical' => true,
		  'pad_counts' => true,
		  'taxonomy'     => 'product_cat',
		  'hide_empty'   => false,
		  'menu_order'   => 'asc',
		);
		$raw_taxonomies = $this->get_cat_tree($args);
		$taxonomies = array();
		if($raw_taxonomies)
		{
			foreach($raw_taxonomies as $taxonomy)
			{
				$taxonomies = $this->rcinline($taxonomy,0,$taxonomies);
			}
		}
		$result = array(
			// '' => '-- ВСЕ --',
		);
		foreach($taxonomies as $tax)
		{
			$result[$tax->term_id] = str_repeat('— ',$tax->depth).$tax->name;
		}
		return $result;
	}

	public function get_filter_elements($widget_id)
	{
		$widget_cache_key = 'woocommerce_filters_widget_'.$widget_id.'_attributes';
		// $filters = wp_cache_get( $widget_cache_key, 'wgfw' );
		$filters = false;
		if(!$filters)
		{
			$_attributes = get_field('use_attributes','widget_' . $widget_id);

			if(!$_attributes or empty($_attributes))
			{
				$_temp = wc_get_attribute_taxonomies();
				$_attributes = array();
				if(is_product_taxonomy() && !is_product_category())
				{
					$_attributes[] = array(
						'attribute'      => 'product_cat',
						'attribute_name' => 'Категория',
						'hierarchical'   => true,
						'search'         => true,
					);
				}
				foreach($_temp as $_t)
				{
					$_attributes[] = array(
						'attribute'      => $_t->attribute_id,
						'attribute_name' => $_t->attribute_label,
						'hierarchical'   => false,
						'search'         => true,
					);
				}
				$filters['price'] = array(
					'name'              => 'Цена',
					'price_min'         => 0,
					'price_max'         => 0,
					'current_min_price' => 0,
					'current_max_price' => 0,
					'has_active'        => false,
					'type'              => 'slider',
				);
			}

			foreach($_attributes as $_attr)
			{
				if($_attr['attribute'] != '')
				{
					if($_attr['attribute'] == 'price')
					{
						$filters['price'] = array(
							'name'              => $_attr['attribute_name'] ? $_attr['attribute_name'] : 'Цена',
							'price_min'         => 0,
							'price_max'         => 0,
							'current_min_price' => 0,
							'current_max_price' => 0,
							'has_active'        => false,
							'type'              => 'slider',
						);
					}
					elseif($_attr['attribute'] == 'product_cat')
					{
						if(!is_product_category())
						{
							$hierarchical = $_attr['hierarchical'];
							$filters['product_cat'] = array(
								'name'         => $_attr['attribute_name'] ? $_attr['attribute_name'] : 'Категория',
								'has_active'   => false,
								'values'       => array(),
								'ordering'     => 'name',
								'hierarchical' => $hierarchical,
								'search'       => $_attr['search']
							);
						}
					}
					else
					{
						$attr = wc_get_attribute($_attr['attribute']);
						if(!is_null($attr))
						{
							$taxonomy = get_taxonomy($attr->slug);
							$hierarchical = false;
							if(!is_wp_error($taxonomy))
							{
								$hierarchical = $_attr['hierarchical'] && $taxonomy->hierarchical;
							}
							$filters[substr($attr->slug,3)] = array(
								'name'         => $_attr['attribute_name'] ? $_attr['attribute_name'] : $attr->name,
								'has_active'   => false,
								'values'       => array(),
								'ordering'     => $attr->order_by,
								'hierarchical' => $hierarchical,
								'search'       => $_attr['search'],
							);
						}
					}
				}
			}
			wp_cache_set( $widget_cache_key, $filters, 'wgfw', $this->cache_time );
		}
		$this->filters = $filters;
	}

	public function get_filter_elements_data($category_obj=null,$widget_id=0)
	{
		$http_request = $_REQUEST;

		$is_search = is_search();
		$is_shop = is_shop();

		if(!$is_search)
		{
			if(!$category_obj)
			{
				$category_obj = get_queried_object();
			}
			if($is_shop)
			{
				$category_obj = (object)array(
					'name' => 'catalog',
					'term_id' => 'catalog',
					'taxonomy' => 'catalog',
					'slug' => 'catalog',
				);

				$widget_cache_key = 'woocommerce_filters_widget_'.$widget_id.'_catalog';

				$this->_ids = wp_cache_get( $widget_cache_key, 'wgfw' );

				if(!$this->_ids)
				{
					$args = array(
						'post_type'   => 'product',
						'numberposts' => -1,
						'fields'      => 'ids',
					);
					$this->_ids = get_posts($args);
					wp_cache_set( $widget_cache_key, $this->_ids, 'wgfw', $this->cache_time );
				}
			}
			else
			{
				$category = $category_obj->term_id;

				$category_cache_key = $category_obj->taxonomy.'_'.$category_obj->term_id;

				$widget_cache_key = 'woocommerce_filters_widget_'.$widget_id.'_'.$category_cache_key;

				$this->_ids = wp_cache_get( $widget_cache_key, 'wgfw' );

				if(!$this->_ids)
				{
					$args = array(
						'post_type'   => 'product',
						'numberposts' => -1,
						'fields'      => 'ids',
						'tax_query'   => array(
							array(
								'taxonomy' => $category_obj->taxonomy,
								'field'    => 'id',
								'terms'    => $category
							)
						)
					);
					$this->_ids = get_posts($args);
					wp_cache_set( $widget_cache_key, $this->_ids, 'wgfw', $this->cache_time );
				}
			}
		}
		else
		{
			$q = get_search_query(false);
			$sslug = md5($q);
			$category_obj = (object)array(
				'name' => $q,
				'term_id' => 'search-'.$sslug,
				'taxonomy' => 'search',
				'slug' => 'search',
			);

			$category = $category_obj->term_id;

			$category_cache_key = $category_obj->taxonomy.'_'.$category_obj->term_id;

			$widget_cache_key = 'woocommerce_filters_widget_'.$widget_id.'_'.$category_cache_key;

			$this->_ids = wp_cache_get( $widget_cache_key, 'wgfw' );

			if(!$this->_ids)
			{
				$args = array(
					'post_type'   => 'product',
					'numberposts' => -1,
					'fields'      => 'ids',
					's' => get_search_query(),
				);
				$this->_ids = get_posts($args);
				wp_cache_set( $widget_cache_key, $this->_ids, 'wgfw', $this->cache_time );
			}
		}

		if($this->_ids)
		{
			foreach($this->filters as $slug => $filter)
			{
				$cc = 'pa_'.$slug == $category_obj->taxonomy;
				$cache_key = $widget_cache_key.'_'.$slug;
				if($slug == 'price')
				{
					$price = wp_cache_get( $cache_key, 'wgfw' );
					if(!$price)
					{
						$_ids_string = implode(',', $this->_ids);
						/* ▼ Minimal and maximal price in categories list ▼ */
						global $wpdb;

						$sql = "SELECT min( min_price ) as vMin, MAX( max_price ) as vMax FROM {$wpdb->wc_product_meta_lookup} WHERE product_id IN ({$_ids_string})";

						$temp = $wpdb->get_row( $sql, ARRAY_A );
						$price = array(
							'vMin' => floor($temp['vMin']),
							'vMax' => ceil($temp['vMax']),
						);
						wp_cache_set( $cache_key, $price, 'wgfw', $this->cache_time );
						unset($temp);
						unset($_ids_string);
					}

					$this->filters['price']['price_min'] = isset($price['vMin']) ? $price['vMin'] : 0;
					$this->filters['price']['price_max'] = isset($price['vMax']) ? $price['vMax'] : 0;

					$this->filters['price']['current_min_price'] = isset($http_request['min_price']) ? floatval($http_request['min_price']) : $this->filters['price']['price_min'];
					$this->filters['price']['current_max_price'] = isset($http_request['max_price']) ? floatval($http_request['max_price']) : $this->filters['price']['price_max'];

					if( ($this->filters['price']['current_min_price'] != $this->filters['price']['price_min']) || ($this->filters['price']['current_max_price'] != $this->filters['price']['price_max']) )
					{
						$this->filters['price']['has_active'] = true;
					}

				}
				elseif($slug == 'product_cat')
				{
					// $terms = wp_cache_get( $cache_key, 'wgfw' );
					$terms = false;
					$fslug = 'f_'.$slug;
					if(!$terms)
					{
						$_args = array(
							'taxonomy'        => 'product_cat',
							'object_ids'      => $this->_ids,
							'hide_empty'      => true,
							'pad_counts'      => true,
							'hierarchical'    => $filter['hierarchical'],
							'suppress_filter' => true,
							'cache_domain'    => $cache_key
						);
						if(!$filter['hierarchical'])
						{
							$datas = get_terms($_args);

							foreach($datas as $term)
							{
								$this->filters[$slug]['values'][] = array(
									'value'  => $term->slug,
									'name'   => $term->name,
									'active' => false,
									'depth'  => 0,
								);
								// $this->filters[$slug]['values'] = $t;
							}
							$terms = $this->filters[$slug]['values'];
							$terms = $this->order_by_terms($terms,$filter['ordering']);
						}
						else
						{
							$datas = $this->get_cat_tree($_args);
							$_terms = array();
							if($datas)
							{
								foreach($datas as $term)
								{
									$_terms = $this->rcinline($term,0,$_terms);
								}
							}
							foreach($_terms as $term)
							{
								$this->filters[$slug]['values'][] = array(
									'value'  => $term->slug,
									'name'   => $term->name,
									'active' => false,
									'depth'  => $term->depth,
								);
							}
							$terms = $this->filters[$slug]['values'];
						}
						wp_cache_set( $cache_key, $terms, 'wgfw', $this->cache_time );
					}
					if(count($terms) < 2)
					{
						unset($this->filters[$slug]);
					}
					else
					{
						foreach ($terms as $t_key => $term)
						{
							$active = false;
							if(isset($http_request[$fslug]))
							{
								$active = in_array($term['value'], $http_request[$fslug]);
							}
							if($cc && $term['value'] == $category_obj->slug)
							{
								$active = true;
							}
							if($active)
							{
								$terms[$t_key]['active'] = true;
								$this->filters[$slug]['has_active'] = true;
							}
						}
						$this->filters[$slug]['values'] = $terms;
					}
				}
				else
				{
					$terms = wp_cache_get( $cache_key, 'wgfw' );
					$fslug = 'f_'.$slug;
					if(!$terms)
					{
						$_args = array(
							'taxonomy'        => 'pa_'.$slug,
							'object_ids'      => $this->_ids,
							'hide_empty'      => true,
							'pad_counts'      => true,
							'hierarchical'    => $filter['hierarchical'],
							'suppress_filter' => true,
							'cache_domain'    => $cache_key
						);
						if(!$filter['hierarchical'])
						{
							$datas = get_terms($_args);

							foreach($datas as $term)
							{
								$this->filters[$slug]['values'][] = array(
									'value'  => $term->slug,
									'name'   => $term->name,
									'active' => false,
									'depth'  => 0,
								);
								// $this->filters[$slug]['values'] = $t;
							}
							$terms = $this->filters[$slug]['values'];
							$terms = $this->order_by_terms($terms,$filter['ordering']);
						}
						else
						{
							$datas = $this->get_cat_tree($_args);
							$_terms = array();
							if($datas)
							{
								foreach($datas as $term)
								{
									$_terms = $this->rcinline($term,0,$_terms);
								}
							}
							foreach($_terms as $term)
							{
								$this->filters[$slug]['values'][] = array(
									'value'  => $term->slug,
									'name'   => $term->name,
									'active' => false,
									'depth'  => $term->depth,
								);
							}
							$terms = $this->filters[$slug]['values'];
						}
						wp_cache_set( $cache_key, $terms, 'wgfw', $this->cache_time );
					}
					if(count($terms) < 2)
					{
						unset($this->filters[$slug]);
					}
					else
					{
						foreach ($terms as $__ => $term)
						{
							$active = false;
							if(isset($http_request[$fslug]))
							{
								$active = in_array($term['value'], $http_request[$fslug]);
							}
							if($cc && $term['value'] == $category_obj->slug)
							{
								$active = true;
							}
							if($active)
							{
								$terms[$__]['active'] = true;
								$this->filters[$slug]['has_active'] = true;
							}
						}
						$this->filters[$slug]['values'] = $terms;
					}
				}
			}

			if(array_key_exists('price', $this->filters))
			{
				$_price = get_field('price','widget_' . $widget_id);
				$this->filters['price']['type'] = $_price['type'];
				$_ranges = $_price['ranges'];
				$ranges = array();
				if($_ranges)
				{
					// $pf = get_woocommerce_price_format();
					$cs = get_woocommerce_currency_symbol();
					$lae = 0;
					$dp = $this->filters['price']['current_min_price'] == $this->filters['price']['price_min'] && $this->filters['price']['current_max_price'] == $this->filters['price']['price_max'];

					foreach($_ranges as $range)
					{
						if($range['start'] == '')
						{
							$range['start'] = $this->filters['price']['price_min'];
						}
						else
						{
							$range['start'] = floatval($range['start']);
						}
						if($range['end'] == '')
						{
							$range['end'] = $this->filters['price']['price_max'];
						}
						else
						{
							$range['end'] = floatval($range['end']);
						}
						if(
							($range['start'] >= $this->filters['price']['price_min'])
							&&
							($range['start'] <= $this->filters['price']['price_max'])
							// and
							// ($range['end'] >= $this->filters['price']['price_min'])
							// and
							// ($range['end'] <= $this->filters['price']['price_max'])
						){
							$checked = !$dp && $range['start'] == $this->filters['price']['current_min_price'] && $range['end'] == $this->filters['price']['current_max_price'];
							$name = '';
							if($range['start'] != $this->filters['price']['price_min'] && $range['end'] != $this->filters['price']['price_max'])
							{
								$name = $range['start'].$cs.' — '.$range['end'].$cs;
							}
							elseif($range['end'] != $this->filters['price']['price_max'])
							{
								$name = 'До '.$range['end'].$cs;
							}
							else
							{
								$name = 'От '.$range['start'].$cs;
							}
							$ranges[] = array(
								'min'    => $range['start'],
								'max'    => $range['end'],
								'name'   => $name,
								'active' => $checked,
							);
							if($checked)
							{
								$this->filters['price']['has_active'] = true;
							}
							$lae = $range['end'];
						}
					}
					if($lae > 0 && $lae < $this->filters['price']['price_max'])
					{
						$range = array(
							'start' => $lae,
							'end' => $this->filters['price']['price_max'],
						);
						$checked = !$dp && $range['start'] == $this->filters['price']['current_min_price'] && $range['end'] == $this->filters['price']['current_max_price'];
						$name = '';
						if($range['start'] != $this->filters['price']['price_min'] && $range['end'] != $this->filters['price']['price_max'])
						{
							$name = $range['start'].$cs.' — '.$range['end'].$cs;
						}
						elseif($range['end'] != $this->filters['price']['price_max'])
						{
							$name = 'До '.$range['end'].$cs;
						}
						else
						{
							$name = 'От '.$range['start'].$cs;
						}
						$ranges[] = array(
							'min'    => $range['start'],
							'max'    => $range['end'],
							'name'   => $name,
							'active' => $checked,
						);
						if($checked)
						{
							$this->filters['price']['has_active'] = true;
						}
					}
				}
				if(!empty($ranges))
				{
					$checked = $dp;
					array_unshift($ranges, array(
						'min'    => $this->filters['price']['price_min'],
						'max'    => $this->filters['price']['price_max'],
						'name'   => 'Все',
						'active' => $checked,
					));
				}
				elseif($this->filters['price']['type'] == 'ranges')
				{
					$this->filters['price']['type'] == 'slider';
				}
				$this->filters['price']['ranges'] = $ranges;
			}
		}
	}

	/*
	 * HELPERS
	 */

	public function order_by_terms($terms,$by)
	{
		if(!in_array($by, ['name','name_num','active','hierarchical']))
		{
			$by = 'name';
		}
		uasort($terms,array($this,'_order_by_terms_by_'.$by));
		return $terms;
	}

	private function _order_by_terms_by_active($a, $b)
	{
		if($a['active'] and $b['active'])
		{
			return 0;
		}
		if(!$a['active'] and $b['active'])
		{
			return 1;
		}
		if($a['active'] and !$b['active'])
		{
			return -1;
		}
	}

	public function _order_by_terms_by_name($a,$b)
	{
		$_a = $a['name'];
		$_b = $b['name'];
		$_a_ = $a['active'];
		$_b_ = $b['active'];
		$r = 0;
		if($_a > $_b)
		{
			$r = 1;
		}
		if($_a < $_b)
		{
			$r = -1;
		}
		if(!$_a_ and $_b_)
		{
			$r = 1;
		}
		if($_a_ and !$_b_)
		{
			$r = -1;
		}
		return $r;
	}

	public function _order_by_terms_by_name_num($a,$b)
	{
		$_a = floatval(preg_replace('/[\D|\.|\,]/','',$a['name']));
		$_b = floatval(preg_replace('/[\D|\.|\,]/','',$b['name']));
		$_a_ = $a['active'];
		$_b_ = $b['active'];
		$r = 0;
		if($_a > $_b)
		{
			$r = 1;
		}
		if($_a < $_b)
		{
			$r = -1;
		}
		if(!$_a_ and $_b_)
		{
			$r = 1;
		}
		if($_a_ and !$_b_)
		{
			$r = -1;
		}
		return $r;
	}

	private function get_cat_tree($args=array())
	{
		$RAWcategories = get_terms( $args );
		$RAWcategories2id = array();
		$categories = array();
		foreach($RAWcategories as $category)
		{
			$category->childs = array();
			$RAWcategories2id[$category->term_id] = $category;
			if($category->parent == 0)
			{
				$categories[$category->term_id] = $category;
			}
		}
		foreach ($RAWcategories2id as $id => $category)
		{
			if($category->parent != 0)
			{
				if(!array_key_exists($category->parent, $RAWcategories2id))
				{
					$t = get_term($category->parent);
					$t->childs = array();
					$RAWcategories2id[$t->term_id] = $t;
				}
				$RAWcategories2id[$category->parent]->childs[] = $category;
			}
		}
		if(empty($categories) && !array_key_exists('parent', $args) && !array_key_exists('child_of', $args))
		{
			$categories = $RAWcategories2id;
		}
		return $categories;
	}
 
	private function rcinline($taxonomy,$depth=0,$return='')
	{
		$return[] = $taxonomy;
		$taxonomy->depth = $depth;
		if(count($taxonomy->childs)>0)
		{
			foreach($taxonomy->childs as $child)
			{
				$return = $this->rcinline($child,$depth+1,$return);
			}
		}
		return $return;
	}

	private function clear_active_cache()
	{
		$widget_id = $this->id;

		$widget_cache_key = 'woocommerce_filters_widget_'.$widget_id.'_attributes';
		wp_cache_delete($widget_cache_key,'wgfw');
		$categories = get_terms(
			array(
				'taxonomy'     => 'product_cat',
				'hide_empty'   => false,
				'hierarchical' => true,
			)
		);
		$attributes = array('product_cat','price');
		$_temp = wc_get_attribute_taxonomies();
		foreach($_temp as $_t)
		{
			$tax = wc_attribute_taxonomy_name_by_id(intval($_t->attribute_id));
			$attributes[] = substr($tax, 3);
			if($_t->attribute_public)
			{
				$terms = get_terms(
					array(
						'taxonomy'     => $tax,
						'hide_empty'   => false,
						'hierarchical' => true,
					)
				);
				$categories = $categories + $terms;
			}
		}
		foreach($categories as $category_obj)
		{
			$category_cache_key = $category_obj->taxonomy.'_'.$category_obj->term_id;

			$widget_cache_key = 'woocommerce_filters_widget_'.$widget_id.'_'.$category_cache_key;
			wp_cache_delete($widget_cache_key,'wgfw');

			foreach($attributes as $slug)
			{
				$cache_key = $widget_cache_key.'_'.$slug;
				wp_cache_delete($cache_key,'wgfw');
			}

		}
	}
}
?>