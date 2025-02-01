<?php

/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;


$totalPrice = 0;
$saleTotalPrice = 0;

$cart_contents = WC()->cart->get_cart_contents();
foreach ($cart_contents as $key => $cart_item) {
	if ($cart_item['variation_id']) {
		$product = wc_get_product($cart_item['variation_id']);
	} else {
		$product = wc_get_product($cart_item['product_id']);
	}
	$totalProductRegularPrice = $product->get_regular_price() * intval($cart_item['quantity']);
	if ($product->get_sale_price() != '') {
		$saleTotalPrice += ($product->get_regular_price() - $product->get_sale_price()) * intval($cart_item['quantity']);
	}
	$totalPrice += $totalProductRegularPrice;
}


?>
<div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">
	<?php do_action('woocommerce_before_cart_totals'); ?>

	<div class="item-info">
		<div class="p1 gray">
			products: <?= WC()->cart->get_cart_contents_count() ?>
		</div>
	</div>

	<table cellspacing="0" class="shop_table shop_table_responsive">

		<tr class="cart-subtotal info-line">
			<th class="p2">subtotal</th>
			<td class="p2" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>"><?= wc_cart_totals_subtotal_html() ?></td>
		</tr>
		<tr class="cart-discount info-line">
			<th class="p2">discount</th>
			<td class="p2" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">$<?= number_format($saleTotalPrice, 2, '.', ' ') ?></td>
		</tr>
		<tr class="cart-tax info-line">
			<th class="p2">shipping & tax</th>
			<td class="p2">calculated at checkout</td>
		</tr>

		<?php do_action('woocommerce_cart_totals_before_order_total'); ?>
		<?php
		$price = explode(' ', WC()->cart->get_total());

		?>
		<tr class="order-total">
			<th class="h6">estimated total</th>
			<td class="h3" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>"><?= wc_cart_totals_subtotal_html() ?></td>
		</tr>

		<?php do_action('woocommerce_cart_totals_after_order_total'); ?>

	</table>
	<?php do_action('woocommerce_after_cart_totals'); ?>

</div>
<div class="wc-proceed-to-checkout">
	<?php do_action('woocommerce_proceed_to_checkout'); ?>
</div>