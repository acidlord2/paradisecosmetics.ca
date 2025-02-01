<?php

/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined('ABSPATH') || exit;
?>
<div class="woocommerce-shipping-fields">

	<div class="billing-fields-wrapper">
		<div class="h4 chechout-title">
			billing address
		</div>
		<div class="p4 chechout-subtitle gray">
			enter your address and details for delivery of goods
		</div>

		<div class="billing-fields chechout-inputs">
			<?php
			$billing = $checkout->get_checkout_fields('billing');

			if (isset($billing['billing_address_1'])) {
				$key = 'billing_address_1';
				woocommerce_form_field($key, $billing[$key], $checkout->get_value($key));
			}
			if (isset($billing['billing_address_2'])) {
				$key = 'billing_address_2';
				woocommerce_form_field($key, $billing[$key], $checkout->get_value($key));
			}
			if (isset($billing['billing_city'])) {
				$key = 'billing_city';
				woocommerce_form_field($key, $billing[$key], $checkout->get_value($key));
			}
			if (isset($billing['billing_country'])) {
				$key = 'billing_country';
				woocommerce_form_field($key, $billing[$key], $checkout->get_value($key));
			}
			if (isset($billing['billing_state'])) {
				$key = 'billing_state';
				woocommerce_form_field($key, $billing[$key], $checkout->get_value($key));
			}

			if (isset($billing['billing_postcode'])) {
				$key = 'billing_postcode';
				woocommerce_form_field($key, $billing[$key], $checkout->get_value($key));
			}

			?>
		</div>

		<div id="ship-to-different-address" class="p4 gray">
			<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked(apply_filters('woocommerce_ship_to_different_address_checked', 'shipping' === get_option('woocommerce_ship_to_destination') ? 1 : 0), 1); ?> type="checkbox" name="ship_to_different_address" value="1" />
			<label for="ship-to-different-address-checkbox" class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<span>use this address for shipping</span>
			</label>
		</div>
	</div>

	<?php if (true === WC()->cart->needs_shipping_address()) : ?>
		<div class="shipping-fields-wrapper">
			<div class="h4 chechout-title">
				shipping address
			</div>
			<div class="p4 chechout-subtitle gray">
				enter your address and details for delivery of goods
			</div>

			<div class="shipping_address">

				<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

				<div class="woocommerce-shipping-fields__field-wrapper chechout-inputs">
					<?php
					$fields = $checkout->get_checkout_fields('shipping');

					foreach ($fields as $key => $field) {
						woocommerce_form_field($key, $field, $checkout->get_value($key));
					}
					?>
				</div>

				<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

			</div>

		</div>
	<?php endif; ?>
</div>
