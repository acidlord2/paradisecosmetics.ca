/* global wc_add_to_cart_params */
jQuery( function( $ ) {

	/**
	 * QtyControl class.
	 */
	var QtyControl = function() {
		var self = this;

		self.timeout     = false;
		self.summ_format = false;
		self.summ_pid    = false;

		$( document.body )
			.on( 'click', '.qty-wrapper [qty-increase]', { QtyControl: self, mode: 1 }, self.modifyCount)
			.on( 'click', '.qty-wrapper [qty-decrease]', { QtyControl: self, mode: -1 }, self.modifyCount)
			.on( 'change', '.qty-wrapper input.qty', { QtyControl: self, mode: 0 }, self.modifyCount)
			.on( 'click', '.qty-wrapper input.qty', function(e){e.stopPropagation();});

		if(typeof wNumb == 'function' && typeof qty_summ_args != 'undefined')
		{
			self.summ_format = wNumb({
				mark: qty_summ_args.format.mark,
				thousand: qty_summ_args.format.thousand,
				decimals: qty_summ_args.format.decimals,
				suffix: qty_summ_args.format.suffix,
				prefix: qty_summ_args.format.prefix,
			});
			if(qty_summ_args.mode == 'variable')
			{
				$( document.body )
					.on('reload_product_variations show_variation', 'form.variations_form', function(event,variation){
						self.summ_pid = variation['variation_id'];
						$form = $(this);
						$form.find('input.qty').trigger({
							type:"change",
							QtyControl: self,
							mode: 0,
						});
					})
					.on('hide_variation', 'form.variations_form', function(event){
						self.summ_pid = false;
						$form = $(this);
						$form.find('input.qty').trigger({
							type:"change",
							QtyControl: self,
							mode: 0,
						});
					});
			}
		}

		return this;
	};

	QtyControl.prototype.modifyCount = function( e ) {
		e.preventDefault();
		var mode = e.data['mode'];
		var self = e.data['QtyControl'];
		var $element = $(e.currentTarget).closest('.qty-wrapper');

		var $input = $element.find('input.qty');
		var $inc = $element.find('[qty-increase]');
		var $dec = $element.find('[qty-decrease]');

		var step = parseFloat($input.attr('step'));
		var min = parseFloat($input.attr('min'));
		var max = parseFloat($input.attr('max'));

		let product = $(this).closest('.product.type-product')
		let priceSpan = product.find('.add-to-cart-price')
        let price = parseFloat(product.find('input.product-price').val()).toFixed(2)
		
		let quantityLink = product.find('a.ajax_add_to_cart')		
		

		if(isNaN(step))
		{
			step = 1;
		}

		if(isNaN(max))
		{
			max = Number.MAX_SAFE_INTEGER;
		}

		if(isNaN(min))
		{
			min = 1;
		}

		var v = parseFloat($input.val());

		switch(mode)
		{
			case 1:
				v+=step;
				break;
			case 0:
				if(isNaN(v))
				{
					v = min;
				}
				break;
			case -1:
				v-=step;
				break;
		}

		var can_inc = true;
		var can_dec = true;

		if(v >= max)
		{
			v = max;
			can_inc = false;
		}
		if(v <= min)
		{
			v = min;
			can_dec = false;
		}

		if(can_inc) { $inc.prop('disabled',false); } else { $inc.prop('disabled',true);}
		if(can_dec) { $dec.prop('disabled',false); } else { $dec.prop('disabled',true);}

		v = parseFloat(v.toFixed(3));

		$input.val(v);

		if(priceSpan.lenght != 0){			
			let priceText = '$' + parseFloat($input.val() * price).toFixed(2)
			priceSpan.text(priceText.replace('.', ','))
		}

		if(_QtyControl_isCart)
		{
			if(mode != 0)
			{
				if(self.timeout)
				{
					var t = self.timeout;
					clearTimeout(t);
					self.timeout=null;
				}
				self.timeout = setTimeout(function(){
					$('[name=\"update_cart\"]').removeAttr('disabled').trigger('click');
				},1000);
			}
			else
			{
				$('[name=\"update_cart\"]').removeAttr('disabled').trigger('click');
			}
		}

		if(self.summ_format)
		{
			var $s = $('.qty-summ strong');
			var s = 0;
			if(qty_summ_args.mode == 'simple')
			{
				s = v * qty_summ_args.prices[0];
			}
			else
			{
				if(self.summ_pid)
				{
					s = v * qty_summ_args.prices[self.summ_pid];
				}
			}
			s = parseFloat(s.toFixed(2));
			if(qty_summ_args.format.trim_zeros)
			{
				s = parseFloat( (''+s).replace('.00','') );
			}
			$s.html( self.summ_format.to(s) );
		}

		quantityLink.attr('data-quantity', v);
		
	}

	/**
	 * Init QtyControl.
	 */
	new QtyControl();
});
