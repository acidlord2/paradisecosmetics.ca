<?php

class GSE_Social_Widget extends WP_Widget
{
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_social',
			'description'                 => 'Выводит указанную соц. сеть из настроек сайта',
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => false,
		);
		parent::__construct( 'gse_social', 'GSE: Соц. сеть', $widget_ops );
	}

	/**
	 * Outputs the content for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$social = false;
		if(array_key_exists('social_index',$instance))
		{
			$socials = @settings('socials');
			if(array_key_exists($instance['social_index'],$socials))
			{
				$social = $socials[$instance['social_index']];
			}
		}

		$payload = array(
			'social'=>$social,
			'args'=>$args,
			'instance'=>$instance,
		);

		$template_path = apply_filters('widget_template_path', '', 'widget_social', $args, $instance);
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
		$instance = wp_parse_args( (array) $instance, array( 'social_index' => '' ) );
		$social_index = $instance['social_index'];
		$socials = settings('socials');
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'social_index' ); ?>">Соц. сеть:</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'social_index' ); ?>" name="<?php echo $this->get_field_name( 'social_index' ); ?>">
				<?php foreach($socials as $index => $social) { ?>
				<option value="<?=$index?>" <?=selected($social_index,$index,false)?>>Номер <?=$index+1?> (<?=$social['name']?>)</option>
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
		$new_instance      = wp_parse_args( (array) $new_instance, array( 'social_index' => '' ) );
		$instance['social_index'] = sanitize_text_field( $new_instance['social_index'] );
		return $instance;
	}
}