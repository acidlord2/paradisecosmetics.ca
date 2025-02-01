<?php

namespace GSE;

class Form
{
	protected static $_instance = null;

	private $mail_error;

	public function __construct()
	{
		add_action( 'rest_api_init', array($this, 'register_form_rest') );
		add_action( 'wp_enqueue_scripts', array($this, 'frontend_styles_and_scripts') );

		add_action( 'grampus_clear_form_uploads', array($this, 'clear_old_uploads') );

		if( !wp_next_scheduled('grampus_clear_form_uploads') )
		{
			wp_schedule_event( time(), 'hourly', 'grampus_clear_form_uploads');
		}
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
		$FS_path = GSE()::plugin_path().'assets/js/form-v2.js';
		$URL_path = GSE()::plugin_uri().'assets/js/form-v2.js';
		$version = @filemtime($FS_path);
		wp_register_script('form-v2', $URL_path, array('extends'), $version);

		$FS_path = GSE()::plugin_path().'assets/js/modal.js';
		$URL_path = GSE()::plugin_uri().'assets/js/modal.js';
		$version = @filemtime($FS_path);
		wp_register_script('modal', $URL_path, array('extends'), $version);

		$FS_path = GSE()::plugin_path().'assets/css/modal.css';
		$URL_path = GSE()::plugin_uri().'assets/css/modal.css';
		$version = @filemtime($FS_path);
		wp_register_style('modal', $URL_path, array(), $version);

		$FS_path = GSE()::plugin_path().'assets/js/modal-v2.js';
		$URL_path = GSE()::plugin_uri().'assets/js/modal-v2.js';
		$version = @filemtime($FS_path);
		wp_register_script('modal-v2', $URL_path, array('form'), $version);

		$FS_path = GSE()::plugin_path().'assets/css/modal-v2.css';
		$URL_path = GSE()::plugin_uri().'assets/css/modal-v2.css';
		$version = @filemtime($FS_path);
		wp_register_style('modal-v2', $URL_path, array(), $version);

		wp_enqueue_script('form-v2');
		// wp_enqueue_script('modal');
		// wp_enqueue_style('modal');
	}

	public function clear_old_uploads()
	{
		$ct = time() - 5 * DAY_IN_SECONDS;
		if(!is_dir(GSE()::form_uploads_path()))
		{
			mkdir(GSE()::form_uploads_path(),0777,true);
		}
		$di = new \DirectoryIterator(GSE()::form_uploads_path());
		foreach($di as $fileInfo)
		{
			if($fileInfo->isFile())
			{
				if($ct > $fileInfo->getMTime())
				{
					unlink($fileInfo->getPathname());
				}
			}
		}
	}

