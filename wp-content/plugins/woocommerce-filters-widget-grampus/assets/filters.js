jQuery(function($) {
	var ItemFilter = function(form) {
		this.form = $(form);
		this.state = this.serialize(false);
		this.auto = this.form.data('auto') == 'yes';
		this.url = this.form.attr('action');
		this.clears = {};
		this.query = false;
		this.ignore = false;
		this.ignoreQuery = false;
		this.delayTimeout = 1000;

		/* */
		this.initAuto();
		this.initReset();
		this.initSearch();
		this.initPrices();
		this.initAccordeon();
	}

	ItemFilter.prototype.serialize = function(to_string=true) {
		var self = this;

		var formData = new FormData(self.form.get(0));
		var params = new URLSearchParams(formData);

		if(to_string)
		{
			return params.toString();
		}
		else
		{
			// var keys = [];
			// for (var key of formData.keys())
			// {
			// 	keys.push(key);
			// }
			// function onlyUnique(value, index, self) {
			// 	return self.indexOf(value) === index;
			// }
			// keys = keys.filter(onlyUnique);
			// return keys;
			var data = {};
			for(var key of formData.keys())
			{
				if(!(key in data))
				{
					data[key] = formData.getAll(key);
				}
			}
			return data;
		}
	};

	ItemFilter.prototype.update = function() {
		var self = this;

		if(self.ignore)
		{
			return;
		}

		if(self.query != false)
		{
			var t = self.query;
			clearTimeout(t);
			self.query = false;
		}

		var s = self.serialize(false);

		if(s != self.state)
		{
			self.query = setTimeout(function(){
				self.query = false;
				self.submitAjax();
			}, self.delayTimeout);
		}

	};

	ItemFilter.prototype.submitAjax = function() {
		var self = this;

		if(self.ignore)
		{
			return;
		}

		var url = self.url;
		var query = self.serialize(false);

		if(query == self.state)
		{
			return;
		}

		if(query)
		{
			url += '?'+self.serialize(true);
		}

		var $content = $('.products-holder');
		$content.block({
			message: null,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.6
			}
		});
		self.form.block({
			message: null,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.6
			}
		});

		$.ajax({
			type: 'GET',
			url: url,
			dataType: 'html',
			success: function( response ) {
				if ( ! response ) {
					return;
				}

				self.state = query;

				window.history.pushState(response,document.title,url);

				var hidden = document.createElement('html');
				hidden.innerHTML = response;
				var $hidden = $(hidden);
				var page = $('.products-holder',$hidden).html();
				var title = $('head title',$hidden).html();
				document.title = title;
				$('head title').html(title);
				$content.html(page);
				window.history.pushState({}, title, url);

				var i = document.querySelector("body");
				var event = document.createEvent("HTMLEvents");
				event.initEvent("jetpack-lazy-images-load", true, true);
				event.eventName = "jetpack-lazy-images-load";
				i.dispatchEvent(event);

				self.form.unblock();
			},
		});
	};

	ItemFilter.prototype.initAuto = function() {
		var self = this;
		self.form.on('change', 'input[name]', function(e) {
			if(self.auto)
			{
				self.update();
			}
		});
	};

	ItemFilter.prototype.initReset = function() {
		var self = this;

		self.form.on('click', '[type="reset"]', function(e) {
			e.preventDefault();
			var q = self.form.find('input[type="checkbox"],input[type="radio"]');
			q.prop('checked', false);
			var $min = self.form.find('input[data-type="min"]');
			var $max = self.form.find('input[data-type="max"]');
			$min.each(function(ind,el){
				var $el = $(this);
				$el.val($el.data('min')).change();
			});
			$max.each(function(ind,el){
				var $el = $(this);
				$el.val($el.data('max')).change();
			});
			if(!self.auto)
			{
				self.form.get(0).submit();
			}
		});
	};

	ItemFilter.prototype.initSearch = function(obj=null) {
		var self = this;

		if(!obj)
		{
			var $filterable = $('.filter-block.opened .filter-block-content.filterable',self.form);
		}
		else
		{
			var $filterable = $(obj);
		}
		
		var options = {
			valueNames: ['group-label'],
			indexAsync: true,
			searchDelay: 250,
			page: 500,
		};

		var lists = {};

		$filterable.each(function(ind,list_group){
			var id = list_group.id;
			lists[id] = new List(id, options);
			lists[id].reIndex();
			self.initScroll(list_group);
		});
	};

	ItemFilter.prototype.initScroll = function(obj) {
		if(typeof SimpleBar == 'function')
		{
			obj.simplebar = new SimpleBar(obj,{autoHide:false});
		}
		else if(typeof OverlayScrollbars == 'function')
		{
			obj.overlaysb = OverlayScrollbars(obj,{scrollbars:{clickScrolling:true}});
		}
	};

	ItemFilter.prototype.destroyScroll = function(obj) {
		if(typeof SimpleBar == 'function')
		{
			if('simplebar' in obj)
			{
				obj.simplebar.unMount();
				var $t = $(obj);
				$t.html($t.find('.simplebar-content').html());
			}
		}
		else if(typeof OverlayScrollbars == 'function')
		{
			if('overlaysb' in obj)
			{
				obj.overlaysb.destroy();
			}
		}
	};

	ItemFilter.prototype.initPrices = function() {
		var self = this;

		var $prices = $('.inputs.price',self.form);
		$prices.each(function(ind,price){
			var $price = $(price);
			var mode = $price.data('mode');

			if(mode == 'slider' && typeof noUiSlider != "undefined")
			{
				self.initSlider($price);
			}
			else if(mode == 'both' && typeof noUiSlider != "undefined")
			{
				self.initSlider($price);
				self.initRanges($price);
			}
			else
			{
				self.initRanges($price);
			}
		});
	};

	ItemFilter.prototype.initRanges = function($price) {
		var self = this;

		var $ranges = $price.parent().find('[data-filter-node="ranges"] input');
		var $min = $price.find('input[data-type="min"]');
		var $max = $price.find('input[data-type="max"]');
		var min = $min.data('min');
		var max = $max.data('max');
		var ignore = false;
		$price.on('change','input', function(e){
			e.preventDefault();
			e.stopPropagation();

			var vmin = $min.val();
			var vmax = $max.val();
			var $f = $ranges.find('[data-min="'+vmin+'"][data-max="'+vmax+'"]');

			if($f.length > 0)
			{
				$f.prop('checked',true);
			}
			else
			{
				$ranges.find('input').prop('checked',false);
			}

			emin = false;
			emax = false;
			if(vmin == '')
			{
				emin = true;
				vmin = min;
			}
			if(vmax == '')
			{
				emax = true;
				vmax = max;
			}

			if(vmin == min && !emax)
			{
				$min.removeAttr('name');
			}
			else
			{
				$min.attr('name',$min.data('name'));
			}
			if(vmax == max && !emin)
			{
				$max.removeAttr('name');
			}
			else
			{
				$max.attr('name',$max.data('name'));
			}

			if(self.auto)
			{
				self.update();
			}
		});
		$ranges.on('change', function(e){
			e.preventDefault();
			e.stopPropagation();

			if(ignore) return;
			var $t = $(this);
			ignore = true;
			$ranges.not(this).prop('checked', false);
			ignore = false;
			$min.val($t.data('min')).trigger('change');
			$max.val($t.data('max')).trigger('change');
		});
	};

	ItemFilter.prototype.initSlider = function($price) {
		var self = this;
		var range = $price.parent().find('[data-filter-node="slider"]');

		var range_i = range.get(0);
		var pbox = range.closest('.filter-block-content');
		var input_min = pbox.find('input[data-type="min"]');
		var input_max = pbox.find('input[data-type="max"]');
		var min = parseInt(range.data('min'));
		var max = parseInt(range.data('max'));
		var min_v = parseInt(input_min.val());
		var max_v = parseInt(input_max.val());

		// console.log(min_v,max_v);

		if(isNaN(min_v))
		{
			min_v = min;
		}
		if(isNaN(max_v))
		{
			max_v = max;
		}

		noUiSlider.create(range_i, {
			start: [min_v, max_v],
			connect: true,
			step: 10,
			orientation: "horizontal",
			range: {
				'min': min,
				'max': max
			}
		});

		var inputs = [input_min, input_max];
		$(inputs).each(function(index, input) {
			$(input).on('change', function() {
				var v = [null, null];
				v[index] = $(this).val();
				range_i.noUiSlider.set(v);
			});
		});
		range_i.noUiSlider.on('update', function(values, index) {
			var v = parseInt(values[index]);
			inputs[index].val(v);
			if(index == 0)
			{
				if(v == min)
				{
					input_min.removeAttr('name');
				}
				else
				{
					input_min.attr('name',input_min.data('name'));
				}
			}

			if(index == 1)
			{
				if(v == max)
				{
					input_max.removeAttr('name');
				}
				else
				{
					input_max.attr('name',input_max.data('name'));
				}
			}
		});

		var handlers = range.find('.noUi-handle');
		handlers.each(function(index, handler) {
			$(handler).on('keydown', function(e) {
				var q, v;
				v = [null, null];
				q = parseInt(inputs[index].val());
				switch (e.which) {
					case 37:
						v[index] = q - 100;
						range_i.noUiSlider.set(v);
						break;
					case 39:
						v[index] = q + 100;
						range_i.noUiSlider.set(v);
						break;
				}
			});
		});
	};

	ItemFilter.prototype.initAccordeon = function() {
		var self = this;

		self.form.on('click', '.filter-block-header', function() {
			var header = $(this);
			var block = header.closest('.filter-block');
			var bl_con = block.find('.filter-block-content');
			var ftb = bl_con.hasClass('filterable');
			var lsst = bl_con.get(0);
			if (!block.hasClass('opened')) {
				if(ftb)
				{
					self.destroyScroll(lsst);
				}
				bl_con.stop().slideDown(300, function() {
					if(ftb)
					{
						self.initSearch(bl_con);
					}
					var bl_srh = bl_con.find('.local-search');
					if(bl_srh.length>0)
					{
						bl_srh.stop().slideDown(300);
					}
				});
				block.addClass('opened');
			} else {
				var bl_srh = bl_con.find('.local-search');
				if(bl_srh.length>0)
				{
					bl_srh.stop().slideUp(300, function() {
						bl_con.stop().slideUp(300);
					});
				}
				else
				{
					bl_con.stop().slideUp(300);
				}
				block.removeClass('opened');
			}
		});
	};

	$(function() {
		$('form.filters-form').each(function(){
			window.itempfilter = new ItemFilter(this);
		});
	});
});