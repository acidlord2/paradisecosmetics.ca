<?php

namespace GSE;

class Assets
{
	protected static $_instance = null;

	public function __construct(){}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private static function asset_path($path)
	{
		$rpath = '/assets/'.$path;
		$rpath = preg_replace('~//~', '/', $rpath);
		return $rpath;
	}

	public static function asset_version($path)
	{
		$version = null;
		if(file_exists($path))
		{
			$version = date('YmdHis',filemtime($path));
		}
		return $version;
	}

	public static function asset($path)
	{
		$rpath = self::asset_path($path);
		return GSE()::theme_uri().$rpath;
	}

	public static function assetv($path)
	{
		$rpath = self::asset_path($path);
		$version = self::asset_version($path);
		if($version)
		{
			$rpath = '?timestamp='.$version;
		}
		return GSE()::theme_uri().$rpath;
	}

	public static function inline($include='',$echo=true)
	{
		$path = GSE()::theme_path().'/'.$include;
		if(file_exists($path))
		{
			if($echo)
			{
				echo file_get_contents($path);
			}
			else
			{
				return file_get_contents($path);
			}
		}
	}
}
return true;