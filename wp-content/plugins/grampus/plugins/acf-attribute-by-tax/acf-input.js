(function($, undefined){

	/**
	*  requirementsManager
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var requirementsManager = new acf.Model({
		
		id: 'requirementsManager',
		
		priority: 30, // run actions later
		
		actions: {
			'new_field': 'onNewField',
			// 'append_field': 'onNewField',
			// 'delete_field_object': 'onRemoveField',
		},

		events: {
			'focus .requirements-rule-field': 'onFocusField',
		},
		
		onNewField: function( field ){
			if( field.get('name') == 'requirements' && field.get('setting') == 'acf_woocommerce_attribute_by_tax' ) {
				field.renderRequirementsOptions();
			}
		},

		onFocusField: function( e, $el ){
			var $parent = $el.closest('.acf-field-object');
			var fields = acf.getFields({'key':'requirements','parent':$parent});
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i];
				if( field.get('name') == 'requirements' && field.get('setting') == 'acf_woocommerce_attribute_by_tax' ) {
					field.renderRequirementsOptions();
				}
			}
		},

		onRemoveField: function(el){
			var fields = acf.getFields({'key':'requirements'});
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i];
				if( field.get('name') == 'requirements' && field.get('setting') == 'acf_woocommerce_attribute_by_tax' ) {
					field.renderRequirementsOptions();
					field.triggerRequirementsOptions();
				}
			}
		}
	});
	
	acf.Field.prototype.triggerRequirementsOptions = function(){
		var $select = this.$input('field');
		$select.trigger('change').change();
	};

	acf.Field.prototype.renderRequirementsOptions = function(){

		var choices = [];
		var validFieldTypes = [];
		var cid = this.cid;
		var $select = this.$input('field');
		var _val = $select.val();
		
		// loop
		acf.getFieldObjects({}).map(function( fieldObject ){
			
			console.log(fieldObject);
			// vars
			var choice = {
				id:		fieldObject.getKey(),
				text:	fieldObject.getLabel()
			};
			
			// bail early if is self
			if( fieldObject.cid === cid  ) {
				choice.text += acf.__('(this field)');
				choice.disabled = true;
			}

			if( fieldObject.data.type != 'woocommerce_attributes')
			{
				choice.text += acf.__('(недоступно)');
				choice.disabled = true;
			}

			if( choice.id == _val ) {
				choice.selected = true;
			}
			
			// calulate indents
			var indents = fieldObject.getParents().length;
			choice.text = '- '.repeat(indents) + choice.text;
			
			// append
			choices.push(choice);
		});
		
		// allow for scenario where only one field exists
		if( !choices.length ) {
			choices.push({
				id: '',
				text: acf.__('No'),
			});
			this.disable();
		}
		else
		{
			this.enable();
		}
		
		// render
		acf.renderSelect( $select, choices );
	};
	
})(jQuery);
(function($, undefined){
	
	var Field = acf.Field.extend({
		
		type: 'acf_woocommerce_attribute_by_tax',
		
		select2: false,
		
		wait: 'load',
		
		events: {
			'removeField': 'onRemove',
			'duplicateField': 'onDuplicate'
		},
		
		$input: function(){
			return this.$('select');
		},
		
		initialize: function(){
			// vars
			var self = this;
			var $select = this.$input();
			var ajaxAction = 'acf/fields/' + this.get('type') + '/query';
			var multi = $select.attr('multiple') ? true : false;
			
			// inherit data
			this.inherit( $select );

			var target = $select.data('target');
			this.$target = $select.closest('.acf-row').find('[data-key="'+target+'"] select');
			if(this.$target.length == 0)
			{
				this.$target = $select.closest('.acf-fields').find('[data-key="'+target+'"] select');
			}

			if(this.$target.length > 0)
			{
				this.$target.on('change', function(){
					$select.val(null).trigger('change');
				});
			}

			// select2
			this.select2 = acf.newSelect2($select, {
				field: this,
				ajax: true,
				multiple: multi,
				placeholder: '',
				allowNull: false,
				ajaxAction: ajaxAction,
			});
		},
		
		onRemove: function(){
			if( this.select2 ) {
				this.select2.destroy();
			}
			if(this.$target.length > 0)
			{
				this.$target.off('change');

			}
		},
		
		onDuplicate: function( e, $el, $duplicate ){
			if( this.select2 ) {
				$duplicate.find('.select2-container').remove();
				$duplicate.find('select').removeClass('select2-hidden-accessible');
			}
		}
	});
	
	acf.registerFieldType( Field );

	// populate ajax_data (allowing custom attribute to already exist)
	acf.addFilter('select2_ajax_data', function(ajaxData, data, $el, field, obj){
		if(field)
		{
			if(field.data.type == 'acf_woocommerce_attribute_by_tax')
			{
				if(field.$target)
				{
					ajaxData['by_tax'] = field.$target.val();
				}
			}
		}
		return ajaxData;
	});
})(jQuery);