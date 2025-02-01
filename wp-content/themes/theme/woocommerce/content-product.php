<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || ! $product->is_visible()) {
	return;
}

if ($product->is_on_sale()) {
	$percentage = (($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100;
} else {
	$percentage = 0;
}

$image_id = $product->get_image_id();
$image = wp_get_attachment_image_url($image_id, 'hd');
$price = $product->get_price_html();
$name = $product->get_name();

$review_count = $product->get_rating_count();
$rating = intval($product->get_average_rating());

$attrs = theme('display_attributes');


?>
<li <?php wc_product_class('', $product); ?>>
	<?php if ($percentage != 0) { ?>
		<div class="sale-flash">
			sale - <?= intval($percentage) ?>%
		</div>
	<?php } ?>
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
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action('woocommerce_before_shop_loop_item');
	?>
	<div class="product-top">
		<?php if ($image != '') { ?>
			<img src="<?= $image ?>" alt="">
		<?php } ?>
	</div>

	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action('woocommerce_before_shop_loop_item_title');
	?>

	<div class="product-bottom">
		<?php
		do_action('woocommerce_before_shop_loop_item');
		?>
		<div class="product-bottom__info">
			<div class="info-left">
				<?php if ($review_count > 0) { ?>
					<div class="review">
						<div class="icon">
							<svg width="120" height="20" viewBox="0 0 120 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0_498_12901)">
									<path d="M19.4694 7.15354L13.3211 6.21437L10.5652 0.344375C10.3594 -0.0939583 9.64024 -0.0939583 9.43441 0.344375L6.67941 6.21437L0.531075 7.15354C0.417556 7.17098 0.311037 7.21936 0.223214 7.29337C0.135391 7.36739 0.0696596 7.46417 0.0332373 7.57309C-0.00318507 7.68201 -0.00888996 7.79886 0.016749 7.91082C0.0423879 8.02277 0.0983792 8.12549 0.178575 8.20771L4.64524 12.786L3.58941 19.2585C3.57056 19.3743 3.58465 19.493 3.63009 19.6011C3.67552 19.7093 3.75047 19.8024 3.84634 19.87C3.94222 19.9375 4.05517 19.9767 4.17228 19.9831C4.28939 19.9895 4.40593 19.9628 4.50858 19.906L10.0002 16.871L15.4919 19.9069C15.5946 19.9636 15.7111 19.9903 15.8282 19.9839C15.9453 19.9775 16.0583 19.9383 16.1541 19.8708C16.25 19.8033 16.325 19.7101 16.3704 19.602C16.4158 19.4939 16.4299 19.3751 16.4111 19.2594L15.3552 12.7869L19.8219 8.20854C19.9023 8.12635 19.9585 8.02356 19.9843 7.91149C20.0101 7.79942 20.0044 7.68241 19.968 7.57334C19.9316 7.46427 19.8657 7.36737 19.7778 7.2933C19.6898 7.21923 19.5831 7.17087 19.4694 7.15354Z" fill="var(--primary)" />
								</g>
								<g clip-path="url(#clip1_498_12901)">
									<path d="M44.4694 7.15354L38.3211 6.21437L35.5652 0.344375C35.3594 -0.0939583 34.6402 -0.0939583 34.4344 0.344375L31.6794 6.21437L25.5311 7.15354C25.4176 7.17098 25.311 7.21936 25.2232 7.29337C25.1354 7.36739 25.0697 7.46417 25.0332 7.57309C24.9968 7.68201 24.9911 7.79886 25.0167 7.91082C25.0424 8.02277 25.0984 8.12549 25.1786 8.20771L29.6452 12.786L28.5894 19.2585C28.5706 19.3743 28.5847 19.493 28.6301 19.6011C28.6755 19.7093 28.7505 19.8024 28.8463 19.87C28.9422 19.9375 29.0552 19.9767 29.1723 19.9831C29.2894 19.9895 29.4059 19.9628 29.5086 19.906L35.0002 16.871L40.4919 19.9069C40.5946 19.9636 40.7111 19.9903 40.8282 19.9839C40.9453 19.9775 41.0583 19.9383 41.1541 19.8708C41.25 19.8033 41.325 19.7101 41.3704 19.602C41.4158 19.4939 41.4299 19.3751 41.4111 19.2594L40.3552 12.7869L44.8219 8.20854C44.9023 8.12635 44.9585 8.02356 44.9843 7.91149C45.0101 7.79942 45.0044 7.68241 44.968 7.57334C44.9316 7.46427 44.8657 7.36737 44.7778 7.2933C44.6898 7.21923 44.5831 7.17087 44.4694 7.15354Z" fill="var(--<?= $rating > 1 ? 'primary' : 'gray' ?>)" />
								</g>
								<g clip-path="url(#clip2_498_12901)">
									<path d="M69.4694 7.15354L63.3211 6.21437L60.5652 0.344375C60.3594 -0.0939583 59.6402 -0.0939583 59.4344 0.344375L56.6794 6.21437L50.5311 7.15354C50.4176 7.17098 50.311 7.21936 50.2232 7.29337C50.1354 7.36739 50.0697 7.46417 50.0332 7.57309C49.9968 7.68201 49.9911 7.79886 50.0167 7.91082C50.0424 8.02277 50.0984 8.12549 50.1786 8.20771L54.6452 12.786L53.5894 19.2585C53.5706 19.3743 53.5847 19.493 53.6301 19.6011C53.6755 19.7093 53.7505 19.8024 53.8463 19.87C53.9422 19.9375 54.0552 19.9767 54.1723 19.9831C54.2894 19.9895 54.4059 19.9628 54.5086 19.906L60.0002 16.871L65.4919 19.9069C65.5946 19.9636 65.7111 19.9903 65.8282 19.9839C65.9453 19.9775 66.0583 19.9383 66.1541 19.8708C66.25 19.8033 66.325 19.7101 66.3704 19.602C66.4158 19.4939 66.4299 19.3751 66.4111 19.2594L65.3552 12.7869L69.8219 8.20854C69.9023 8.12635 69.9585 8.02356 69.9843 7.91149C70.0101 7.79942 70.0044 7.68241 69.968 7.57334C69.9316 7.46427 69.8657 7.36737 69.7778 7.2933C69.6898 7.21923 69.5831 7.17087 69.4694 7.15354Z" fill="var(--<?= $rating > 2 ? 'primary' : 'gray' ?>)" />
								</g>
								<g clip-path="url(#clip3_498_12901)">
									<path d="M94.4694 7.15354L88.3211 6.21437L85.5652 0.344375C85.3594 -0.0939583 84.6402 -0.0939583 84.4344 0.344375L81.6794 6.21437L75.5311 7.15354C75.4176 7.17098 75.311 7.21936 75.2232 7.29337C75.1354 7.36739 75.0697 7.46417 75.0332 7.57309C74.9968 7.68201 74.9911 7.79886 75.0167 7.91082C75.0424 8.02277 75.0984 8.12549 75.1786 8.20771L79.6452 12.786L78.5894 19.2585C78.5706 19.3743 78.5847 19.493 78.6301 19.6011C78.6755 19.7093 78.7505 19.8024 78.8463 19.87C78.9422 19.9375 79.0552 19.9767 79.1723 19.9831C79.2894 19.9895 79.4059 19.9628 79.5086 19.906L85.0002 16.871L90.4919 19.9069C90.5946 19.9636 90.7111 19.9903 90.8282 19.9839C90.9453 19.9775 91.0583 19.9383 91.1541 19.8708C91.25 19.8033 91.325 19.7101 91.3704 19.602C91.4158 19.4939 91.4299 19.3751 91.4111 19.2594L90.3552 12.7869L94.8219 8.20854C94.9023 8.12635 94.9585 8.02356 94.9843 7.91149C95.0101 7.79942 95.0044 7.68241 94.968 7.57334C94.9316 7.46427 94.8657 7.36737 94.7778 7.2933C94.6898 7.21923 94.5831 7.17087 94.4694 7.15354Z" fill="var(--<?= $rating > 3 ? 'primary' : 'gray' ?>)" />
								</g>
								<g clip-path="url(#clip4_498_12901)">
									<path d="M119.469 7.15354L113.321 6.21437L110.565 0.344375C110.359 -0.0939583 109.64 -0.0939583 109.434 0.344375L106.679 6.21437L100.531 7.15354C100.418 7.17098 100.311 7.21936 100.223 7.29337C100.135 7.36739 100.07 7.46417 100.033 7.57309C99.9968 7.68201 99.9911 7.79886 100.017 7.91082C100.042 8.02277 100.098 8.12549 100.179 8.20771L104.645 12.786L103.589 19.2585C103.571 19.3743 103.585 19.493 103.63 19.6011C103.676 19.7093 103.75 19.8024 103.846 19.87C103.942 19.9375 104.055 19.9767 104.172 19.9831C104.289 19.9895 104.406 19.9628 104.509 19.906L110 16.871L115.492 19.9069C115.595 19.9636 115.711 19.9903 115.828 19.9839C115.945 19.9775 116.058 19.9383 116.154 19.8708C116.25 19.8033 116.325 19.7101 116.37 19.602C116.416 19.4939 116.43 19.3751 116.411 19.2594L115.355 12.7869L119.822 8.20854C119.902 8.12635 119.959 8.02356 119.984 7.91149C120.01 7.79942 120.004 7.68241 119.968 7.57334C119.932 7.46427 119.866 7.36737 119.778 7.2933C119.69 7.21923 119.583 7.17087 119.469 7.15354Z" fill="var(--<?= $rating > 4 ? 'primary' : 'gray' ?>)" />
								</g>
								<defs>
									<clipPath id="clip0_498_12901">
										<rect width="20" height="20" fill="white" />
									</clipPath>
									<clipPath id="clip1_498_12901">
										<rect width="20" height="20" fill="white" transform="translate(25)" />
									</clipPath>
									<clipPath id="clip2_498_12901">
										<rect width="20" height="20" fill="white" transform="translate(50)" />
									</clipPath>
									<clipPath id="clip3_498_12901">
										<rect width="20" height="20" fill="white" transform="translate(75)" />
									</clipPath>
									<clipPath id="clip4_498_12901">
										<rect width="20" height="20" fill="white" transform="translate(100)" />
									</clipPath>
								</defs>
							</svg>
						</div>
						<div class="p5 rating">
							(<?= $review_count ?> reviews)
						</div>
					</div>
				<?php } ?>
				<div class="h6 name">
					<?= $name ?>
				</div>
				<?php if (!empty($attrs)) { ?>
					<div class="attrs">
						<?php foreach ($attrs as $key => $attrId) {
							$attr = wc_get_attribute($attrId['attr']);
							$attrValue = $product->get_attribute($attr->name);
							if ($attrValue != '') { ?>
								<div class="attr">
									<div class="attr__value p5">
										<?= $key != 0 ? ' / ' : '' ?>
										<?= $attrValue ?>
									</div>
								</div>
							<?php }
							?>

						<?php }
						?>
					</div>
				<?php } ?>
			</div>
			<div class="info-right">
				<?= $price ?>
			</div>
		</div>
		<?php
		do_action('woocommerce_before_shop_loop_item_title');
		?>
		<?php
		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		do_action('woocommerce_after_shop_loop_item');
		?>
	</div>
</li>