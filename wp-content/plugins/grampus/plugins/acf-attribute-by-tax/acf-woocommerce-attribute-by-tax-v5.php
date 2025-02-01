<?php

class acf_woocommerce_attribute_by_tax extends acf_field
{

	function __construct()
	{
		$this->name = 'acf_woocommerce_attribute_by_tax';
		$this->label = 'Элементы атрибута WooCommerce';
		$this->category = 'relational';

		$this->defaults = array(
			'multiple'     => 0,
			'field_type'   => 'select',
			'ui'           => 1,
			'requirements' => '',
		);

		add_action('acf/input/admin_enqueue_scripts', array($this,'enqueue_scripts_admin'));
		
		// Register filter variations.
		acf_add_filter_variations( 'acf/fields/acf_woocommerce_attribute_by_tax/query', array('name', 'key'), 1 );
		acf_add_filter_variations( 'acf/fields/acf_woocommerce_attribute_by_tax/result', array('name', 'key'), 2 );
		
		
		// ajax
		add_action('wp_ajax_acf/fields/acf_woocommerce_attribute_by_tax/query',			array($this, 'ajax_query'));
		add_action('wp_ajax_nopriv_acf/fields/acf_woocommerce_attribute_by_tax/query',	array($this, 'ajax_query'));

		parent::__construct();
	}

	function enqueue_scripts_admin()
	{
		wp_enqueue_script( 'acf-woocommerce-attribute-by-tax', plugins_url( 'acf-input.js', __FILE__ ), array('acf','acf-input'), '1.0.0', true );
	}

