<?php

/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if (! defined('ABSPATH')) {
	exit;
}

if ($related_products) : ?>

	<section class="related products">
		<div class="container">
			<h2 class="block-title">
				buy with this product
			</h2>
		</div>
		<div class="container">
			<?php if (count($related_products) > 4) { ?>
				<div class="related-products-button-prev button-prev">
					<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M8.21111 15.4939L16.7754 23.7964C17.056 24.0683 17.5103 24.0679 17.7904 23.795C18.0703 23.5223 18.0696 23.0804 17.7889 22.8083L9.73445 15L17.7892 7.19165C18.0698 6.91954 18.0705 6.47795 17.7907 6.20514C17.724 6.14004 17.6448 6.08839 17.5576 6.05319C17.4703 6.01798 17.3768 5.9999 17.2823 6C17.1882 5.99989 17.095 6.01782 17.008 6.05278C16.921 6.08774 16.842 6.13904 16.7754 6.20373L8.21111 14.506C8.07596 14.6367 8.00012 14.8146 8.00012 15C8.00012 15.1853 8.07618 15.363 8.21111 15.4939Z" fill="#1B1B1B" />
					</svg>
				</div>
				<div class="related-products-button-next button-next">
					<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M20.789 14.5061L12.2248 6.20355C11.9442 5.93169 11.4899 5.93214 11.2097 6.20496C10.9298 6.47773 10.9306 6.91961 11.2112 7.19168L19.2657 15L11.2109 22.8083C10.9303 23.0805 10.9296 23.5221 11.2095 23.7949C11.2761 23.86 11.3553 23.9116 11.4425 23.9468C11.5298 23.982 11.6233 24.0001 11.7178 24C11.8119 24.0001 11.9052 23.9822 11.9921 23.9472C12.0791 23.9123 12.1582 23.861 12.2247 23.7963L20.789 15.494C20.9242 15.3633 21 15.1854 21 15C21 14.8147 20.9239 14.637 20.789 14.5061Z" fill="#1B1B1B" />
					</svg>
				</div>
			<?php } ?>
			<div class="swiper related-products-swiper">
				<div class="swiper-wrapper">
					<?php foreach ($related_products as $product) { ?>
						<div class="swiper-slide">
							<?php

							$post_object = get_post($product->get_id());

							setup_postdata($GLOBALS['post'] = &$post_object);

							wc_get_template_part('content', 'product');
							?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
<?php
endif;

wp_reset_postdata();
