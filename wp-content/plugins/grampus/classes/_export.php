<?php

if(!function_exists('settings') )
{
	function settings()
	{
		$args = func_get_args();
		return GSE\Settings::get_settings(...$args);
	}
}

if(!function_exists('privacy_link') )
{
	function privacy_link()
	{
		$args = func_get_args();
		return GSE\Settings::privacy_link(...$args);
	}
}

if(!function_exists('render_map') )
{
	function render_map()
	{
		$args = func_get_args();
		return GSE\Settings::render_map(...$args);
	}
}

if(!function_exists('assetv') )
{
	function assetv()
	{
		$args = func_get_args();
		return GSE\Assets::assetv(...$args);
	}
}

if(!function_exists('asset') )
{
	function asset()
	{
		$args = func_get_args();
		return GSE\Assets::asset(...$args);
	}
}

if(!function_exists('format') )
{
	function format()
	{
		$args = func_get_args();
		return GSE\Helpers::format(...$args);
	}
}


if(!function_exists('f') )
{
	function f()
	{
		_doing_it_wrong( __FUNCTION__, __( 'Функция f заменена на функцию format' ), '2.0.0' );
		$args = func_get_args();
		return GSE\Helpers::format(...$args);
	}
}

if(!function_exists('ds2w') )
{
	function ds2w()
	{
		$args = func_get_args();
		return GSE\Helpers::ds2w(...$args);
	}
}

if(!function_exists('inline') )
{
	function inline()
	{
		$args = func_get_args();
		return GSE\Assets::inline(...$args);
	}
}

if(!function_exists('get_thumb_lightbox') )
{
	function get_thumb_lightbox()
	{
		$args = func_get_args();
		return GSE\Images::get_thumb_lightbox(...$args);
	}
}

if(!function_exists('the_thumb_lightbox') )
{
	function the_thumb_lightbox()
	{
		$args = func_get_args();
		return GSE\Images::the_thumb_lightbox(...$args);
	}
}

if(!function_exists('get_thumb') )
{
	function get_thumb()
	{
		$args = func_get_args();
		return GSE\Images::get_thumb(...$args);
	}
}

if(!function_exists('the_thumb') )
{
	function the_thumb()
	{
		$args = func_get_args();
		return GSE\Images::the_thumb(...$args);
	}
}

if(!function_exists('get_thumb_src') )
{
	function get_thumb_src()
	{
		$args = func_get_args();
		return GSE\Images::get_thumb_src(...$args);
	}
}

if(!function_exists('the_thumb_src') )
{
	function the_thumb_src()
	{
		$args = func_get_args();
		return GSE\Images::the_thumb_src(...$args);
	}
}

if(!function_exists('get_image') )
{
	function get_image()
	{
		$args = func_get_args();
		return GSE\Images::get_image(...$args);
	}
}

if(!function_exists('the_image') )
{
	function the_image()
	{
		$args = func_get_args();
		return GSE\Images::the_image(...$args);
	}
}

if(!function_exists('get_image_lightbox') )
{
	function get_image_lightbox()
	{
		$args = func_get_args();
		return GSE\Images::get_image_lightbox(...$args);
	}
}

if(!function_exists('the_image_lightbox') )
{
	function the_image_lightbox()
	{
		$args = func_get_args();
		return GSE\Images::the_image_lightbox(...$args);
	}
}

if(!function_exists('get_image_src') )
{
	function get_image_src()
	{
		$args = func_get_args();
		return GSE\Images::get_image_src(...$args);
	}
}

if(!function_exists('the_image_src') )
{
	function the_image_src()
	{
		$args = func_get_args();
		return GSE\Images::the_image_src(...$args);
	}
}

if(!function_exists('get_image_src_ws') )
{
	function get_image_src_ws()
	{
		$args = func_get_args();
		return GSE\Images::get_image_src_ws(...$args);
	}
}

if(!function_exists('get_svg') )
{
	function get_svg()
	{
		$args = func_get_args();
		return GSE\Images::get_svg(...$args);
	}
}

if(!function_exists('inline_svg') )
{
	function inline_svg()
	{
		$args = func_get_args();
		return GSE\Images::inline_svg(...$args);
	}
}

if(!function_exists('get_terms_tree') )
{
	function get_terms_tree()
	{
		$args = func_get_args();
		return GSE\Helpers::get_terms_tree(...$args);
	}
}

if(!function_exists('get_posts_tree') )
{
	function get_posts_tree()
	{
		$args = func_get_args();
		return GSE\Helpers::get_posts_tree(...$args);
	}
}