	function render_field_settings($field)
	{
		$choices = array(
			'' => __('No','acf'),
		);
		if(isset($field['requirements']))
		{
			$temp = get_field_object($field['requirements']);
			$choices['requirements'] = $temp['label'];
		}
		else
		{
			$field['requirements'] = '';
		}

		// multiple
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Select multiple values?', 'acf' ),
				'instructions' => '',
				'name'         => 'multiple',
				'type'         => 'true_false',
				'ui'           => 1,
			)
		);

		acf_render_field_setting(
			$field,
			array(
				'label'		=> 'Зависит от',
				'type'		=> 'select',
				'name'		=> 'requirements',
				'class'		=> 'requirements-rule-field',
				'value'		=> $field['requirements'],
				'data-value'=> $field['requirements'],
				'choices'	=> $choices,
			)
		);
	}

	function render_field($field)
	{
		$field['choices'] = array();

		$name = $field['name'];
		$multiple = '';

		if($field['multiple'])
		{
			$multiple = 'multiple';
			$name .= '[]';

			if( is_array($field['value']) )
			{
				foreach($field['value'] as $v )
				{
					$term = get_term( intval($v) );
					if( $term && !is_wp_error($term) ) {
						$field['choices'][ $term->term_id ] = $term->name;
					}
				}
			}
		}
		else
		{
			$term = get_term( intval($field['value']) );
			if( $term && !is_wp_error($term) ) {
				$field['choices'][ $term->term_id ] = $term->name;
			}
		}

		$tv = is_array($field['value']) ? $field['value'] : array($field['value']);
		
?>
<select name="<?php echo $name; ?>" data-target="<?=$field['requirements']?>" <?=esc_attr($multiple)?>>
	<?php foreach ($field['choices'] as $key => $value) { ?>
	<option value="<?=$key?>" <?php if(in_array($key, $tv)){ echo 'selected'; } ?>><?=$value?></option>
	<?php } ?>
</select>
<?php
	}

	function ajax_query()
	{
		// validate
		if( !acf_verify_ajax() ) die();
		
		// get choices
		$response = $this->get_ajax_query( $_POST );
		
		// return
		acf_send_ajax_results($response);
			
	}

	function get_ajax_query( $options = array() ) {
		
   		// defaults
   		$options = acf_parse_args($options, array(
			'post_id'	=> 0,
			's'			=> '',
			'by_tax'	=> '',
			'field_key'	=> '',
			'paged'		=> 0
		));
		
		
		// load field
		$field = acf_get_field( $options['field_key'] );
		if( !$field ) return false;

		if(!is_numeric($options['by_tax'])) return false;
		
		// bail early if taxonomy does not exist
		if( !($taxonomy = wc_attribute_taxonomy_name_by_id($options['by_tax'])) ) return false;
		
		
		// vars
   		$results = array();
		$is_hierarchical = is_taxonomy_hierarchical( $taxonomy );
		$is_pagination = ($options['paged'] > 0);
		$is_search = false;
		$limit = 20;
		$offset = 20 * ($options['paged'] - 1);
		
		
		// args
		$args = array(
			'taxonomy'		=> $taxonomy,
			'hide_empty'	=> false
		);
		
		
		// pagination
		// - don't bother for hierarchial terms, we will need to load all terms anyway
		if( $is_pagination && !$is_hierarchical ) {
			
			$args['number'] = $limit;
			$args['offset'] = $offset;
		
		}
		
		
		// search
		if( $options['s'] !== '' ) {
			
			// strip slashes (search may be integer)
			$s = wp_unslash( strval($options['s']) );
			
			
			// update vars
			$args['search'] = $s;
			$is_search = true;
			
		}
		
		
		// filters
		$args = apply_filters('acf/fields/acf_woocommerce_attribute_by_tax/query', $args, $field, $options['post_id']);
		
		
		// get terms
		$terms = acf_get_terms( $args );
		
		
		// sort into hierachial order!
		if( $is_hierarchical ) {
			
			// update vars
			$limit = acf_maybe_get( $args, 'number', $limit );
			$offset = acf_maybe_get( $args, 'offset', $offset );
			
			
			// get parent
			$parent = acf_maybe_get( $args, 'parent', 0 );
			$parent = acf_maybe_get( $args, 'child_of', $parent );
			
			
			// this will fail if a search has taken place because parents wont exist
			if( !$is_search ) {
				
				// order terms
				$ordered_terms = _get_term_children( $parent, $terms, $taxonomy );
				
				
				// check for empty array (possible if parent did not exist within original data)
				if( !empty($ordered_terms) ) {
					
					$terms = $ordered_terms;
					
				}
			}
			
			
			// fake pagination
			if( $is_pagination ) {
				
				$terms = array_slice($terms, $offset, $limit);
				
			}
			
		}
		
		
		/// append to r
		foreach( $terms as $term ) {
		
			// add to json
			$results[] = array(
				'id'	=> $term->term_id,
				'text'	=> $this->get_term_title( $term, $field, $options['post_id'] )
			);
			
		}
		
		
		// vars
		$response = array(
			'results'	=> $results,
			'limit'		=> $limit
		);
		
		
		// return
		return $response;
			
	}

	function get_term_title( $term, $field, $post_id = 0 ) {
		$title = acf_get_term_title( $term );
		
		// Default $post_id to current post being edited.
		$post_id = $post_id ? $post_id : acf_get_form_data('post_id');
		
		/**
		 * Filters the term title.
		 *
		 * @date	1/11/2013
		 * @since	5.0.0
		 *
		 * @param	string $title The term title.
		 * @param	WP_Term $term The term object.
		 * @param	array $field The field settings.
		 * @param	(int|string) $post_id The post_id being edited.
		 */
		 return apply_filters('acf/fields/acf_woocommerce_attribute_by_tax/result', $title, $term, $field, $post_id);
	}

	function update_value( $value, $post_id, $field )
	{
		// Bail early if no value.
		if( empty($value) ) {
			return $value;
		}
		
		// Format array of values.
		// - Parse each value as string for SQL LIKE queries.
		if( is_array($value) ) {
			$value = array_map('strval', $value);
		}
		
		// return
		return $value;
	}

	function load_value( $value, $post_id, $field )
	{

		// Return an array when field is set for multiple.
		if ( $field['multiple'] ) {
			if ( acf_is_empty( $value ) ) {
				return array();
			}
			return acf_array( $value );
		}

		// Otherwise, return a single value.
		return acf_unarray( $value );
	}

	function format_value( $value, $post_id, $field )
	{
		if(!$field['multiple'] && is_array($value))
		{
			$value = array_shift($value);
		}
		return $value;
	}
}

new acf_woocommerce_attribute_by_tax();