<?php

namespace GSE;

class Images
{
	protected static $_instance = null;

	function __construct()
	{
		add_action( 'wp_enqueue_scripts', array($this, 'frontend_styles_and_scripts') );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function frontend_styles_and_scripts()
	{
		$FS_path = GSE()::plugin_path().'assets/js/lightbox.js';
		$URL_path = GSE()::plugin_uri().'assets/js/lightbox.js';
		$version = @filemtime($FS_path);
		wp_register_script('lightbox', $URL_path, array('jquery'), $version);

		$FS_path = GSE()::plugin_path().'assets/css/lightbox.css';
		$URL_path = GSE()::plugin_uri().'assets/css/lightbox.css';
		$version = @filemtime($FS_path);
		wp_register_style('lightbox', $URL_path, array(), $version);
	}

	public static function generate_image($image_id, $size)
	{
		if(!$image_id)
		{
			return false;
		}

		if(!is_array($size))
		{
			return $size;
		}

		if($size == 'full')
		{
			return $size;
		}

		$meta = wp_get_attachment_metadata($image_id);

		if($size[1] >= $meta['height'] && $size[0] >= $meta['width'])
		{
			return 'full';
		}

		$sized = wp_get_attachment_image_src($image_id, $size);

		return $size;
	}

	public static function the_thumb_lightbox($post=0,$size=false,$key='')
	{
		echo get_thumb_lightbox($post,$size,$key);
	}

	public static function get_thumb_lightbox($post=0,$size=false,$key='')
	{
		$post = get_post( $post );
		$id = get_post_thumbnail_id($post);
		return get_image_lightbox($id,$size,$key);
	}

	public static function the_thumb($post=0,$size=false)
	{
		echo get_thumb($post,$size);
	}

	public static function get_thumb($post=0,$size=false)
	{
		$post = get_post( $post );
		$id = get_post_thumbnail_id($post);
		return get_image($id,$size);
	}

	public static function the_thumb_src($post=0,$size=false)
	{
		echo get_thumb_src($post,$size);
	}

	public static function get_thumb_src($post=0,$size=false)
	{
		$post = get_post( $post );
		$id = get_post_thumbnail_id($post);
		return get_image_src($id,$size);
	}

	public static function the_image($id,$size=false)
	{
		echo get_image($id,$size);
	}

	public static function get_image($id,$size=false)
	{
		if(is_array($id) and array_key_exists('ID', $id))
		{
			$id = $id['ID'];
		}
		if(get_post_mime_type($id) == 'image/svg+xml')
		{
			$path = get_attached_file($id);
			if(file_exists($path))
			{
				return @file_get_contents($path);
			}
		}
		$attr = array();
		$attr['src'] = get_image_src($id,$size);
		if($attr['src'] == '')
		{
			return '';
		}
		$attr['alt'] = get_post_meta($id, '_wp_attachment_image_alt', true);
		$attr['title'] = get_the_title($id);

		$html = '<img';
		foreach ($attr as $key => $value)
		{
			$html .= " {$key}=\"{$value}\"";
		}
		$html .= ' />';
		return $html;
	}

	public static function the_image_lightbox($id,$size=false,$key='')
	{
		echo get_image_lightbox($id,$size,$key);
	}

	public static function get_image_lightbox($id,$size=false,$key='')
	{

		$attr['src'] = get_image_src($id,$size);

		if($attr['src'] == '')
		{
			return '';
		}
		$attr['title'] = get_the_title($id);
		$attr['alt'] = get_post_meta($id, '_wp_attachment_image_alt', true) || $attr['title'];
		// $attr['data-caption'] = $attr['title'];

		$lightbox = get_image_src($id,'full');
		if($lightbox)
		{
			$attr['data-src'] = $lightbox;
			if($key)
			{
				$attr['data-fancybox'] = $key;
			}
			else
			{
				$attr['data-fancybox'] = 'lightbox';
			}
			wp_enqueue_script('lightbox');
			wp_enqueue_style('lightbox');
		}

		$html = '<img';
		foreach($attr as $key => $value)
		{
			$html .= " {$key}=\"{$value}\"";
		}
		$html .= ' />';
		return $html;
	}

	public static function the_image_src($id,$size=false)
	{
		echo get_image_src($id,$size);
	}

	public static function get_image_src($id,$size=false)
	{
		if($size != false)
		{
			$size = self::generate_image($id,$size);
			$src = wp_get_attachment_image_src($id,$size);
		}
		else
		{
			$src = wp_get_attachment_image_src($id);
		}
		if(is_array($src) and isset($src[0]))
		{
			return $src[0];
		}
		return '';
	}

	public static function get_image_src_ws($id,$size=false)
	{
		if($size != false)
		{
			$size = self::generate_image($id,$size);
			$src = wp_get_attachment_image_src($id,$size);
		}
		else
		{
			$src = wp_get_attachment_image_src($id);
		}
		if(is_array($src) and isset($src[0]))
		{
			return $src;
		}
		return '';
	}

	public static function get_svg($post)
	{
		$post = get_post( $post );
		$path = wp_get_attachment_image_src($post->ID);
		if(is_array($path) and isset($path[0]))
		{
			$path = $path[0];
		}
		else
		{
			return '';
		}
		$path = preg_split('~\/wp-content\/~', $path);
		$path = ABSPATH.'/wp-content/'.$path[1];
		if(file_exists($path))
		{
			return file_get_contents($path);
		}
		return '';
	}

	public static function inline_svg($post)
	{
		$post = get_post( $post );
		$path = wp_get_attachment_image_src($post->ID);
		if(is_array($path) and isset($path[0]))
		{
			$path = $path[0];
		}
		else
		{
			return '';
		}
		$path = preg_split('~\/wp-content\/~', $path);
		$path = ABSPATH.'/wp-content/'.$path[1];
		if(file_exists($path))
		{
			return file_get_contents($path);
		}
		return '';
	}
}
return true;