<?php

defined( 'ABSPATH' ) || exit;

class WC_Favorites_Link extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_favorites_link';
		$this->widget_description = 'Переход на страницу избранных';
		$this->widget_id          = 'woocommerce_favorites_link';
		$this->widget_name        = 'Ссылка на избранные';

		if ( is_customize_preview() ) {
			wp_enqueue_script( 'wc-cart-fragments' );
		}

		add_filter( 'woocommerce_add_to_favorites_fragments', array($this,'ajax_refresh_fragments') );

		parent::__construct();
	}

	public function ajax_refresh_fragments($fragments)
	{
		ob_start();
		wc_get_template('widgets/favorites/widget.php', array(), '', WCFAVORITES()->get_templates_path());
		$favorites_widget = ob_get_clean();
		$fragments['.widget_favorites_content'] = '<div class="widget_favorites_content">'.$favorites_widget.'</div>';

		return $fragments;
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		// Insert favorites widget placeholder - code in add2favorites.js will update this on page load.
		echo '<div class="widget_favorites_content"></div>';

		$this->widget_end( $args );
	}
}
