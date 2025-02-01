<?php

namespace GSE;

class Slider
{
	protected static $_instance = null;

	function __construct()
	{
		add_action( 'acf/init', array($this, 'register_acf') );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function sliders_table_head( $columns )
	{
		if(array_key_exists('date', $columns))
		{
			unset($columns['date']);
		}
		$columns['shortcode'] = 'Shortcode';
		return $columns;
	}

	public function sliders_table_content( $column_name, $post_id ) {
		if($column_name == 'shortcode')
		{
			echo '<span class="shortcode"><input type="text" onfocus="this.select();" readonly="readonly" value="'.esc_attr('[gslider id="'.$post_id.'"]').'" class="large-text code" /></span>';
		}
	}

	private function register_acf()
	{
		register_post_type(
			'gse-slider',
			array(
				'label'	=> 'Слайдеры',
				'labels' => array(
					'name'				=> 'Слайдеры',
					'singular_name'		=> 'Слайдер',
					'add_new'			=> 'Добавить',
					'add_new_item'		=> 'Добавить слайдер',
					'edit_item'			=> 'Редактировать слайдер',
					'new_item'			=> 'Новый слайдер',
					'view_item'			=> 'Просмотреть слайдер',
					'parent_item_colon'	=> 'Слайдеры',
					'menu_name'			=> 'Слайдеры',
				),
				'description'			=> '',
				'public'				=> false,
				'publicly_queryable'	=> true,
				'exclude_from_search'	=> false,
				'show_ui'				=> true,
				'show_in_menu'			=> null, //'themes.php',
				'show_in_admin_bar'		=> null,
				'show_in_nav_menus'		=> false,
				'show_in_rest'			=> false,
				'rest_base'				=> false,
				'menu_position'			=> null,
				'menu_icon'				=> 'dashicons-images-alt',
				'hierarchical'			=> false,
				'supports'				=> array('title','custom-fields'),
				'taxonomies'			=> array(),
				'has_archive'			=> false,
				'rewrite'				=> false,
				'query_var'				=> false,
				'delete_with_user'		=> false,
				'capability_type'		=> 'post',
				'hierarchical'			=> false, 
			)
		);

		add_filter('manage_gs-sliders_posts_columns', [$this,'sliders_table_head']);
		add_action('manage_gs-sliders_posts_custom_column', [$this,'sliders_table_content'], 10, 2 );
		add_shortcode('gslider',[$this,'gslider_shortcode']);


		$config = GSE::pluging_path().'configs/slider.json';
		$config = file_get_contents($config);
		$config = json_decode($config,true);

		acf_add_local_field_group($config[0]);
	}
}
return true;