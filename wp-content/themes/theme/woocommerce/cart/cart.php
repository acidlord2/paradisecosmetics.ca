<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

$cart = $cart = WC()->cart->get_cart();

do_action('woocommerce_before_cart'); ?>
<div class="container">
	<div class="cart-top">
		<div class="p1 gray">
			products in cart: <?= WC()->cart->get_cart_contents_count(); ?>
		</div>
		<a href="<?php echo esc_url(add_query_arg('empty_cart', 'yes')) ?>" class="mini-btn-transparent">
			deselect all
		</a>
	</div>
	<div class="cart-wrapper block">
		<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
			<?php do_action('woocommerce_before_cart_table'); ?>

			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
				<tbody>
					<?php do_action('woocommerce_before_cart_contents'); ?>

					<?php
					foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
						$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
						$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
						$price = $_product->get_price();
						$sale_price = $_product->get_sale_price();
						if ($_product->is_on_sale()) {
							$percentage = (($_product->get_regular_price() - $_product->get_sale_price()) / $_product->get_regular_price()) * 100;
						} else {
							$percentage = 0;
						}

						/**
						 * Filter the product name.
						 *
						 * @since 2.1.0
						 * @param string $product_name Name of the product in the cart.
						 * @param array $cart_item The product in the cart.
						 * @param string $cart_item_key Key for the product in the cart.
						 */
						$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

						if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
							$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
					?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

								<td class="product-thumbnail">
									<?php
									$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
									?>
									<?= $thumbnail ?>
								</td>

								<td class="product-center" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
									<?php if ($percentage != 0) { ?>
										<div class="sale-flash">
											sale - <?= intval($percentage) ?>%
										</div>
									<?php } ?>
									<a href="<?= $product_permalink ?>" class="h4 name">
										<?= $_product->get_name() ?>
									</a>
									<div class="btns">
										<?php
										echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove p3 gray" aria-label="%s" data-product_id="%s" data-product_sku="%s">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M19 7C18.7348 7 18.4804 7.10536 18.2929 7.29289C18.1054 7.48043 18 7.73478 18 8V19.191C17.9713 19.6967 17.744 20.1706 17.3675 20.5094C16.991 20.8482 16.4959 21.0246 15.99 21H8.01C7.5041 21.0246 7.00898 20.8482 6.63251 20.5094C6.25603 20.1706 6.02869 19.6967 6 19.191V8C6 7.73478 5.89464 7.48043 5.70711 7.29289C5.51957 7.10536 5.26522 7 5 7C4.73478 7 4.48043 7.10536 4.29289 7.29289C4.10536 7.48043 4 7.73478 4 8V19.191C4.02854 20.2272 4.46658 21.2099 5.21818 21.9238C5.96978 22.6378 6.97367 23.0247 8.01 23H15.99C17.0263 23.0247 18.0302 22.6378 18.7818 21.9238C19.5334 21.2099 19.9715 20.2272 20 19.191V8C20 7.73478 19.8946 7.48043 19.7071 7.29289C19.5196 7.10536 19.2652 7 19 7ZM20 4H16V2C16 1.73478 15.8946 1.48043 15.7071 1.29289C15.5196 1.10536 15.2652 1 15 1H9C8.73478 1 8.48043 1.10536 8.29289 1.29289C8.10536 1.48043 8 1.73478 8 2V4H4C3.73478 4 3.48043 4.10536 3.29289 4.29289C3.10536 4.48043 3 4.73478 3 5C3 5.26522 3.10536 5.51957 3.29289 5.70711C3.48043 5.89464 3.73478 6 4 6H20C20.2652 6 20.5196 5.89464 20.7071 5.70711C20.8946 5.51957 21 5.26522 21 5C21 4.73478 20.8946 4.48043 20.7071 4.29289C20.5196 4.10536 20.2652 4 20 4ZM10 4V3H14V4H10Z" fill="#A0A0A0" />
													<path d="M11 17V10C11 9.73478 10.8946 9.48043 10.7071 9.29289C10.5196 9.10536 10.2652 9 10 9C9.73478 9 9.48043 9.10536 9.29289 9.29289C9.10536 9.48043 9 9.73478 9 10V17C9 17.2652 9.10536 17.5196 9.29289 17.7071C9.48043 17.8946 9.73478 18 10 18C10.2652 18 10.5196 17.8946 10.7071 17.7071C10.8946 17.5196 11 17.2652 11 17ZM15 17V10C15 9.73478 14.8946 9.48043 14.7071 9.29289C14.5196 9.10536 14.2652 9 14 9C13.7348 9 13.4804 9.10536 13.2929 9.29289C13.1054 9.48043 13 9.73478 13 10V17C13 17.2652 13.1054 17.5196 13.2929 17.7071C13.4804 17.8946 13.7348 18 14 18C14.2652 18 14.5196 17.8946 14.7071 17.7071C14.8946 17.5196 15 17.2652 15 17Z" fill="#A0A0A0" />
													</svg> delete
												</a>',
												esc_url(wc_get_cart_remove_url($cart_item_key)),
												/* translators: %s is the product name */
												esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
												esc_attr($product_id),
												esc_attr($_product->get_sku())
											),
											$cart_item_key
										);
										?>
										<?php $in_favorites = WCFAVORITES()->check_item($_product->get_id());
										$text = get_option('favorites_category_product_text', 'В избранные');
										?>
										<button type="button" data-product_id="<?= $_product->get_id() ?>" class="favorites p3 gray add_to_favorites ajax_add_to_favorites <?php if ($in_favorites) {
																																												echo 'added';
																																											} ?>" aria-label="<?= $text ?>">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M22.0718 3.95979C19.4616 1.34709 15.2154 1.34709 12.6059 3.95979L11.9998 4.56628L11.394 3.95979C8.78455 1.34674 4.53795 1.34674 1.92847 3.95979C-0.628032 6.51944 -0.644633 10.5768 1.88997 13.3977C4.2017 15.9698 11.0196 21.5265 11.3089 21.7617C11.4974 21.9156 11.7332 21.9996 11.9765 21.9993L11.9994 21.999C12.2424 22.0103 12.4872 21.9268 12.6899 21.7617C12.9792 21.5265 19.7979 15.9698 22.1103 13.3974C24.6445 10.5768 24.6279 6.51944 22.0718 3.95979ZM20.535 11.9782C18.7326 13.983 13.7782 18.1178 11.9994 19.585C10.2207 18.1181 5.26732 13.9837 3.46527 11.9786C1.69712 10.0109 1.68052 7.20868 3.42677 5.46028C4.31861 4.56769 5.48984 4.12105 6.66107 4.12105C7.8323 4.12105 9.00354 4.56734 9.89538 5.46028L11.2277 6.79421C11.3819 6.94777 11.5809 7.04821 11.796 7.08102C11.9694 7.11865 12.1495 7.11216 12.3198 7.06212C12.4901 7.01207 12.6452 6.92009 12.7708 6.79457L14.1038 5.46028C15.8879 3.67475 18.7898 3.67511 20.5728 5.46028C22.319 7.20868 22.3024 10.0109 20.535 11.9782Z" fill="#A0A0A0" />
											</svg>
											add wishlist
										</button>
									</div>
								</td>

								<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
									<div class="price">
										<?php if ($sale_price != '') { ?>
											<div class="h3">
												$<?= number_format($sale_price * $cart_item['quantity'], 2, ',', ' ') ?>
											</div>
											<?php if ($sale_price != '') { ?>
												<div class="p4 gray">
													$<?= number_format($price * $cart_item['quantity'], 2, ',', ' ') ?>
												</div>
											<?php } ?>
										<?php } else { ?>
											<div class="h3">
												$<?= number_format($price * $cart_item['quantity'], 2, ',', ' ') ?>
											</div>
										<?php } ?>
									</div>
									<?php
									if ($_product->is_sold_individually()) {
										$min_quantity = 1;
										$max_quantity = 1;
									} else {
										$min_quantity = 0;
										$max_quantity = $_product->get_max_purchase_quantity();
									}

									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $max_quantity,
											'min_value'    => $min_quantity,
											'product_name' => $product_name,
										),
										$_product,
										false
									);

									echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
									?>
								</td>

							</tr>
					<?php
						}
					}
					?>

					<?php do_action('woocommerce_cart_contents'); ?>

					<tr>
						<td colspan="6" class="actions">

							<?php if (wc_coupons_enabled()) { ?>
								<div class="coupon">
									<label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" /> <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply coupon', 'woocommerce'); ?></button>
									<?php do_action('woocommerce_cart_coupon'); ?>
								</div>
							<?php } ?>

							<button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

							<?php do_action('woocommerce_cart_actions'); ?>

							<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
						</td>
					</tr>

					<?php do_action('woocommerce_after_cart_contents'); ?>
				</tbody>
			</table>
			<?php do_action('woocommerce_after_cart_table'); ?>
		</form>

		<?php do_action('woocommerce_before_cart_collaterals'); ?>

		<div class="cart-collaterals">
			<?php
			/**
			 * Cart collaterals hook.
			 *
			 * @hooked woocommerce_cross_sell_display
			 * @hooked woocommerce_cart_totals - 10
			 */
			do_action('woocommerce_cart_collaterals');
			?>
		</div>

		<?php do_action('woocommerce_after_cart'); ?>
	</div>
</div>