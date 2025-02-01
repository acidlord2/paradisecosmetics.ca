<?php

namespace GSE;

class Secure
{
	protected static $_instance = null;

	private static $link_replaceable_files = array('wp-login.php','wp-includes/general-template.php','wp-includes/link-template.php','wp-includes/post-template.php','wp-includes/pluggable.php','wp-includes/user.php');
	private static $removable_files = array('xmlrpc.php','wp-config-sample.php','readme.html','license.txt');

	function __construct()
	{
		add_action( 'automatic_updates_complete', array(__CLASS__, 'secure_auth') );
		add_action( '_core_updated_successfully', array(__CLASS__, 'secure_auth') );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function secure_auth()
	{
		/* REPLACE `wp-login.php` to `auth.php` link */
		foreach(self::$link_replaceable_files as $filename)
		{
			$path = ABSPATH.$filename;
			if(file_exists($path))
			{
				$content = file_get_contents($path);
				$content = preg_replace('/wp-login\.php/', 'auth.php', $content);
				file_put_contents($path, $content);
			}
		}
		/* REPLACE `wp-login.php` to `auth.php` link */

		/* REPLACE `wp-login.php` file */
		$a = ABSPATH.'auth.php';
		$w = ABSPATH.'wp-login.php';
		if(file_exists($a) and file_exists($w))
		{
			unlink($a);
		}
		if(file_exists($w))
		{
			rename($w, $a);
		}
		/* REPLACE `wp-login.php` file */

		/* SKIP `xmlrpc.php` file if JETPACK exists */
		$removable_files = self::$removable_files;
		if( in_array('jetpack/jetpack.php', apply_filters('active_plugins', get_option('active_plugins'))) )
		{
			if(($key = array_search('xmlrpc.php', $removable_files)) !== false)
			{
				unset($removable_files[$key]);
			}
		}
		/* SKIP `xmlrpc.php` file if JETPACK exists */

		/* DELETE files */
		foreach($removable_files as $filename)
		{
			$path = ABSPATH.$filename;
			if(file_exists($path))
			{
				unlink($path);
			}
		}
		/* DELETE files */
	}
}
return true;