<?php

namespace GSE;

class Settings
{
	protected static $_instance = null;

	public function __construct()
	{
		add_action( 'acf/init', array($this, 'register_acf') );
		add_action( 'admin_enqueue_scripts', array($this, 'frontend_styles_and_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_styles_and_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'frontend_styles_and_scripts') );
		add_action( 'wp_head', array($this, 'wp_head') );
		add_action( 'wp_footer', array($this, 'wp_footer') );
	}

	public static function instance()
	{
		if( is_null(self::$_instance) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function register_acf()
	{
		acf_add_options_page(
			array(
				'page_title' 		=> 'Настройки сайта',
				'menu_title'		=> 'Настройки сайта',
				'menu_slug' 		=> 'gse-settings',
				'capability'		=> 'manage_options',
				'icon_url'			=> 'dashicons-location-alt',
				'redirect'			=> false,
				'autoload'			=> true,
				'update_button'		=> 'Обновить',
				'updated_message'	=> 'Настройки сайта обновлены',
			)
		);

		$config = GSE()::plugin_path().'configs/settings.json';
		$config = file_get_contents($config);
		$config = json_decode($config,true);

		acf_add_local_field_group($config[0]);

		$this->set_map_key();
	}

	private function set_map_key()
	{
		$data = @self::get_settings('map-internal');
		if(isset($data['yandex-key']))
		{
			acf_update_setting( 'yandex_api_key', $data['yandex-key'] );
		}
	}

	public static function admin_styles_and_scripts()
	{
		$screen = get_current_screen();
		if(strpos($screen->id, "gse-settings") !== -1)
		{
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			$script = "
(function (jQuery) {
	jQuery(document).ready(function(){
		var miiw = jQuery('<div>',{'id':'map-icon-instruction'}).html('<img src=\"".GSE()::plugin_uri()."assets/images/mmarker.jpg\">').hide();
		jQuery('body').prepend(miiw);
		miiw.dialog({
			title: '',
			dialogClass: 'wp-dialog',
			autoOpen: false,
			draggable: true,
			width: 'auto',
			modal: false,
			resizable: false,
			closeOnEscape: true,
			position: {
				my: 'center',
				at: 'center',
				of: window
			},
			create: function () {
				jQuery('.ui-dialog-titlebar-close').addClass('ui-button');
			}
		});
		jQuery('a.map-icon-instruction').click(function(e) {
			e.preventDefault();
			miiw.dialog('open');
		});
	});
})(jQuery);";
		wp_add_inline_script('jquery-ui-dialog',$script,'after');
		}
	}

	public function frontend_styles_and_scripts()
	{
		if(!is_admin())
		{
			$FS_path = GSE()::plugin_path().'/assets/js/map.js';
			$URL_path = GSE()::plugin_uri().'/assets/js/map.js';
			$version = @filemtime($FS_path);
			wp_register_script('map', $URL_path, array('jquery'), $version);
		}

		$FS_path = GSE()::plugin_path().'/assets/css/map.css';
		$URL_path = GSE()::plugin_uri().'/assets/css/map.css';
		$version = @filemtime($FS_path);
		wp_register_style('map', $URL_path, array(), $version);
	}

	public static function wp_head()
	{
		echo "<script>window.privacy_link = '".self::privacy_link('',true)."';</script>";
		if($metrics = Settings::get_settings('scripts.metrics')){ echo $metrics; }
		if($head = Settings::get_settings('scripts.head')){ echo $head; }
	}

	public static function wp_footer()
	{
		if($footer = Settings::get_settings('scripts.footer')){ echo $footer; }
	}

	public static function get_settings($type,$default=array())
	{
		$setting = null;
		if(function_exists('get_field'))
		{
			$setting = get_field($type,'options');
		}
		if($setting)
		{
			return $setting;
		}
		else
		{
			return $default;
		}
	}

	public static function privacy_link($text='условия обработки персональных данных',$only_link=false,$before='',$after='')
	{
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
		if($policy_page_id)
		{
			$title = ( $text ) ? $text : get_the_title( $policy_page_id );
			$link  = get_permalink($policy_page_id);
			if($only_link)
			{
				return $link;
			}
			if($link == '')
			{
				return $title;
			}
			$link = "<a href=\"{$link}\" target=\"_blank\">{$before}{$title}{$after}</a>";
			return $link;
		}
		return $text;
	}

	public static function render_map($key=null,$payload=array())
	{
		wp_enqueue_style('map');

		if($payload)
		{
			if(is_null($key)) { $key='map'; }

			$lat = '';
			$lng = '';
			$zoom = 16;
			$script = '';
			$markers = array();

			if($payload != '')
			{
				wp_enqueue_script('map');

				$icon = false;
				$payload = json_decode($payload,true);
				$lat = $payload['center_lat'];
				$lng = $payload['center_lng'];
				$zoom = $payload['zoom'];
				$markers = $payload['marks'];

				$type = 'yandex';

				?>
				<div class="map-holder" style="min-height:200px;">
					<div id="<?=$key?>" class="map-object"></div>
				</div>
				<script type="text/javascript">
				if(typeof window['MapData'] != 'object'){window['MapData'] = {};}
				window.MapData['#<?=$key?>'] = {
					'type': '<?=$type?>',
					'zoom': '<?=$zoom?>',
					'center': '<?=$lat?>,<?=$lng?>',
					'icon': <?=json_encode($icon)?>,
					'markers': <?=json_encode($markers)?>
				};
				</script>
				<?php
			}
		}
		else
		{
			$map_type = @self::get_settings('map-type');
			$data = @self::get_settings('map-'.$map_type);
			if($map_type == 'internal')
			{
				if(is_null($key)) { $key='map'; }

				$coordinates = $data['map-coordinates'];


				$lat = '';
				$lng = '';
				$zoom = 16;
				$script = '';
				$markers = array();

				if($coordinates != '')
				{

					$icon = false;
					$_icon = $data['map-icon'];
					if(is_array($_icon) && !empty($_icon))
					{
						$icon = array(
							'url' => $_icon['url'],
							'width' => $_icon['width'],
							'height' => $_icon['height'],
							'w_offset' => $data['map-icon-w-offset'] ? intval($data['map-icon-w-offset']) : 0,
							'h_offset' => $data['map-icon-h-offset'] ? intval($data['map-icon-h-offset']) : 0,
						);
					}
					$coordinates = json_decode($coordinates,true);
					$lat = $coordinates['center_lat'];
					$lng = $coordinates['center_lng'];
					$zoom = $coordinates['zoom'];
					$markers = $coordinates['marks'];

					if(!$markers || count($markers) < 1)
					{
						return;
					}

					$type = 'yandex';
					wp_enqueue_script('map');
					wp_add_inline_script('map', 'var yandex_api_key="'.$data['yandex-key'].'";', 'before');
				}
			?>
			<div class="map-holder" style="min-height:200px;">
				<div id="<?=$key?>" class="map-object"></div>
			</div>
			<script type="text/javascript">
			if(typeof window['MapData'] != 'object'){window['MapData'] = {};}
			window.MapData['#<?=$key?>'] = {
				'type': '<?=$type?>',
				'zoom': '<?=$zoom?>',
				'center': '<?=$lat?>,<?=$lng?>',
				'icon': <?=json_encode($icon)?>,
				'markers': <?=json_encode($markers)?>
			};
			</script>
			<?php
			}
			elseif($map_type == 'iframe')
			{
				$code = array_key_exists($map_type.'-code', $data) ? $data[$map_type.'-code'] : '';
				if(!$code)
				{
					return;
				}
				wp_enqueue_script('map');
			?>
			<div class="map-holder">
				<div id="<?=$key?>" class="map-object"></div>
			</div>
			<script type="text/javascript">
			if(typeof window['MapData'] != 'object'){window['MapData'] = {};}
			window.MapData['#<?=$key?>'] = {
				'iframe': '<?=htmlentities($code)?>'
			};
			</script>
			<?php
			}
			else
			{
				$code = array_key_exists($map_type.'-code', $data) ? $data[$map_type.'-code'] : '';
				if(!$code)
				{
					return;
				}
			?>
			<div class="map-holder">
				<div id="<?=$key?>" class="map-object">
					<?php echo $code; ?>
				</div>
			</div>
			<?php
			}
		}
	}
}
return true;