jQuery(function ($) {
    var WooCommerceNotices;

    WooCommerceNotices = function WooCommerce() {
        var self = this;
        this.initNotices();
    }

    WooCommerceNotices.prototype.initNotices = function () {
        var self = this;
        $(document.body).on('click', '.woocommerce-message .close-notice, .woocommerce-notice .close-notice, .woocommerce-error .close-notice, .wc-block-components-notice-banner .close-notice', function () {
            self.hideNotice($(this).parent());
        });
        $(document.body).on('mouseenter', '.woocommerce-message.delayed, .woocommerce-notice.delayed, .woocommerce-error.delayed, .wc-block-components-notice-banner.delayed', function () {
            self.removeDelay($(this));
        });
        $(document.body).on('mouseleave', '.woocommerce-message.delayed, .woocommerce-notice.delayed, .woocommerce-error.delayed, .wc-block-components-notice-banner.delayed', function () {
            self.addDelay($(this));
        });
        $(document.body).on('notifications_loaded wc_cart_emptied updated_wc_div', function () {
            self.addDelay();
        });
        $(document.body).find('.woocommerce-notices-wrapper .woocommerce-message, .woocommerce-notices-wrapper .woocommerce-error, .woocommerce-notices-wrapper .wc-block-components-notice-banner').each(function (index, el) {
            self.addDelay($(el));
        })
        // $(document.body).on('checkout_error', function (ev, data) {
        //     self.checkoutErrors(data);
        // });
    };

    // WooCommerceNotices.prototype.checkoutErrors = function (data) {
    //     var self = this;
    //     if (data) {
    //         $('.woocommerce-notices-wrapper').append(data);
    //         self.addDelay(null, true);
    //     }
    // };

    WooCommerceNotices.prototype.addDelay = function ($notices = null, extended = false) {
        var self = this;
        if (!$notices) {
            $notices = $('.woocommerce-message:not(.delayed), .woocommerce-notice:not(.delayed), .woocommerce-error:not(.delayed), .wc-block-components-notice-banner:not(.delayed)');
        }
        var _time = 2500;
        if (extended) {
            _time += 2500;
        }
        $notices.each(function (index, el) {
            var $notice = $(el);
            var t = setTimeout(function () {
                self.hideNotice($notice);
            }, _time);
            $notice.data('noticeTimer', t);
            $notice.addClass('delayed');
        });
    };

    WooCommerceNotices.prototype.removeDelay = function ($notice) {
        var self = this;
        var t = $notice.data('noticeTimer');
        if (t) {
            $notice.css({ 'opacity': '1' });
            $notice.stop(true, false);
            clearTimeout(t);
        }
    };

    WooCommerceNotices.prototype.hideNotice = function ($notice) {
        var self = this;
        self.removeDelay($notice);
        $notice.animate(
            { 'opacity': 0 },
            300,
            'linear',
            function () {
                $notice.slideUp({
                    duration: 300,
                    easing: 'linear',
                    complete: function () { this.remove() },
                });
            }
        );
    };

    $('document').ready(function () {
        return new WooCommerceNotices();
    });

});