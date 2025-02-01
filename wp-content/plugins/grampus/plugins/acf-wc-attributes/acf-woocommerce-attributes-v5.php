<?php

class acf_woocommerce_attributes extends acf_field
{

	function __construct()
	{
		$this->name = 'woocommerce_attributes';
		$this->label = 'Атрибут WooCommerce';
		$this->category = 'relational';

		$this->defaults = array(
			'data_type'    => 'attributes',
			'field_type'   => 'select'
		);

		parent::__construct();
	}

	function render_field_settings($field){}

	function render_field($field)
	{
		call_user_func( array($this, 'render_field_attributes'), $field );
	}

	function render_field_attributes($field)
	{
		$attributes = array();
		$all = wc_get_attribute_taxonomies();
		foreach ($all as $attr)
		{
			$attributes[$attr->attribute_id] = $attr->attribute_label;
		}
		uasort($attributes,function($a,$b){ if($a > $b){ return 1;} if($a < $b){ return -1;} return 0;});

		$attributes = apply_filters('woocommerce_attributes_field',$attributes);

		if(!is_array($field['value']))
		{
			$field['value'] = array();
		}
?>
<select name="<?php echo $field['name'] ?>[]">
	<?php
	foreach($attributes as $name => $label)
	{
		$selected = in_array($name,$field['value']) ? 'selected="selected"' : '';
	?>
	<option <?php echo $selected ?> value="<?=$name?>"><?=$label?></option>
	<?php
	}
	?>
</select>
<?php
	}

	function update_value( $value, $post_id, $field )
	{
		return $value;
	}

	function format_value( $value, $post_id, $field )
	{
		if( is_array($value) ) {
			$value = $value[0];
		}
		return $value;
	}
}

new acf_woocommerce_attributes();