	public function register_form_rest()
	{
		register_rest_route(
			'gse/form',
			'/send',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'validate_form'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'gse/form-v2',
			'/send',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'validate_form_v2'),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function validate_form(\WP_REST_Request $request)
	{
		if(is_object($request) and property_exists($request, 'params'))
		{
			return $this->send_form($request);
		}
	}

	public function validate_form_v2(\WP_REST_Request $request)
	{
		if(is_object($request) and property_exists($request, 'params'))
		{
			return $this->send_form_v2($request);
		}
	}

	function set_email_content_type()
	{
		return 'text/html';
	}

	function set_mail_from($from)
	{
		$sender = @settings('sender_email');
		if($sender)
		{
			return $sender;
		}
		return $from;
	}

	function set_mail_from_name($from)
	{
		$sender = @settings('sender_name');
		if($sender)
		{
			return $sender;
		}
		return $from;
	}

	function get_mail_errors($wp_error=null)
	{
		if(empty($this->mail_error) && is_wp_error($wp_error))
		{
			$this->mail_error = $wp_error->get_error_message();
		}
		return $this->mail_error;
	}

	function get_mail_content($payload=array())
	{
		extract($payload);
		ob_start();
		@include dirname(__DIR__).'/render/request.php';
		return ob_get_clean();
	}

	function get_mail_content_v2($payload=array())
	{
		extract($payload);
		ob_start();
		@include dirname(__DIR__).'/render/request-v2.php';
		return ob_get_clean();
	}

	private function send_form($request)
	{
		$data = $request->get_params();
		$files = $request->get_file_params();

		$type = $data['type'];
		unset($data['formsubmit']);
		unset($data['type']);
		unset($data['allow-info-processing']);
		unset($data['spp']);
		/* we don't need this anymore  */

		foreach($files as $input_name => $attachments)
		{
			$names       = (array) $attachments['name'];
			$tmp_names   = (array) $attachments['tmp_name'];
			$error_codes = (array) $attachments['error'];
			$sizes       = (array) $attachments['size'];

			foreach($names as $key => $name)
			{
				$filetype = wp_check_filetype( $name );
				if(!$filetype['ext'])
				{
					continue;
				}
				$fname = uniqid() . '.' .$filetype['ext'];
				$target_file = GSE()::form_uploads_path() . $fname;
				if(move_uploaded_file($tmp_names[$key], $target_file))
				{
					if(!array_key_exists($input_name, $data))
					{
						$data[$input_name] = array();
					}
					$data[$input_name][] = '<a href="'.GSE()::form_uploads_uri().$fname.'">'.$name.' <em>(срок хранения 5 дней с момента отправки)</em></a>';
				}
			}
		}

		$email = @settings('recipient_email');

		$email = apply_filters('gse_form_recipient_email',$email);

		if($email != '')
		{
			$payload = array(
				'data' => $data,
				'type' => $type,
			);
			try
			{
				add_filter( 'wp_mail_from_name', array($this, 'set_mail_from_name') );
				add_filter( 'wp_mail_from', array($this, 'set_mail_from') );
				add_action( 'wp_mail_failed', array($this, 'get_mail_errors') );

				$subject = apply_filters('gse_form_subject',"Уведомление сайта");
				$headers = "Content-type: text/html; charset=utf-8 \r\n";
				$message = $this->get_mail_content($payload);
				$sended  = @wp_mail($email, $subject, $message, $headers);

				remove_filter( 'wp_mail_from_name', array($this, 'set_mail_from_name') );
				remove_filter( 'wp_mail_from', array($this, 'wp_mail_from') );
				remove_action( 'wp_mail_failed', array($this, 'get_mail_errors') );

				if( $sended )
				{
					return array('status'=>true);
				}
				else
				{
					return array('status'=>false,'error'=>print_r($this->get_mail_errors(),true));
				}
			}
			catch (Exception $e)
			{
				return array('status'=>true,'error'=>print_r($e,true));
			}
			return array('status'=>false);
		}
		return array('status'=>false,'errors'=>'No notices email configured.');
	}

	private function send_form_v2($request)
	{
		$data = $request->get_params();
		$files = $request->get_file_params();

		unset($data['formsubmit']);
		unset($data['spp']);
		/* we don't need this anymore  */

		foreach($files as $input_name => $attachments)
		{
			$names       = (array) $attachments['name'];
			$tmp_names   = (array) $attachments['tmp_name'];
			$error_codes = (array) $attachments['error'];
			$sizes       = (array) $attachments['size'];

			foreach($names as $key => $name)
			{
				$filetype = wp_check_filetype( $name );
				if(!$filetype['ext'])
				{
					continue;
				}
				$fname = uniqid() . '.' .$filetype['ext'];
				$target_file = GSE()::form_uploads_path() . $fname;
				if(move_uploaded_file($tmp_names[$key], $target_file))
				{
					if(!array_key_exists($input_name, $data))
					{
						$data[$input_name] = array();
					}
					$data[$input_name][] = '<a href="'.GSE()::form_uploads_uri().$fname.'">'.$name.' <em>(срок хранения 5 дней с момента отправки)</em></a>';
				}
			}
		}

		$email = @settings('recipient_email');

		$email = apply_filters('gse_form_recipient_email',$email);

		if($email)
		{
			$payload = array(
				'data' => $data,
			);
			try
			{
				add_filter( 'wp_mail_from_name', array($this, 'set_mail_from_name') );
				add_filter( 'wp_mail_from', array($this, 'set_mail_from') );
				add_action( 'wp_mail_failed', array($this, 'get_mail_errors') );

				$subject = apply_filters('gse_form_subject', "Уведомление сайта", $data);
				$headers = "Content-type: text/html; charset=utf-8 \r\n";
				$message = $this->get_mail_content_v2($payload);
				$sended  = @wp_mail($email, $subject, $message, $headers);

				remove_filter( 'wp_mail_from_name', array($this, 'set_mail_from_name') );
				remove_filter( 'wp_mail_from', array($this, 'wp_mail_from') );
				remove_action( 'wp_mail_failed', array($this, 'get_mail_errors') );

				if( $sended )
				{
					do_action('gse_form_sent', $data);
					$return = apply_filters('gse_form_sent_data', [], $data);
					return array('status'=>true,'data'=>$return);
				}
				else
				{
					$errs = $this->get_mail_errors();
					do_action('gse_form_error', $data, $errs);
					return array('status'=>false,'error'=>$errs);
				}
			}
			catch (Exception $e)
			{
				do_action('gse_form_error', $data, $e);
				return array('status'=>false,'error'=>$e);
			}
		}
		do_action('gse_form_error', $data, 'No notices email configured.');
		return array('status'=>false,'errors'=>'No notices email configured.');
	}
}
return true;