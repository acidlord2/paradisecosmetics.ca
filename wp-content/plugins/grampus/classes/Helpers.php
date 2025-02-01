<?php

namespace GSE;

class Helpers
{
	protected static $_instance = null;

	function __construct()
	{
		remove_action( 'wp_head',				'rsd_link' );
		remove_action( 'wp_head',				'wp_generator' );
		remove_action( 'wp_head',				'wlwmanifest_link' );
		// remove_action( 'wp_head',				'wp_resource_hints', 2 );
		remove_action( 'wp_head',				'wp_shortlink_wp_head' );
		remove_action( 'wp_head',				'print_emoji_detection_script', 7 );
		remove_action( 'wp_head',				'wp_oembed_add_discovery_links', 10 );
		remove_action( 'wp_head',				'wp_oembed_add_discovery_links' );
		remove_filter( 'wp_mail',				'wp_staticize_emoji_for_email' );
		remove_action( 'wp_print_styles',		'print_emoji_styles' );
		remove_filter( 'the_content_feed',		'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss',		'wp_staticize_emoji' );
		remove_action( 'admin_print_styles',	'print_emoji_styles' );
		remove_action( 'admin_print_scripts',	'print_emoji_detection_script' );
		remove_action( 'wp_head',				'wp_oembed_add_host_js' );
		remove_action( 'wp_head',				'rest_output_link_wp_head', 10, 0 );
		remove_action( 'wp_head',				'wp_oembed_add_discovery_links' );
		remove_action( 'xmlrpc_rsd_apis',		'rest_output_rsd' );
		remove_action( 'template_redirect',		'rest_output_link_header', 11, 0 );

		// add_filter( 'intermediate_image_sizes', function($sizes){return array_filter($sizes,function($val){return 'medium_large' !== $val;});} );

		add_action( 'wp_before_admin_bar_render', array($this,'admin_bar_render_wplogo') );

		add_filter( 'nav_menu_item_id', '__return_empty_string' );
		add_filter( 'nav_menu_css_class', array($this, 'replace_menu_classes'), 10, 4 );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function admin_bar_render_wplogo()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
		if(!is_admin())
		{
			$wp_admin_bar->add_menu(
				array(
					'parent' => 'site-name',
					'id' => 'gs-settings',
					'title' => 'Настройки сайта',
					'href' => '/wp-admin/admin.php?page=gse-settings',
				)
			);
		}
	}

	function replace_menu_classes($classes, $item, $args, $depth)
	{
		global $current_archive;
		if(!$current_archive and is_single() and !is_singular(array('post', 'page')))
		{
			$current_archive = get_post_type_object(get_post_type());
		}
		$newclasses = array('nav-menu-element');
		if($current_archive and $item->type == 'post_type_archive')
		{
			if(get_post_type() == $item->object)
			{
				$newclasses[] = 'active';
			}
		}
		if( in_array('menu-item-has-children', $classes) && ($depth+1 < $args->depth) )
		{
			$newclasses[] = 'has-childs';
		}
		if( in_array('current-menu-item', $classes) )
		{
			$newclasses[] = 'active';
		}
		if( in_array('has-childs', $newclasses) and in_array('active', $newclasses) )
		{
			$newclasses[] = 'open';
		}
		if( in_array('current-menu-ancestor', $classes) )
		{
			$newclasses[] = 'active';
		}
		$newclasses = array_unique($newclasses);

		// $item->title = $this->->ds2w($item->title);
		return $newclasses;
	}

	public static function ds2w($var,$wrapper = 'br',$first = true)
	{
		if($wrapper == '\n')
		{
			return preg_replace('/([^\s]+)\\s\\s(.*)/', "$1\n$2", $var);
		}
		elseif($wrapper == 'br')
		{
			return preg_replace('/([^\s]+)\\s\\s(.*)/', '$1 <br>$2', $var);
		}
		else
		{
			if($first == true)
			{
				return preg_replace('/([^\s]+)\\s\\s(.*)/', '<'.$wrapper.'>$1</'.$wrapper.'> $2', $var);
			}
			else
			{
				return preg_replace('/([^\s]+)\\s\\s(.*)/', '$1 <'.$wrapper.'>$2</'.$wrapper.'>', $var);
			}
		}
	}

	public static function wrap($string = '', $s = 'p')
	{
		$rep = '<'.$s.'>$1</'.$s.'>';
		return preg_replace("/^(.*)$\n?/m", $rep, $string);
	}

