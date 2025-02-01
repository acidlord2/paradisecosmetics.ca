<?php

//=========== BASE CONFIG ============

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}


function theme_setup()
{

	load_theme_textdomain('theme', get_template_directory() . '/languages');

	add_theme_support('automatic-feed-links');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('widgets');
	add_theme_support('woocommerce');
	add_theme_support('widgets-block-editor');

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');
}
add_action('after_setup_theme', 'theme_setup');


function theme_scripts()
{

	wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.css', array(), filemtime(__DIR__ . '/assets/css/main.css'));
	wp_enqueue_style('fonts', get_template_directory_uri() . '/assets/fonts/fonts.css');
	wp_enqueue_style('swiperCss', get_template_directory_uri() . '/assets/css/swiper-bundle.min.css');
	wp_enqueue_style('fancybox', get_template_directory_uri() . '/assets/css/fancybox.css');

	wp_enqueue_script('swiperJs', get_template_directory_uri() . '/assets/js/swiper-bundle.min.js', array('jquery'), _S_VERSION, true);
	wp_enqueue_script('swiperJsCustom', get_template_directory_uri() . '/assets/js/swiper.js', array('jquery', 'swiperJs'), _S_VERSION, true);
	wp_enqueue_script('fancyboxJs', get_template_directory_uri() . '/assets/js/fancybox.umd.js', array('jquery'), _S_VERSION, true);
	wp_enqueue_script('inputmask', get_template_directory_uri() . '/assets/js/inputmask.js', array('jquery'), _S_VERSION, true);
	wp_enqueue_script('mobileMenu', get_template_directory_uri() . '/assets/js/modules/mobileMenu.js', array('jquery'), _S_VERSION, true);
	wp_enqueue_script('fnModal', get_template_directory_uri() . '/assets/js/modules/modal.js', array('jquery'), _S_VERSION, true);
	wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', array('jquery', 'mobileMenu', 'fnModal', 'fancyboxJs', 'inputmask'), filemtime(__DIR__ . '/assets/js/main.js'), true);
	wp_enqueue_script('wooNotices.js', get_template_directory_uri() . '/assets/js/wooNotices.js', array('jquery'), _S_VERSION, true);
}
add_action('wp_enqueue_scripts', 'theme_scripts');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


/*========= SUPPORT ES6 MODULES ===========*/
function scripts_as_es6_modules($tag, $handle, $src)
{

	if ('mobileMenu' === $handle || 'themeModal' === $handle || 'main' === $handle) {
		return str_replace('<script ', '<script type="module"', $tag);
	}

	return $tag;
}
// add_filter( 'script_loader_tag', 'scripts_as_es6_modules', 10, 3 );



/*========= ADD CANNONICAL LINKS ===========*/
add_filter('wpseo_canonical', 'return_canon');
function return_canon()
{
	if (is_paged()) {
		$canon_page = get_pagenum_link(1);
		return $canon_page;
	}
}


//============= THEME FUNCTIONS =============

require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/template-woocommerce.php';


function print_w_btn(string $link)
{
?>
	<a href="<?= $link ?>" class="btn-w">
		<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg">
			<g clip-path="url(#clip0_1_6503)">
				<path d="M0.5 8H29.5" stroke="white" />
				<path d="M30 8.25L23 1" stroke="white" />
				<path d="M23 15L30 7.75" stroke="white" />
			</g>
			<defs>
				<clipPath id="clip0_1_6503">
					<rect width="30" height="15" fill="white" transform="translate(0.5 0.5)" />
				</clipPath>
			</defs>
		</svg>
	</a>
<?php
}

/*=========== MENUS ==============*/

register_nav_menu('TopMenu', 'Верхнее меню');
register_nav_menu('TopCatalog', 'Каталог хедера');
register_nav_menu('footCat', 'Каталог подвал');
register_nav_menu('footMenu', 'Меню подвал');
register_nav_menu('mobileMenu', 'Мобильное меню');
