/* global wc_add_to_cart_params */
jQuery(function ($) {

	if (typeof wc_add_to_cart_params === 'undefined') {
		return false;
	}

	var favorites_hash_key = 'favorites_hash_key';
	var favorites_fragments_key = 'favorites_fragments';

	/**
	 * AddToFavoritesHandler class.
	 */
	var AddToFavoritesHandler = function () {
		this.requests = [];
		this.addRequest = this.addRequest.bind(this);
		this.run = this.run.bind(this);

		$(document.body)
			.on('click', '.ajax_add_to_favorites:not(.added)', { addToFavoritesHandler: this }, this.onAddToFavorites)
			.on('click', '.ajax_add_to_favorites.added', { addToFavoritesHandler: this }, this.onRemoveFromFavorites)
			.on('added_to_favorites removed_from_favorites refreshed_favorites', { addToFavoritesHandler: this }, this.updateFragments);
		$(window)
			.on('pageshow', favoritesLoadFragments)
			.on('wc_fragment_refresh', favoritesRefreshFragments)
			.on('storage onstorage', favoritesReloadFragments);
		try {
			var fragments = JSON.parse(sessionStorage.getItem(favorites_fragments_key)),
				favorites_hash = sessionStorage.getItem(favorites_hash_key);
			if (favorites_hash === null || favorites_hash === undefined || favorites_hash === '') {
				throw 'No favorites created';
			}
			if (fragments && (fragments['.widget_favorites_content'] || fragments['.widget_favorites_mobile_content'])) {
				$.each(fragments, function (key, value) {
					$(key).replaceWith(value);
				});

				$(document.body).trigger('favorites_loaded');
			} else {
				throw 'No fragments';
			}
		} catch (err) {
			console.error(err);
			favoritesRefreshFragments();
		}
	};

	/**
	 * Add add to favorites event.
	 */
	AddToFavoritesHandler.prototype.addRequest = function (request) {
		this.requests.push(request);

		if (1 === this.requests.length) {
			this.run();
		}
	};

	/**
	 * Run add to favorites events.
	 */
	AddToFavoritesHandler.prototype.run = function () {
		var requestManager = this,
			originalCallback = requestManager.requests[0].complete;

		requestManager.requests[0].complete = function () {
			if (typeof originalCallback === 'function') {
				originalCallback();
			}

			requestManager.requests.shift();

			if (requestManager.requests.length > 0) {
				requestManager.run();
			}
		};

		$.ajax(this.requests[0]);
	};

	/**
	 * Handle the add to favorites event.
	 */
	AddToFavoritesHandler.prototype.onAddToFavorites = function (e) {
		var $thisbutton = $(this);

		var pid = $thisbutton.attr('data-product_id');

		if (!pid) {
			return true;
		}

		$thisbutton = $('.ajax_add_to_favorites[data-product_id="' + pid + '"]');

		e.preventDefault();

		var data = {};

		// Fetch changes that are directly added by calling $thisbutton.data( key, value )
		$.each($thisbutton.data(), function (key, value) {
			data[key] = value;
		});

		// Fetch data attributes in $thisbutton. Give preference to data-attributes because they can be directly modified by javascript
		// while `.data` are jquery specific memory stores.
		$.each($thisbutton[0].dataset, function (key, value) {
			data[key] = value;
		});

		$thisbutton.block({
			message: null,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.6
			}
		});

		// Trigger event.
		$(document.body).trigger('adding_to_favorites', [$thisbutton, data]);

		e.data.addToFavoritesHandler.addRequest({
			type: 'POST',
			url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_favorites'),
			data: data,
			dataType: 'json',
			success: function (response) {
				if (!response) {
					return;
				}

				$thisbutton.addClass('added');

				// Trigger event so themes can refresh other areas.
				$(document.body).trigger('added_to_favorites', [response.fragments, response.favorites_hash, $thisbutton]);
			},
		});
	};

	/**
	 * Update fragments after remove from favorites event in widgets.
	 */
	AddToFavoritesHandler.prototype.onRemoveFromFavorites = function (e) {
		var $thisbutton = $(this);

		var pid = $thisbutton.attr('data-product_id');

		if (!pid) {
			return true;
		}

		$thisbutton = $('.ajax_add_to_favorites[data-product_id="' + pid + '"]');

		e.preventDefault();

		var data = {};

		// Fetch changes that are directly added by calling $thisbutton.data( key, value )
		$.each($thisbutton.data(), function (key, value) {
			data[key] = value;
		});

		// Fetch data attributes in $thisbutton. Give preference to data-attributes because they can be directly modified by javascript
		// while `.data` are jquery specific memory stores.
		$.each($thisbutton[0].dataset, function (key, value) {
			data[key] = value;
		});

		$thisbutton.block({
			message: null,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.6
			}
		});

		e.data.addToFavoritesHandler.addRequest({
			type: 'POST',
			url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_from_favorites'),
			data: data,
			dataType: 'json',
			success: function (response) {
				if (!response) {
					return;
				}

				$thisbutton.removeClass('added');

				$(document.body).trigger('removed_from_favorites', [response.fragments, response.favorites_hash, $thisbutton]);
			},
		});
	};

	/**
	 * Update fragments after add to favorites events.
	 */
	AddToFavoritesHandler.prototype.updateFragments = function (e, fragments, favorites_hash, $button) {
		if ($button) {
			$button.unblock({});
		}
		if (fragments) {

			var alerts = {}
			if ('.woocommerce-notices-wrapper' in fragments) {
				alerts['.woocommerce-notices-wrapper'] = fragments['.woocommerce-notices-wrapper'];
			}
			if ('div.woocommerce-notices-wrapper' in fragments) {
				alerts['div.woocommerce-notices-wrapper'] = fragments['div.woocommerce-notices-wrapper'];
			}
			delete fragments['.woocommerce-notices-wrapper'];
			delete fragments['div.woocommerce-notices-wrapper'];

			if (alerts) {
				$.each(alerts, function (key, value) {
					$(key).append(value);
					$(key).stop(true).css('opacity', '1').unblock();
				});
				$(document.body).trigger('notifications_loaded');
			}

			$.each(fragments, function (key, value) {
				$(key).replaceWith(value);
				$(key).stop(true).css('opacity', '1').unblock();
			});

			$(document.body).trigger('favorites_loaded');

			sessionStorage.setItem(favorites_fragments_key, JSON.stringify(fragments));
			favoritesSetHash(favorites_hash);
		}
	};

	function favoritesSetHash(favorites_hash) {
		localStorage.setItem(favorites_hash_key, favorites_hash);
		sessionStorage.setItem(favorites_hash_key, favorites_hash);
	}

	/**
	 * Load fragments
	 */
	function favoritesLoadFragments(e) {
		if (e.originalEvent.persisted && ($('.widget_favorites_content').empty() || $('.widget_favorites_mobile_content').empty())) {
			favoritesRefreshFragments();
		}
	};

	/**
	 * Reload fragments
	 */
	function favoritesReloadFragments(e) {
		if (favorites_hash_key === e.originalEvent.key && localStorage.getItem(favorites_hash_key) !== sessionStorage.getItem(favorites_hash_key)) {
			favoritesRefreshFragments();
		}
	};

	/**
	 * Refresh fragments.
	 */
	function favoritesRefreshFragments() {
		$.ajax({
			url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_favorites'),
			type: 'POST',
			data: {
				time: new Date().getTime()
			},
			dataType: 'json',
			success: function (response) {
				$(document.body).trigger('refreshed_favorites', [response.fragments, response.favorites_hash]);
			},
			error: function () {
				console.error(data);
			}
		});
	};

	/**
	 * Init AddToFavoritesHandler.
	 */
	new AddToFavoritesHandler();

	$('body').on('added_to_favorites removed_from_favorites', function () {
		$.ajax({
			url: '/wp-admin/admin-ajax.php',
			data: { action: 'updatefavorites' },
			type: 'POST',
			success: function (data) {
				$('.favorite-btn .count').text(data);
			},
		});
	});

	$('body').on('added_to_favorites', function () {
		$('#modal-wishlist').text('add to wishlist!');
		$('#modal-wishlist').addClass('modal-open');
		setTimeout(function () {
			$('#modal-wishlist').removeClass('modal-open');
		}, 3000);
	});
	$('body').on('removed_from_favorites', function () {
		$('#modal-wishlist').text('remove from wishlist!');
		$('#modal-wishlist').addClass('modal-open');
		setTimeout(function () {
			$('#modal-wishlist').removeClass('modal-open');
		}, 3000);
	});
});
