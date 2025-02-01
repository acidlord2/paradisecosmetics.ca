<?php

class GSE_Address_Widget extends WP_Widget
{
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_address',
			'description'                 => 'Выводит указанный адрес из настроек сайта',
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => false,
		);
		parent::__construct( 'gse_address', 'GSE: Адрес', $widget_ops );
	}

	/**
	 * Outputs the content for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$address = false;
		if(array_key_exists('address_index',$instance))
		{
			$addresses = @settings('addresses');
			if(array_key_exists($instance['address_index'],$addresses))
			{
				$address = $addresses[$instance['address_index']];
			}
		}

		$payload = array(
			'address'=>$address,
			'args'=>$args,
			'instance'=>$instance,
		);

		$template_path = apply_filters('widget_template_path', '', 'widget_address', $args, $instance);
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
		$instance = wp_parse_args( (array) $instance, array( 'address_index' => '' ) );
		$address_index = $instance['address_index'];
		$addresss = settings('addresses');
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'address_index' ); ?>">Адрес:</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'address_index' ); ?>" name="<?php echo $this->get_field_name( 'address_index' ); ?>">
				<?php foreach($addresss as $index => $address) { ?>
				<option value="<?=$index?>" <?=selected($address_index,$index,false)?>>Номер <?=$index+1?> (<?=$address['value']?>)</option>
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
		$new_instance      = wp_parse_args( (array) $new_instance, array( 'address_index' => '' ) );
		$instance['address_index'] = sanitize_text_field( $new_instance['address_index'] );
		return $instance;
	}
}