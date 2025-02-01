/* global wc_add_to_cart_params */
jQuery( function( $ ) {

	if ( typeof wc_add_to_cart_params === 'undefined' ) {
		return false;
	}

	/**
	 * Add2CartHandler class.
	 */
	var Add2CartHandler = function() {
		var self = this;

		self.requests    = [];
		self.addRequest  = self.addRequest.bind( self );
		self.run         = self.run.bind( self );
		self.modal       = null;

		$( document.body )
			.on( 'click', '.add_to_cart_button', { Add2CartHandler: self }, self.onAddToCart )
			.on( 'click', '.remove_from_cart_button', { Add2CartHandler: self }, self.onRemoveFromCart )
			.on( 'added_to_cart', self.updateButton )
			.on( 'ajax_request_not_sent.adding_to_cart', self.updateButton )
			.on( 'added_to_cart_proxy removed_from_cart_proxy', { Add2CartHandler: self }, self.updateFragments )
			.on( 'submit', 'form.cart', { Add2CartHandler: self }, self.onAddToCartForm );

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
						$form.find('input[name="quantity"]').trigger({
							type:"change",
							Add2CartHandler: self,
							mode: 0,
						});
					})
					.on('hide_variation', 'form.variations_form', function(event){
						self.summ_pid = false;
						$form = $(this);
						$form.find('input[name="quantity"]').trigger({
							type:"change",
							Add2CartHandler: self,
							mode: 0,
						});
					});
			}
		}

		return this;
	};

	/**
	 * Add add to favorites event.
	 */
	Add2CartHandler.prototype.addRequest = function( request ) {
		this.requests.push( request );

		if ( 1 === this.requests.length ) {
			this.run();
		}
	};

	/**
	 * Run add to favorites events.
	 */
	Add2CartHandler.prototype.run = function() {
		var requestManager = this,
			originalCallback = requestManager.requests[0].complete;

		requestManager.requests[0].complete = function() {
			if ( typeof originalCallback === 'function' ) {
				originalCallback();
			}

			requestManager.requests.shift();

			if ( requestManager.requests.length > 0 ) {
				requestManager.run();
			}
		};

		$.ajax( this.requests[0] );
	};

	/**
	 * Handle the add to favorites event.
	 */
	Add2CartHandler.prototype.onAddToCartForm = function( e ) {

		e.preventDefault();

		var $form = $(this);

		$form.block({
			message: null,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.6
			}
		});

		var formData = new FormData(this);

		$form.find('[type="submit"]').each(function(index, el) {
		  var $el;
		  $el = $(el);
		  formData.append($el.attr('name'), $el.val());
		  return true;
		});

		var data = {};

		formData.forEach(function(value, key){
			data[key] = value;
		});

		if( ('add-to-cart' in data) && !('product_id' in data))
		{
			data['product_id'] = data['add-to-cart'];
		}
		delete data['add-to-cart'];

		$( document.body ).trigger( 'adding_to_cart', [ $form, data ] );

		e.data.Add2CartHandler.addRequest({
			type: 'POST',
			url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add2cart'),
			data: data,
			// processData: false,
			// contentType: false,
			// dataType: 'json',
			success: function(response) {
				if (!response) {
				  return;
				}

				if (response.error && response.product_url) {
				  window.location = response.product_url;
				  return;
				}

				if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
				  window.location = wc_add_to_cart_params.cart_url;
				  return;
				}

				$thisbutton = $(document.body).find('.ajax_add_to_cart[data-product_id="'+formData.get('add-to-cart')+'"]');

				$( document.body ).trigger( 'added_to_cart_proxy', [ response, $thisbutton ] );

				$form.unblock();
				return;
			}
		});

	};

	/**
	 * Handle the add to cart event.
	 */
	Add2CartHandler.prototype.onAddToCart = function( e ) {

		var $thisbutton = $( this );

		if ( $thisbutton.is( '.ajax_add_to_cart' ) ) {
			if ( ! $thisbutton.attr( 'data-product_id' ) ) {
				return true;
			}

			e.preventDefault();

			$thisbutton.removeClass( 'added' );
			$thisbutton.addClass( 'loading' );

			$thisbutton.block({
				message: null,
				overlayCSS: {
					backgroundColor: '#fff',
					opacity: 0.6
				}
			});

			// Allow 3rd parties to validate and quit early.
			if ( false === $( document.body ).triggerHandler( 'should_send_ajax_request.adding_to_cart', [ $thisbutton ] ) ) { 
				$( document.body ).trigger( 'ajax_request_not_sent.adding_to_cart', [ false, false, $thisbutton ] );
				return true;
			}

			var data = {};

			// Fetch changes that are directly added by calling $thisbutton.data( key, value )
			$.each( $thisbutton.data(), function( key, value ) {
				data[ key ] = value;
			});

			// Fetch data attributes in $thisbutton. Give preference to data-attributes because they can be directly modified by javascript
			// while `.data` are jquery specific memory stores.
			$.each( $thisbutton[0].dataset, function( key, value ) {
				data[ key ] = value;
			});

			// Trigger event.
			$( document.body ).trigger( 'adding_to_cart', [ $thisbutton, data ] );

			e.data.Add2CartHandler.addRequest({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add2cart' ),
				data: data,
				success: function( response ) {
					
					if ( ! response ) {
						return;
					}

					if ( response.error && response.product_url ) {
						window.location = response.product_url;
						return;
					}

					// Redirect to cart option
					if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) {
						window.location = wc_add_to_cart_params.cart_url;
						return;
					}

					// Trigger event so themes can refresh other areas.
					$( document.body ).trigger( 'added_to_cart_proxy', [ response, $thisbutton ] );
				},
				dataType: 'json'
			});
		}
	};

	/**
	 * Update fragments after remove from cart event in mini-cart.
	 */
	Add2CartHandler.prototype.onRemoveFromCart = function( e ) {
		var $thisbutton = $( this ),
			$row        = $thisbutton.closest( '.woocommerce-mini-cart-item' );

		e.preventDefault();

		$row.block({
			message: null,
			overlayCSS: {
				opacity: 0.6
			}
		});

		e.data.Add2CartHandler.addRequest({
			type: 'POST',
			url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_from_cart' ),
			data: {
				cart_item_key : $thisbutton.data( 'cart_item_key' ),
				include_cart_notice_fragment : true,
			},
			success: function( response ) {
				if ( ! response || ! response.fragments ) {
					window.location = $thisbutton.attr( 'href' );
					return;
				}

				$( document.body ).trigger( 'removed_from_cart_proxy', [ response, $thisbutton ] );
			},
			error: function() {
				window.location = $thisbutton.attr( 'href' );
				return;
			},
			dataType: 'json'
		});
	};

	/**
	 * Update cart page elements after add to cart events.
	 */
	Add2CartHandler.prototype.updateButton = function( e, fragments, cart_hash, $button ) {
		$button = typeof $button === 'undefined' ? false : $button;

		if ( $button ) {
			$button.removeClass( 'loading' );
			
			if ( fragments ) {
				$button.addClass( 'added' );
			}

			$( document.body ).trigger( 'wc_cart_button_updated', [ $button ] );
		}
	};

	/**
	 * Update fragments after add to cart events.
	 */
	Add2CartHandler.prototype.updateFragments = function( e, response, $thisbutton ) {

		var self = e.data['Add2CartHandler'];

		var fragments = response.fragments;
		var cart_hash = response.cart_hash;

		if ( fragments ) {

			var alerts = {}
			if('.woocommerce-notices-wrapper' in fragments)
			{
				alerts['.woocommerce-notices-wrapper'] = fragments['.woocommerce-notices-wrapper'];
			}
			if('div.woocommerce-notices-wrapper' in fragments)
			{
				alerts['div.woocommerce-notices-wrapper'] = fragments['div.woocommerce-notices-wrapper'];
			}
			delete fragments['.woocommerce-notices-wrapper'];
			delete fragments['div.woocommerce-notices-wrapper'];

			if(alerts)
			{
				$.each( alerts, function( key, value ) {
					$( key ).append( value );
					$( key ).stop( true ).css( 'opacity', '1' );
				});
				$( document.body ).trigger( 'notifications_loaded' );
			}

			$.each( fragments, function( key, value ) {
				$( key ).replaceWith( value );
				$( key ).stop( true ).css( 'opacity', '1' ).unblock();
			});

			$thisbutton.unblock();

			if('modal' in response)
			{
				self.makeModal(response.modal);
			}

			if(e.type == 'added_to_cart_proxy')
			{
				$( document.body ).trigger( 'added_to_cart', [ fragments, cart_hash, $thisbutton ] );
			}
			if(e.type == 'removed_from_cart_proxy')
			{
				$( document.body ).trigger( 'removed_from_cart', [ fragments, cart_hash, $thisbutton ] );
			}

			$( document.body ).trigger( 'wc_fragments_loaded' );
		}
	};

	Add2CartHandler.prototype.makeModal = function( content ) {
		var config, modal;
		config = {
			title: 'Товар добавлен в корзину',
			cclass: 'added-to-cart-modal',
			content: content,
			buttons: [],
			timeout: 300,
			autoopen: false,
			close_on_bg: true,
			scrollinside: true,
		};
		modal = new Modal(config);
		modal.open(self.modal?0:false)
		if(self.modal)
		{
			self.modal.close(0);
			$(document.body).off('closeModal.addToCart');
			self.modal=null;
		}
		$(document.body).on('closeModal.addToCart',function(e,p){
			$(document.body).off('closeModal.addToCart');
			self.modal=null;
		});
		self.modal=modal;
	};

	/**
	 * Init Add2CartHandler.
	 */
	new Add2CartHandler();
});
