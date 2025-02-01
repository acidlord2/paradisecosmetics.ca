<?php

/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined('ABSPATH') || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if (!function_exists('wc_get_gallery_image_html')) {
	return;
}

global $product;

$columns           = apply_filters('woocommerce_product_thumbnails_columns', 4);
$post_thumbnail_id = $product->get_image_id() ?: 52;
$attachment_ids = $product->get_gallery_image_ids();
array_unshift($attachment_ids, intval($post_thumbnail_id));

$plug = get_field('plug', 'option');

$attachment_ids = array_values(array_unique($attachment_ids));

wp_add_inline_script('theme-single-product', 'var sp_img_id_to_index = ' . json_encode($attachment_ids) . ';', 'before');

$gid = 'gallery-' . $product->get_id();

?>

<div class="single-product__gallery sidebar-items" id="gallery" data-title="Фотографии">
	<div class="single-product__gallery-thumbnails">
		<div class="gallery-thumbnails swiper">
			<div class="swiper-wrapper">
				<?php
				if ($attachment_ids) {
					foreach ($attachment_ids as $attachment_id) {
						$url = (wp_get_attachment_image_url($attachment_id, 'full')) ? wp_get_attachment_image_url($attachment_id, 'full') : $plug;
						$src = (wp_get_attachment_image_url($attachment_id, 'large')) ? wp_get_attachment_image_url($attachment_id, 'large') : $plug;

						$html  = '<div class="swiper-slide gallery-thumbnails__slide">';
						$html .= '<img src="' . $src . '" alt="">';
						$html .= '</div>';

						echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $attachment_id);
					}
				}
				?>
			</div>
		</div>
		<?php if (count($attachment_ids) > 1) { ?>
			<div class="product-navigation">
				<div class="single-product__gallery-btn slider-btn--prev product-navigation__button--prev">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.4939 19.789L18.7964 11.2248C19.0683 10.9442 19.0679 10.4899 18.795 10.2097C18.5223 9.92983 18.0804 9.93056 17.8083 10.2112L9.99997 18.2657L2.19165 10.2109C1.91954 9.9303 1.47795 9.92958 1.20514 10.2095C1.14004 10.2761 1.08839 10.3553 1.05319 10.4425C1.01798 10.5298 0.999905 10.6233 1 10.7178C0.999886 10.8119 1.01782 10.9052 1.05278 10.9921C1.08774 11.0791 1.13904 11.1582 1.20373 11.2247L9.50602 19.789C9.63673 19.9242 9.81462 20 9.99997 20C10.1853 20 10.363 19.9239 10.4939 19.789Z" fill="#1B1B1B" />
					</svg>
				</div>
				<div class="single-product__gallery-btn  slider-btn--next product-navigation__button--next">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9.50605 10.211L1.20355 18.7752C0.931688 19.0558 0.932145 19.5101 1.20496 19.7903C1.47773 20.0702 1.91961 20.0694 2.19168 19.7888L10 11.7343L17.8083 19.7891C18.0805 20.0697 18.5221 20.0704 18.7949 19.7905C18.86 19.7239 18.9116 19.6447 18.9468 19.5575C18.982 19.4702 19.0001 19.3767 19 19.2822C19.0001 19.1881 18.9822 19.0948 18.9472 19.0079C18.9123 18.9209 18.861 18.8418 18.7963 18.7753L10.494 10.211C10.3633 10.0758 10.1854 10 10 10C9.81469 10 9.63701 10.0761 9.50605 10.211Z" fill="#1B1B1B" />
					</svg>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="swiper single-product__gallery-swiper <?php if (count($attachment_ids) >= 1) { ?>swiped<?php } ?>">
		<div class="swiper-wrapper">
			<?php
			if ($attachment_ids) {
				foreach ($attachment_ids as $attachment_id) {
					$url = (wp_get_attachment_image_url($attachment_id, 'full')) ? wp_get_attachment_image_url($attachment_id, 'full') : $plug;
					$src = (wp_get_attachment_image_url($attachment_id, 'large')) ? wp_get_attachment_image_url($attachment_id, 'large') : $plug;

					$html  = '<div class="swiper-slide single-product__gallery-slide" data-img-id="' . $attachment_id . '">';
					$html .= '<img src="' . $src . '" data-src="' . $url . '" data-fancybox="' . $gid . '" alt="">';
					$html .= '</div>';

					echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $attachment_id);
				}
			}
			?>
		</div>
	</div>
</div>