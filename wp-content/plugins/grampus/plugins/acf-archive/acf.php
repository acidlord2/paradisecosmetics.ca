<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Name: ACF Archive
 * Plugin URI: https://www.imark.co.il/
 * Description: ACF Archives is a little plugin for helping you attach ACF fields to the archive template.
 * Text Domain: acf-archive
 * Version: 1.0.5
 * Author: Imark Image
 * Author URI: https://www.imark.co.il
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

class ACF_Archive {
	/**
	 * ACF_Archive constructor.
	 */
	private function __construct() {
		add_action( 'after_setup_theme', [ $this, 'boot' ] );
	}

	/**
	 * @return \ACF_Archive
	 */
	public static function instance() {
		static $instance;

		if ( null !== $instance ) {
			return $instance;
		}

		return $instance = new static;
	}

	/**
	 * Start the plugin
	 * @return void
	 */
	public function boot() {
		$this->post_types = array();
		if ( ! class_exists( 'ACF' ) ) {
			add_action( 'admin_notices', [ $this, 'acf_installed_notify' ] );
			return;
		}

		add_action( 'init', [ $this, 'get_custom_post_types' ], 4 );
		add_action( 'acf/init', [ $this, 'add_archive_option_page' ] );
		add_action( 'admin_bar_menu', [ $this, 'add_view_link' ], 900 );
		// add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		// add_action( 'acf/input/admin_footer', [ $this, 'admin_footer' ], 10, 1 );
		// add_filter( 'acf/location/rule_types', [ $this, 'location_rules_types' ] );
		// add_filter( 'acf/location/rule_values/admin_page', [ $this, 'location_rules_values_archive' ] );
		// add_filter( 'acf/location/rule_match/admin_page', [ $this, 'location_rules_match_archive' ], 10, 3);
	}

	/**
	 * @return void
	 */
	public function acf_installed_notify() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo __( '<strong>ACF Archive is not working</strong>, Advanced Custom Fields is not installed.', 'acf-archive' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Loop for each matching CPT to add menu
	 * @return void
	 */
	public function add_archive_option_page()
	{
		foreach($this->post_types as $post_type => $post_type_object)
		{
			$menu = 'edit.php?post_type=' . $post_type;

			$this->add_menu($post_type_object->label, $menu, $post_type);
		}
	}

	/**
	 * Add ACF menu page for each custom post type
	 *
	 * @param string $label
	 * @param string $menu
	 */
	private function add_menu( $label, $menu, $post_type )
	{
		$page_name = "Параметры";

		$options = array(
			'parent_slug' => $menu,
			'page_title'  => $page_name,
			'menu_title'  => $page_name,
			// 'capability'  => 'edit_posts',
			'menu_slug'   => 'archive_'.$post_type,
		);
		acf_add_options_page(
			array(
				'page_title'        => 'Архив: '.$label,
				'menu_title'        => $options['menu_title'],
				'menu_slug'         => $options['menu_slug'],
				'post_id'           => $post_type,
				'capability'        => 'manage_options',
				'parent_slug'       => $options['parent_slug'],
				'redirect'          => false,
				'autoload'          => true,
				'update_button'     => 'Обновить',
				'updated_message'   => 'Параметры обновлены',
			)
		);
	}

	public function add_view_link()
	{
		if(!is_admin()) return;

		$screen = get_current_screen();

		if(isset($_GET['page']) && isset($_GET['post_type']) && strpos('archive_',$_GET['page']) !== false && $_GET['post_type'] == $screen->post_type)
		{
			global $wp_admin_bar;
			$wp_admin_bar->add_menu(
				array(
					'id' => 'view-'.$screen->post_type,
					'parent' => '',
					'title' => 'Просмотреть',
					'href' => get_post_type_archive_link($screen->post_type),
				)
			);
		}
	}

	/**
	 * Get all the custom post types with archive
	 *
	 * @return array
	 */
	public function get_custom_post_types()
	{
		if(!empty($this->post_types))
		{
			return $this->post_types;
		}

		$args = array(
			'public' => true,
			'has_archive' => true,
			'_builtin' => false,
		);

		$this->post_types = get_post_types( $args, 'objects' );

		return $this->post_types;

		// return $post_types = apply_filters( 'acf_archive_post_types', $post_types );
	}
}

ACF_Archive::instance();
