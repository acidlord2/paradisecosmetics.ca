<?php

class GSE_Phone_Widget extends WP_Widget
{
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_phone',
			'description'                 => 'Выводит указанный телефон из настроек сайта',
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => false,
		);
		parent::__construct( 'gse_phone', 'GSE: Телефон', $widget_ops );
	}

	/**
	 * Outputs the content for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$phone = false;
		if(array_key_exists('phone_index',$instance))
		{
			$phones = @settings('phones');
			if(array_key_exists($instance['phone_index'],$phones))
			{
				$phone = $phones[$instance['phone_index']];
			}
		}

		$payload = array(
			'phone'=>$phone,
			'args'=>$args,
			'instance'=>$instance,
		);

		$template_path = apply_filters('widget_template_path', '', 'widget_phone', $args, $instance);
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
		$instance = wp_parse_args( (array) $instance, array( 'phone_index' => '' ) );
		$phone_index = $instance['phone_index'];
		$phones = settings('phones');
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'phone_index' ); ?>">Телефон:</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'phone_index' ); ?>" name="<?php echo $this->get_field_name( 'phone_index' ); ?>">
				<?php foreach($phones as $index => $phone) { ?>
				<option value="<?=$index?>" <?=selected($phone_index,$index,false)?>>Номер <?=$index+1?> (<?=$phone['value']?>)</option>
				<?php } ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Handles updating settings for the current widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$new_instance      = wp_parse_args( (array) $new_instance, array( 'phone_index' => '' ) );
		$instance['phone_index'] = sanitize_text_field( $new_instance['phone_index'] );
		return $instance;
	}
}