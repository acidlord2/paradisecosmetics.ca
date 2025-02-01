<?php

class GSE_Email_Widget extends WP_Widget
{
	
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_email',
			'description'                 => 'Выводит указанный e-mail из настроек сайта',
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => false,
		);
		parent::__construct( 'gse_email', 'GSE: E-mail', $widget_ops );
	}

	/**
	 * Outputs the content for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$email = false;
		if(array_key_exists('email_index',$instance))
		{
			$emails = @settings('emails');
			if(array_key_exists($instance['email_index'],$emails))
			{
				$email = $emails[$instance['email_index']];
			}
		}

		$payload = array(
			'email'=>$email,
			'args'=>$args,
			'instance'=>$instance,
		);

		$template_path = apply_filters('widget_template_path', '', 'widget_email', $args, $instance);
		if($template_path)
		{
			$this->_render($template_path,$payload);
		}
		else
		{
			$this->_render(__DIR__.'/render.php',$payload);
		}
	}

	private function _render($template_path,$payload)
	{
		extract($payload);
		include $template_path;
	}

	/**
	 * Outputs the settings form
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'email_index' => '' ) );
		$email_index = $instance['email_index'];
		$emails = settings('emails');
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'email_index' ); ?>">E-mail:</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'email_index' ); ?>" name="<?php echo $this->get_field_name( 'email_index' ); ?>">
				<?php foreach($emails as $index => $email) { ?>
				<option value="<?=$index?>" <?=selected($email_index,$index,false)?>>E-mail <?=$index+1?> (<?=$email['value']?>)</option>
				<?php } ?>
			</select>
		</p>
		<?php
		return '';
	}

	/**
	 * Handles updating settings for the current widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$new_instance      = wp_parse_args( (array) $new_instance, array( 'email_index' => '' ) );
		$instance['email_index'] = sanitize_text_field( $new_instance['email_index'] );
		return $instance;
	}
}