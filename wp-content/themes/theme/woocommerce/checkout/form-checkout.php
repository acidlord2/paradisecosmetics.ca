<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (! defined('ABSPATH')) {
	exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in()) {
	echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
	return;
}

?>
<form name="checkout" method="post" class="checkout woocommerce-checkout block" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

	<div class="content">
		<div class="content__left">
			<div class="top">
				<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
					<?php if (function_exists('bcn_display')) {
						bcn_display();
					} ?>
				</div>
				<div class="h1 page-title">
					checkout
				</div>
				<div class="custom-notice-wrapper">
					
				</div>
			</div>
			<?php if ($checkout->get_checkout_fields()) : ?>

				<?php do_action('woocommerce_checkout_before_customer_details'); ?>

				<div class="col2-set" id="customer_details">
					<div class="col-1">
						<?php do_action('woocommerce_checkout_billing'); ?>
					</div>

					<div class="col-2">
						<?php do_action('woocommerce_checkout_shipping'); ?>
					</div>

					<div class="shipping-methods-wrapper">
						<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

							<?php do_action('woocommerce_review_order_before_shipping'); ?>

							<?php wc_cart_totals_shipping_html(); ?>

							<?php do_action('woocommerce_review_order_after_shipping'); ?>

						<?php endif; ?>
					</div>

					<div class="payment-methods">
						<div class="h4 chechout-title">
							payment method
						</div>
						<div class="p4 chechout-subtitle gray">
							choose a convenient payment method
						</div>

						<?php
						do_action('custom_woocommerce_checkout_payment');
						?>
					</div>

				</div>

				<?php do_action('woocommerce_checkout_after_customer_details'); ?>

			<?php endif; ?>

			<?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
		</div>
		<div class="content__right">

			<div class="total-info">
				<h3 id="order_review_heading">
					<div id="hidden_detals" class="p1">
						hidden detals
					</div>
					<div class="mini-btn">
						<svg width="30" height="15" viewBox="0 0 30 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_30_3333)">
								<path d="M0 7.5H29" stroke="#fff" />
								<path d="M29.5 7.75L22.5 0.5" stroke="#fff" />
								<path d="M22.5 14.5L29.5 7.25" stroke="#fff" />
							</g>
							<defs>
								<clipPath id="clip0_30_3333">
									<rect width="30" height="15" fill="white" />
								</clipPath>
							</defs>
						</svg>
						<svg width="30" height="15" viewBox="0 0 30 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_30_3333)">
								<path d="M0 7.5H29" stroke="#1B1B1B" />
								<path d="M29.5 7.75L22.5 0.5" stroke="#1B1B1B" />
								<path d="M22.5 14.5L29.5 7.25" stroke="#1B1B1B" />
							</g>
							<defs>
								<clipPath id="clip0_30_3333">
									<rect width="30" height="15" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</div>
				</h3>

				<?php do_action('woocommerce_checkout_before_order_review'); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action('woocommerce_checkout_order_review'); ?>
				</div>

				<?php do_action('woocommerce_checkout_after_order_review'); ?>

			</div>

		</div>
	</div>
</form>
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>