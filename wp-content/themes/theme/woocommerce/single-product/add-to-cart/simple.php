<?php

/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

global $product;

if (! $product->is_purchasable()) {
	return;
}

echo wc_get_stock_html($product); // WPCS: XSS ok.

if ($product->is_in_stock()) : ?>

	<?php do_action('woocommerce_before_add_to_cart_form'); ?>

	<form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
		<input class="product-price" type="hidden" value="<?= $product->get_price() ?>">
		<?php do_action('woocommerce_before_add_to_cart_button'); ?>

		<?php
		do_action('woocommerce_before_add_to_cart_quantity');

		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
				'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
				'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);

		do_action('woocommerce_after_add_to_cart_quantity');
		?>

		<button type="submit" class="add_to_cart" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>">add to cart - <span class="add-to-cart-price"> <?= $product->get_price_html() ?></span></button>

		<?php do_action('woocommerce_after_add_to_cart_button'); ?>

		<?php $in_favorites = WCFAVORITES()->check_item($product->get_id());
		$text = get_option('favorites_category_product_text', 'В избранные');
		?>
		<button type="button" data-product_id="<?= $product->get_id() ?>" class="favorites add_to_favorites ajax_add_to_favorites <?php if ($in_favorites) {
																																		echo 'added';
																																	} ?>" aria-label="<?= $text ?>">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M22.0718 3.95979C19.4616 1.34709 15.2154 1.34709 12.6059 3.95979L11.9998 4.56628L11.394 3.95979C8.78455 1.34674 4.53795 1.34674 1.92847 3.95979C-0.628032 6.51944 -0.644633 10.5768 1.88997 13.3977C4.2017 15.9698 11.0196 21.5265 11.3089 21.7617C11.4974 21.9156 11.7332 21.9996 11.9765 21.9993L11.9994 21.999C12.2424 22.0103 12.4872 21.9268 12.6899 21.7617C12.9792 21.5265 19.7979 15.9698 22.1103 13.3974C24.6445 10.5768 24.6279 6.51944 22.0718 3.95979ZM20.535 11.9782C18.7326 13.983 13.7782 18.1178 11.9994 19.585C10.2207 18.1181 5.26732 13.9837 3.46527 11.9786C1.69712 10.0109 1.68052 7.20868 3.42677 5.46028C4.31861 4.56769 5.48984 4.12105 6.66107 4.12105C7.8323 4.12105 9.00354 4.56734 9.89538 5.46028L11.2277 6.79421C11.3819 6.94777 11.5809 7.04821 11.796 7.08102C11.9694 7.11865 12.1495 7.11216 12.3198 7.06212C12.4901 7.01207 12.6452 6.92009 12.7708 6.79457L14.1038 5.46028C15.8879 3.67475 18.7898 3.67511 20.5728 5.46028C22.319 7.20868 22.3024 10.0109 20.535 11.9782Z" fill="#A0A0A0" />
			</svg>
		</button>
	</form>


	<?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php endif; ?>