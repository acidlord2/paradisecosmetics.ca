<?php

// ================= ACTIONS ====================



// ================= ACTIONS FUNCSTIONS ====================




// ================= ФИЛЬТРЫ ====================


add_filter('excerpt_more', function($more) {
    return '...';
});
add_filter( 'excerpt_length', function(){
    return 25;
} );


// ================ FUNCSTIONS ===============

/*-------- ГЕНЕРАЦИЯ ID БЛОКА -----------*/
function blockId($block) {
    if(!$block) {
        return;
    }
    $blockNum = $block . '-block-0';

    $blockName = $block . '-block';

    if(array_key_exists($blockName, $GLOBALS) && !empty($GLOBALS[$blockName])) {
        $blockNum = $block . '-block-' . count($GLOBALS[$blockName]);
        $GLOBALS[$blockName][] = $blockNum;
    }else{
        $GLOBALS[$blockName][] = $blockNum;
    }

    return $blockNum;
}



/*------- ПОЛУЧЕНИЕ КОНТЕНТА С ОПРЕДЕЛЁННОЙ СТРАНИЦЫ ----------*/

function get_page_content($page_id) {
	if(!$page_id) {
		return;
	}
	$content = get_the_content( '', false, $page_id  );
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	echo $content;
}


/*------- ПОЛУЧЕНИЕ ФОРМЫ ----------*/

function get_form($formname = '', $params = []) {
	$echo = true;
	
	if(array_key_exists('echo', $params)) {
		$echo = $params['echo'];
	}
	
	if(!$formname) {
		if($echo === true) {
			echo 'Форма не найдена!';
            return '';
		}else{
			return false;
		}
	}
	
	if($echo) {
		get_template_part('inc/parts/forms/form', $formname, $params);
	}else{
		ob_start();
		get_template_part('inc/parts/forms/form', $formname, $params);
		$out = ob_get_clean();
		return $out;
	}
}


/*-------- ПЕРЕВОД ПОЛЕЙ ---------*/

if( function_exists('GSE') ) {
    GSE()::add_translation('subject','Тема письма');
    GSE()::add_translation('your-name','Имя');
    GSE()::add_translation('your-tel','Телефон');
    GSE()::add_translation('message','Сообщение');
}




// ============== ADD THEME PAGE ===============

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'        => 'Параметры темы',
        'menu_title'        => 'Параметры темы',
        'menu_slug'         => 'gs-theme-params',
        'capability'        => 'manage_options',
        'parent_slug'       => 'themes.php',
        'icon_url'          => 'dashicons-location-alt',
        'redirect'          => false,
        'autoload'          => true,
        'update_button'     => 'Обновить',
        'updated_message'   => 'Параметры темы обновлены',
    ));
}


function theme($type)
{
    $setting = get_field($type,'options');
    if($setting)
    {
        return $setting;
    }
    else
    {
        return '';
    }
}




// =========== РЕГИСТРАЦИЯ БЛОКОВ ===============


add_filter('block_categories_all', 'add_blocks_category', 10 );

function add_blocks_category($categories)
{

    $categories[] = array(
        'slug'  => 'theme-blocks',
        'title' => 'Блоки темы',
        'icon'  => null,
    );

    return $categories;
}

function add_blocks()
{
    $ignore = array('.','..');
    $bpath = __DIR__.'/blocks/';
    $blocks = scandir($bpath);

    foreach ($blocks as $folder)
    {
        if(!in_array($folder, $ignore))
        {
            if(file_exists($bpath.$folder.'/index.php'))
            {
                    // $this->blocks[$folder] = require_once $bpath.$folder.'/index.php';
                require_once $bpath.$folder.'/index.php';

            }
        }
    }
}
add_blocks();

function wide_Setup() {
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'wide_Setup' );

// Добавляем кастомные размеры изображений
function custom_image_sizes()
{
	add_image_size('fhd', 1920, 9999); // 9999 означает произвольную высоту
	add_image_size('hd', 1200, 9999);
}
add_action('after_setup_theme', 'custom_image_sizes');




add_action('wp_ajax_load_more', 'load_more');
add_action('wp_ajax_nopriv_load_more', 'load_more');


function load_more()
{
    $page = $_POST['page'];
    $post_type = $_POST['post_type'];
    $numberposts = $_POST['numberposts'];
    $template_name = $_POST['template_name'];
    $template_name = 'inc/parts/' . $template_name;

    $category = $_POST['category'];
    $catid = intval($_POST['catid']);


    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => $numberposts,
        'paged'          => $page,
    );

    if (isset($_POST['category'])) {
        $args['tax_query'] = [
            [
                'taxonomy' => $category,
                'field' => 'term_id',
                'terms' => $catid,
            ],
        ];
    };

    $products_query = new WP_Query($args);

    $hide = false;
    if (count($products_query->posts) < $numberposts) {
        $hide = true;
    };
    if ($category == '') {
        if (wp_count_posts($post_type)->publish == ($page - 1) * $numberposts +  count($products_query->posts)) {
            $hide = true;
        }
    } else {
        $args = array(
            'post_type' => $post_type,
            'tax_query' => array(
                array(
                    'taxonomy' => $category,
                    'field'    => 'id',
                    'terms'    => $catid,
                ),
            ),
        );
        $query = new WP_Query($args);
        $count = $query->found_posts;
        if ($count == ($page - 1) * $numberposts +  count($products_query->posts)){
            $hide = true;
        }
    }
    ob_start();
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $post = get_post();
            get_template_part($template_name, null, $post);
        }
    };
    $posts = ob_get_clean();

    wp_send_json([
        'status' => 200,
        'posts' => $posts,
        'hide' => $hide
    ]);
}
