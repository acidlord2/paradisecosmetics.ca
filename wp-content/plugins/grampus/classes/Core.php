<?php

namespace GSE;

require_once __DIR__.'/Cracks.php';
require_once __DIR__.'/Assets.php';
require_once __DIR__.'/Plugins.php';
require_once __DIR__.'/Settings.php';
require_once __DIR__.'/Form.php';
require_once __DIR__.'/Helpers.php';
require_once __DIR__.'/Images.php';
require_once __DIR__.'/Update.php';
require_once __DIR__.'/Secure.php';
require_once __DIR__.'/Widgets.php';

class Core
{
	protected static $_instance = null;

	private static $Cracks;
	private static $Assets;
	private static $Plugins;
	private static $Settings;
	private static $Form;
	private static $Helpers;
	private static $Images;
	private static $Update;
	private static $Secure;
	private static $Widgets;

	private static $translations = array(
		'phone'	     => 'Телефон',
		'email'	     => 'E-mail',
		'notice'     => 'E-mail уведомлений',
		'address'    => 'Адрес',
		'call'	     => 'Обратный звонок',
		'request'    => 'Заявка',
		'calc'       => 'Расчет стоимости',
		'order'      => 'Заказ',
		'name'	     => 'Имя',
		'message'    => 'Сообщение',
	);

	protected static $_plugin_path = null;
	protected static $_plugin_uri = null;
	protected static $_theme_path = null;
	protected static $_theme_uri = null;
	protected static $_uploads_path = null;
	protected static $_uploads_uri = null;
	protected static $_form_uploads_path = null;
	protected static $_form_uploads_uri = null;

	public $theme = null;
	public $theme_uri = null;

	function __construct()
	{
		if( is_null(self::$Cracks) )
		{
			self::$Cracks = Cracks::instance();
		}
		if( is_null(self::$Plugins) )
		{
			self::$Plugins = Plugins::instance();
		}
		if( is_null(self::$Images) )
		{
			self::$Images = Images::instance();
		}
		if( is_null(self::$Helpers) )
		{
			self::$Helpers = Helpers::instance();
		}
		if( is_null(self::$Assets) )
		{
			self::$Assets = Assets::instance();
		}
		if( is_null(self::$Settings) )
		{
			self::$Settings = Settings::instance();
		}
		if( is_null(self::$Widgets) )
		{
			self::$Widgets = Widgets::instance();
		}
		if( is_null(self::$Form) )
		{
			self::$Form = Form::instance();
		}
		if( is_null(self::$Secure) )
		{
			self::$Secure = Secure::instance();
		}
		$this->theme = self::theme_path();
		$this->theme_uri = self::theme_uri();
		add_action( 'wp_enqueue_scripts', array($this, 'frontend_styles_and_scripts') );
	}

	public function frontend_styles_and_scripts()
	{
		$FS_path = GSE()::plugin_path().'assets/js/extends.js';
		$URL_path = GSE()::plugin_uri().'assets/js/extends.js';
		$version = @filemtime($FS_path);
		wp_register_script('extends', $URL_path, array('jquery'), $version);

		wp_enqueue_script('extends');
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function activation()
	{
		Secure::secure_auth();
	}

	public static function deactivation()
	{
		Update::clear_transients(null, 'deactivation');
	}

	public static function plugin_version()
	{
		return GSE_VERSION;
	}

	public static function plugin_fullpath()
	{
		return self::plugin_path().'grampus.php';
	}

	public static function plugin_path()
	{
		if(is_null(self::$_plugin_path))
		{
			self::$_plugin_path = trailingslashit( plugin_dir_path( __DIR__ ) );
		}
		return self::$_plugin_path;
	}

	public static function plugin_uri()
	{
		if(is_null(self::$_plugin_uri))
		{
			self::$_plugin_uri = trailingslashit( plugins_url('',__DIR__) );
		}
		return self::$_plugin_uri;
	}

	public static function theme_path()
	{
		if(is_null(self::$_theme_path))
		{
			self::$_theme_path = trailingslashit( get_template_directory() );
		}
		return self::$_theme_path;
	}

	public static function theme_uri()
	{
		if(is_null(self::$_theme_uri))
		{
			self::$_theme_uri = trailingslashit( get_template_directory_uri() );
		}
		return self::$_theme_uri;
	}

	public static function uploads_path()
	{
		if(is_null(self::$_uploads_path))
		{
			self::$_uploads_path = trailingslashit( wp_upload_dir()['basedir'] );
		}
		return self::$_uploads_path;
	}

	public static function uploads_uri()
	{
		if(is_null(self::$_uploads_uri))
		{
			self::$_uploads_uri = trailingslashit( wp_upload_dir()['baseurl'] );
		}
		return self::$_uploads_uri;
	}

	public static function form_uploads_path()
	{
		if(is_null(self::$_form_uploads_path))
		{
			self::$_form_uploads_path = trailingslashit( self::uploads_path() . 'form' );
		}
		return self::$_form_uploads_path;
	}

	public static function form_uploads_uri()
	{
		if(is_null(self::$_form_uploads_uri))
		{
			self::$_form_uploads_uri = trailingslashit( self::uploads_uri() . 'form' );
		}
		return self::$_form_uploads_uri;
	}

	public static function add_translation($key,$value)
	{
		self::$translations[$key] = $value;
	}

	public static function trans($key)
	{
		if(array_key_exists($key, self::$translations))
		{
			return self::$translations[$key];
		}
		else
		{
			return $key;
		}
	}

	public static function ds2w()
	{
		$args = func_get_args();
		return Helpers::ds2w(...$args);
	}

	public static function wrap()
	{
		$args = func_get_args();
		return Helpers::wrap(...$args);
	}

	public static function random_str()
	{
		$args = func_get_args();
		return Helpers::random_str(...$args);
	}
}