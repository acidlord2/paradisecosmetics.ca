<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;

$currency = get_woocommerce_currency();

?>

<div class="shop_table woocommerce-checkout-review-order-table">
	<div class="cart-products-wrapper">
		<?php
		do_action('woocommerce_review_order_before_cart_contents');

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
				$product_post = get_post($_product->get_id());
				$image_id = $_product->get_image_id();
				$image = wp_get_attachment_image_url($image_id, 'medium');
				$name = $_product->get_name();
				$quantity = $cart_item['quantity'];
				$amount_price = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
				$price = $_product->get_price();
				$link = get_permalink($product_post);
		?>
				<div class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
					<div class="cart-item">
						<div class="cart-item__image">
							<?php if ($image != '') { ?>
								<img src="<?= $image ?>" alt="">
							<?php } ?>
							<div class="quantity">
								<?= $quantity ?>
							</div>
						</div>
						<a href="<?= $link ?>" class="cart-item__center">
							<div class="h6 name">
								<?= $name ?>
							</div>
							<div class="info">
								<div class="price p5">
									$<?= number_format($price, 2, '.', ' ')  ?>
								</div>
								<div class="p5 gray quantity">
									x<?= $quantity ?>
								</div>
							</div>
						</a>
						<div class="cart-item__price">
							<div class="p3 amount-price">
								$<?= number_format($amount_price, 2, '.', ' ')  ?>
							</div>
						</div>
					</div>
				</div>
		<?php
			}
		}

		do_action('woocommerce_review_order_after_cart_contents');
		?>
	</div>
	<div class="detail-info">
		<div class="cart-subtotal line">
			<div class="p2">subtotal</div>
			<div class="p2"><?php wc_cart_totals_subtotal_html(); ?></div>
		</div>
		<?php
		$shipping_total = WC()->cart->get_shipping_total();
		if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
			<div class="shipping-cost line">
				<div class="p2">shipping</div>
				<div class="p2">
					<?php if ($shipping_total == 0) { ?>
						free
					<?php } else { ?>
						$<?php echo number_format($shipping_total, 2, '.', ' '); ?>
					<?php } ?>
				</div>
			</div>
		<?php endif; ?>

		<?php foreach (WC()->cart->get_fees() as $fee) : ?>
			<tr class="fee">
				<th><?php echo esc_html($fee->name); ?></th>
				<td><?php wc_cart_totals_fee_html($fee); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if (wc_tax_enabled()) : ?>
			<?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
				<?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited 
				?>
					<div class="tax-rate line tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
						<div class="p2">estimated taxes</div>
						<div class="p2"><?php echo wp_kses_post($tax->formatted_amount); ?></div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="tax-total line">
					<div class="p2">estimated taxes (13%)</div>
					<div class="p2"><?php wc_cart_totals_taxes_total_html(); ?></div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action('woocommerce_review_order_before_order_total'); ?>

		<div class="order-total line">
			<div class="h6">total</div>
			<div class="h3 price">
				<span class="p4 gray">
					<?= $currency ?>
				</span>
				<?php wc_cart_totals_order_total_html(); ?>
			</div>
		</div>

		<?php do_action('woocommerce_review_order_after_order_total'); ?>

		<tr>
			<td>
				<?php
				$order_button_text = 'pay now';
				echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button alt mini-btn-b' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>');
				// @codingStandardsIgnoreLine 
				?>
			</td>
		</tr>

	</div>
</div>