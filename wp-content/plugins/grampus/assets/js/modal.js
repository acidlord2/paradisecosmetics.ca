jQuery(function($) {

	Modal = function (payload) {
		var self = this;
		if (payload == null) {
			payload = {};
		}
		// self.id = UUID();
		self.config = {
			title: '',
			cclass: '',
			dclass: '',
			content: false,
			buttons: [],
			setClose: [],
			timeout: 300,
			autoopen: true,
			callback: false,
			close_on_bg: true,
			scrollinside: false,
		};
		for(key in payload)
		{
			self.config[key] = payload[key];
		}
		var _buttons = [];
		var ref = self.config.buttons;
		for(var i=0, len=ref.length; i<len; i++)
		{
			var button = ref[i];
			var index = self.config.setClose.indexOf(button);
			var $el = $(button);
			if(index > -1)
			{
				self.config.setClose[index] = $el;
			}
			_buttons.push($el);
		}
		self.config.buttons = _buttons;

		/* */
		self.bg = $('<div>', {
			"class": 'modal-background hidden'
		});
		self.modal_container = $('<div>', {
			"class": (self.config.scrollinside ? 'modal-container scrollinside hidden ' : 'modal-container hidden ') + this.config.cclass
		});
		if(self.config.close_on_bg)
		{
			self.config.setClose.push(self.bg);
			self.config.setClose.push(self.modal_container);
		}
		self.modal_dialog = $('<div>', {
			"class": 'modal-dialog ' + this.config.dclass
		});
		self.modal_header = $('<div>', {
			"class": 'modal-header'
		});
		self.modal_content = $('<div>', {
			"class": 'modal-content'
		});
		self.modal_footer = $('<div>', {
			"class": 'modal-footer'
		});
		self.modal_header_title = $('<h3>', {
			"class": 'modal-header-title'
		}).html(self.config.title);
		self.modal_header_close_button = $('<button>', {
			"class": 'modal-header-close'
		}).html('<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.7688 10.0008L19.6335 2.13605C20.1222 1.64784 20.1222 0.85569 19.6335 0.367478C19.1449 -0.12115 18.3536 -0.12115 17.865 0.367478L10.0002 8.23223L2.13504 0.367478C1.64641 -0.12115 0.8551 -0.12115 0.366471 0.367478C-0.122157 0.85569 -0.122157 1.64784 0.366471 2.13605L8.23164 10.0008L0.366471 17.8655C-0.122157 18.3538 -0.122157 19.1459 0.366471 19.6341C0.610786 19.878 0.930979 20.0002 1.25076 20.0002C1.57053 20.0002 1.89073 19.878 2.13504 19.6337L10.0002 11.7689L17.865 19.6337C18.1093 19.878 18.4295 20.0002 18.7492 20.0002C19.069 20.0002 19.3892 19.878 19.6335 19.6337C20.1222 19.1455 20.1222 18.3533 19.6335 17.8651L11.7688 10.0008Z" fill="currentColor"/></svg>');
		self.config.setClose.push(self.modal_header_close_button);

		self.modal_header.append(self.modal_header_title);
		self.modal_header.append(self.modal_header_close_button);
		self.modal_content.append(self.config.content);

		var ref = self.config.buttons;
		for(var i=0, len=ref.length; i<len; i++)
		{
			var button = ref[i];
			self.modal_footer.append(button);
		}

		self.modal_dialog.append(self.modal_header);
		if(self.config.content)
		{
			self.modal_dialog.append(self.modal_content);
		}
		if(self.config.buttons.length > 0)
		{
			self.modal_dialog.append(self.modal_footer);
		}
		self.modal_container.append(self.modal_dialog);

		$('body').append(self.bg).append(self.modal_container);

		$(self.config.setClose).each(function(ind, el) {
			return el.on('click', function(ev) {
				if(ev.target == this)
				{
					self.close();
				}
				return;
			});
		});
		if(self.config.autoopen === true)
		{
			self.open();
		}
		if(self.config.callback !== false && typeof self.config.callback === 'function')
		{
			self.config.callback.call(self);
		}
		return self;
	}

	Modal.prototype.getContent = function() {
		var self = this;
		return self.modal_content;
	};

	Modal.prototype.open = function(timeout) {
		var self;
		self = this;
	    if(timeout==undefined)
	    {
	      timeout=false;
	    }
		if(timeout===false)
		{
			timeout=this.config.timeout;
		}
		var v = 'all '+timeout+'ms linear';
		var p = {
			'transition': v,
			'-o-transition': v,
			'-ms-transition': v,
			'-moz-transition': v,
			'-webkit-transition': v,
		};
		self.bg.css(p);
		self.modal_container.css(p);
		self.modal_dialog.css(p);
		$(document.body).trigger('openModal',{'modal':self});
		setTimeout(function() {
			return self.bg.removeClass('hidden');
		}, timeout>0?50:timeout);
		setTimeout(function() {
			return self.modal_container.removeClass('hidden');
		}, timeout>0?150:timeout);
		return self;
	};

	Modal.prototype.close = function(timeout) {
		var self = this;
	    if(timeout==undefined)
	    {
	      timeout=false;
	    }
		if(timeout===false)
		{
			timeout=self.config.timeout;
		}
		var v = 'all '+timeout+'ms linear';
		var p = {
			'transition': v,
			'-o-transition': v,
			'-ms-transition': v,
			'-moz-transition': v,
			'-webkit-transition': v,
		};
		self.bg.css(p);
		self.modal_container.css(p);
		$(document.body).trigger('closeModal',{'modal':self});
		self.modal_header_close_button.off('click');
		setTimeout(function() {
			self.bg.remove();
			self.bg = null;
			self.modal_container.remove();
			self.modal_container = null;
			self.modal_dialog.remove();
			self.modal_dialog = null;
			self.modal_header.remove();
			self.modal_header = null;
			if (self.config.content !== false) {
				self.modal_content.remove();
				self.modal_content = null;
			}
			if (self.config.buttons.length > 0) {
				self.modal_footer.remove();
				return self.modal_footer = null;
			}
		}, (timeout>0)?timeout+250:timeout);
		if(timeout != 0)
		{
			setTimeout(function() {
				return self.modal_container.addClass('hidden');
			}, (timeout>0)?50:timeout);
			setTimeout(function() {
				return self.bg.addClass('hidden');
			}, (timeout>0)?250:timeout);
		}
		return self;
	};

	window.Modal = Modal;
});