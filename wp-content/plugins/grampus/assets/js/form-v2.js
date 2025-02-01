jQuery(function($)
{
	var FormV2;

	FormV2 = (function ()
	{
		function FormV2()
		{
			this.init();
		}

		FormV2.prototype.init = function()
		{
			var self;
			self = this;
			$(document).on('click', '.form-v2 [form-send]', function(e){
				e.preventDefault();
				e.stopPropagation();
				var callee = $(this);
				var form = callee.closest('.form-v2');
				self.maybeSendForm(callee, form);
				return;
			});
		};

		FormV2.prototype.maybeSendForm = function(callee, form)
		{
			var self;
			self = this;

			$(document).trigger({
				type:"ajaxformprevalidation",
				eventData: {
					form: form,
					button: callee,
				},
			});

			form.find('.error').removeClass('error'); // remove previous validation errors
			eform = form.eserialize(); // serialize new data

			if(eform.status !== true)
			{
				var errors = $(eform.error); // get errors as jQuery object

				setTimeout(function(){
					errors.addClass('error'); // add `error` class on input
				}, 10);
				
				if(window.TOtriggers)
				{
					clearTimeout(window.TOtriggers['fcerrors']); // clear previous `error class remover` timeout
				}
				
				setTimeout(function(){
					errors.removeClass('error'); // add new `error class remover` timeout
				}, 5000, 'fcerrors');
			}

			$(document).trigger({
				type:"ajaxformpostvalidation",
				eventData: {
					form: form,
					button: callee,
				},
			});

			if(eform.status !== true)
			{
				return;
			}

			$('.form-v2 [form-send]').prop('disabled',true);

			$(document).trigger({
				type:"ajaxformsending",
				eventData: {
					form: form,
					button: callee,
				},
			});

			eform.data.append('formsubmit', true);
			$.ajax({
				url: '/wp-json/gse/form-v2/send',
				processData: false,
				contentType: false,
				dataType: 'json',
				data: eform.data,
				method: 'POST',
				success: function(response) {
					if( response.status )
					{
						form.find('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]), textarea').val('');
						$(document).trigger({
							type:"ajaxformsent",
							eventData: {
								form: form,
								button: callee,
								response: response,
							},
						});
						$('.form-v2 [form-send]').prop('disabled',false);
					}
					else
					{
						this.error(response);
					}
				},
				error: function(response) {
					$(document).trigger({
						type:"ajaxformerror",
						eventData: {
							form: form,
							button: callee,
							response: response,
						},
					});
					$('.form-v2 [form-send]').prop('disabled',false);
				}
			});
		};

		return FormV2;

	})();

	new FormV2();
});