	public static function random_str($length=12, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
		$pieces = [];
		$min = 0;
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i)
		{
			$pieces[] = $keyspace[random_int($min, $max)];
		}
		return implode('', $pieces);
	}

	/* Обрезалка текста */
    public static function str_cut($html,$max_length=100,$ender=false,$cut_to_word=false)
    {
        $isUtf8 = true;
        $space_ended = true;
        $result_length = 0;
        $position = 0;
        $add_ender = false;
        $tags = array();

        $output = '';

        $re = $isUtf8 ? '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;|[\x80-\xFF][\x80-\xBF]*}' : '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}';

        while($result_length < $max_length && preg_match($re, $html, $match, PREG_OFFSET_CAPTURE, $position))
        {
            list($tag, $tag_position) = $match[0];

            $str = substr($html, $position, $tag_position - $position);
            if ($result_length + strlen($str) > $max_length)
            {
                $output .= substr($str, 0, $max_length - $result_length);
                $result_length = $max_length;
                break;
            }

            $output .= $str;
            $result_length += strlen($str);
            if($result_length >= $max_length)
            {
                break;
            }

            if($tag[0] == '&' || ord($tag) >= 0x80)
            {
                $output .= $tag;
                $result_length++;
            }
            else
            {
                $tagName = $match[1][0];
                if($tag[1] == '/')
                {
                    $openingTag = array_pop($tags);
                    assert($openingTag == $tagName);
                    $output .= $tag;
                }
                elseif($tag[strlen($tag) - 2] == '/')
                {
                    $output .= $tag;
                }
                else
                {
                    $output .= $tag;
                    $tags[] = $tagName;
                }
            }
            $position = $tag_position + strlen($tag);
        }

        if($result_length < $max_length && $position < strlen($html))
        {
            $output .= substr($html, $position, $max_length - $result_length);
        }
        if($result_length == $max_length && $position < strlen($html))
        {
        	$add_ender = true;
        }

        if($cut_to_word)
        {
            $next_char = substr($html, $position+1,1);
            
            if(!preg_match('/\s/', $next_char))
            {
                $space_ended = false;
            }

            if(!$space_ended)
            {
                if(!empty($tags))
                {
                    $space_pos = strrpos($output,' ');
                    if($space_pos > 0)
                    {
                        $output = substr($output,0,$space_pos);
                    }
                }
                else
                {
                    $space_pos = strrpos($output,'<');
                    $space_pos = strrpos($output,' ',$space_pos);
                    if($space_pos > 0)
                    {
                        $output = substr($output,0,$space_pos);
                    }
                }
            }
        }

        if($ender and $add_ender)
        {
            $output = $output.(string)$ender;
        }
        while(!empty($tags))
        {
            $output .= '</'.array_pop($tags).'>';
        }
        return $output;
    }

	public static function format($type,$value)
	{
		switch ($type)
		{
			case 'phone':
				$p = preg_replace('/\D/','',$value);
				if($p[0] == '8'){ $p = '7'.mb_substr($p,1); }
				if($p[0] == '7'){ $p = '+'.$p; }
				return 'tel:'.$p;
				break;

			case 'email':
				return 'mailto:'.$value;
				break;

			case 'whatsapp':
				$p = preg_replace('/\D/','',$value);
				if($p[0] == '8'){ $p = '7'.mb_substr($p,1); }
				return 'https://wa.me/'.$p;
				break;

			case 'viber':
				$p = preg_replace('/\D/','',$value);
				if($p[0] == '8'){ $p = '7'.mb_substr($p,1); }
				return 'viber://chat?number=%2B'.$p;
				break;
			
			default:
				return $value;
				break;
		}
	}

	public static function get_terms_tree($args=array(),$modifier = false,$modVars=array())
	{
		$RAWelements = get_terms( $args );

		if(is_wp_error($RAWelements))
		{
			return [];
		}

		$RAWelements2id = array();
		$elements = array();

		$bp = false;

		if(array_key_exists('child_of', $args))
		{
			if(intval($args['child_of']) != 0)
			{
				$bp = true;
				$parent = get_term($args['child_of']);
				$parent->childs = array();
				$RAWelements2id[$parent->term_id] = $parent;
				$elements[$parent->term_id] = $parent;
			}
			// else
			// {
			// 	$parent = (object)array(
			// 		'term_id' => 0,
			// 		'parent' => 0,
			// 		'childs' => array(),
			// 	);
			// 	$RAWelements2id[0] = &$parent;
			// 	$elements[0] = &$parent;
			// }
		}

		foreach($RAWelements as $element)
		{
			$element->childs = array();
			$RAWelements2id[$element->term_id] = $element;
			if($element->parent == 0)
			{
				$elements[$element->term_id] = $element;
			}
		}
		foreach($RAWelements2id as $id => $element)
		{
			if($element->parent != 0)
			{
				$RAWelements2id[$element->parent]->childs[] = $element;
			}
		}
		if($modifier and is_callable($modifier))
		{
			$modVars['RAW'] =& $RAWelements2id;
			$modVars['elements'] =& $elements;
			call_user_func($modifier,$modVars);
		}
		if($bp)
		{
			return $elements[$args['child_of']]->childs;
		}
		return $elements;
	}

	public static function get_posts_tree($args=array(),$modifier = false,$modVars=array())
	{
		$RAWelements = get_pages( $args );
		$RAWelements2id = array();
		$elements = array();

		$bp = false;

		// if(array_key_exists('post_parent', $args))
		// {
		// 	$bp = 'post_parent';
		// 	$parent = get_post($args['post_parent']);
		// 	$parent->childs = array();
		// 	$RAWelements2id[$parent->ID] = $parent;
		// 	$elements[$parent->ID] = $parent;
		// }

		if(array_key_exists('child_of', $args))
		{
			$bp = 'child_of';
			$parent = get_post($args['child_of']);
			$parent->childs = array();
			$RAWelements2id[$parent->ID] = $parent;
			$elements[$parent->ID] = $parent;
		}

		if(array_key_exists('parent', $args))
		{
			$bp = 'parent';
			$parent = get_post($args['parent']);
			$parent->childs = array();
			$RAWelements2id[$parent->ID] = $parent;
			$elements[$parent->ID] = $parent;
		}

		foreach($RAWelements as $element)
		{
			$element->childs = array();
			$RAWelements2id[$element->ID] = $element;
			if($element->post_parent == 0)
			{
				$elements[$element->ID] = $element;
			}
		}

		foreach($RAWelements2id as $id => $element)
		{
			if($element->post_parent != 0)
			{
				$RAWelements2id[$element->post_parent]->childs[] = $element;
			}
		}

		if($modifier and is_callable($modifier))
		{
			$modVars['RAW'] =& $RAWelements2id;
			$modVars['elements'] =& $elements;
			call_user_func($modifier,$modVars);
		}

		if($bp != false)
		{
			return $elements[$args[$bp]]->childs;
		}
		return $elements;
	}
}
return true;