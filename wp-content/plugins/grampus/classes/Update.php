<?php

namespace GSE;

class Update
{
	protected static $_instance = null;
	protected static $_version = null;
	protected static $cache_key = 'gse_update_cache_key';

	function __construct()
	{
		// self::$_version = GSE()::plugin_version();
		add_filter( 'site_transient_update_plugins', array($this, 'check_for_updates') );
		add_filter( 'transient_update_plugins', array($this, 'check_for_updates') );
		add_action( 'upgrader_process_complete', array($this, 'clear_transients'), 10, 2 );
		add_filter( 'plugin_row_meta', array($this, 'show_view_details'), 30, 2 );
		add_filter( 'plugins_api', array($this, 'plugin_info'), 20, 3 );
		add_filter( 'site_status_tests', array($this, 'site_status_tests'), 90, 1 );
		add_action( 'wp_dashboard_setup', array($this, 'site_health_disable'), 90 );
		add_filter( 'http_request_args', array($this, 'allow_localhost_for_updates'), 10, 2 );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function check_for_updates( $transient )
	{
		if( empty($transient->checked) )
		{
			return $transient;
		}

		$remote = self::_load();

		if( $remote && version_compare( GSE()::plugin_version(), $remote->version, '<' ) && version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' ) && version_compare( $remote->requires_php, PHP_VERSION, '<=' ) )
		{
			$res = (object)array(
				'slug' => 'grampus',
				'plugin' => 'grampus/grampus.php',
				'new_version' => $remote->version,
				'tested' => $remote->tested,
				'package' => $remote->download_url,
			);

			$transient->response[ $res->plugin ] = $res;
		}

		return $transient;
	}

	public static function allow_localhost_for_updates($args, $url)
	{
		if( strpos($url, 'updates.grampus-server.ru') > -1 )
		{
			$args['reject_unsafe_urls'] = false;
		}
		return $args;
	}

	private static function _load()
	{
		$remote = get_transient( self::$cache_key );

		if( false === $remote )
		{
			$remote = wp_remote_get(
				'https://updates.grampus-server.ru/plugins/grampus/',
				array(
					'timeout' => 10,
					'headers' => array(
						'Accept' => 'application/json'
					)
				)
			);

			if( is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote) || empty( wp_remote_retrieve_body($remote) ) )
			{
				return false;
			}

			set_transient(self::$cache_key, $remote, 3600);
		}

		$remote = json_decode( wp_remote_retrieve_body($remote) );

		return $remote;
	}

	public static function plugin_info($res, $action, $args)
	{
		if('plugin_information' !== $action)
		{
			return $res;
		}

		if('grampus' !== $args->slug)
		{
			return $res;
		}

		$remote = self::_load();

		if(!$remote)
		{
			return $res;
		}

		$res = new \stdClass();

		$res->name = $remote->name;
		$res->slug = $remote->slug;
		$res->author = $remote->author;
		$res->author_profile = '';
		$res->version = $remote->version;
		$res->tested = $remote->tested;
		$res->requires = $remote->requires;
		$res->requires_php = $remote->requires_php;
		$res->download_link = $remote->download_url;
		$res->trunk = $remote->download_url;
		$res->last_updated = $remote->last_updated;
		$res->sections = array(
			'description' => $remote->description,
			'installation' => $remote->installation,
			'changelog' => $remote->changelog
		);
		$res->banners = array();

		return $res;
	}

	public static function clear_transients($ignore, $payload)
	{
		if( $payload == 'deactivation' || ( is_array($payload) && 'update' === $payload['action'] && 'plugin' === $payload['type']) )
		{
			delete_transient(self::$cache_key);
		}
	}

	public static function show_view_details($plugin_meta, $plugin_slug)
	{
		if('grampus/grampus.php' === $plugin_slug)
		{
			foreach($plugin_meta as $existing_link)
			{
				if (strpos($existing_link, 'tab=plugin-information') !== false)
				{
					return $plugin_meta;
				}
			}

			$plugin_info = get_plugin_data( GSE()::plugin_fullpath() );
			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=grampus&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( sprintf( __( 'More information about %s' ), $plugin_info['Name'] ) ),
				esc_attr( $plugin_info['Name'] ),
				__( 'View details' )
			);
		}
		return $plugin_meta;
	}

	public static function site_status_tests($tests)
	{
		if( array_key_exists('direct', $tests) )
		{
			if( array_key_exists('php_version', $tests['direct']) )
			{
				unset( $tests['direct']['php_version'] );
			}
		}
		return $tests;
	}

	public static function site_health_disable()
	{
		if( defined('GS_DISABLE_HEALTH') )
		{
			remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
		}
	}
}
return true;