<?php

namespace GSE;

class Widgets
{
	protected static $_instance = null;

	public function __construct()
	{
		add_action( 'widgets_init', array($this, 'init_widgets') );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function init_widgets()
	{
		require GSE()::plugin_path().'/widgets/phone/init.php';
		require GSE()::plugin_path().'/widgets/email/init.php';
		require GSE()::plugin_path().'/widgets/address/init.php';
		require GSE()::plugin_path().'/widgets/social/init.php';

		register_widget( 'GSE_Phone_Widget' );
		register_widget( 'GSE_Email_Widget' );
		register_widget( 'GSE_Address_Widget' );
		register_widget( 'GSE_Social_Widget' );
	}
}
return true;