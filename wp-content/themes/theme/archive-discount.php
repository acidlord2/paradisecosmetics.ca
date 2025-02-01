<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Theme
 */

get_header();

$discounts = get_posts([
	'post_type' => 'discount',
	'numberposts' => -1
]);

?>

<main id="primary" class="archive archive-discount">
	<div class="container">
		<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
			<?php if (function_exists('bcn_display')) {
				bcn_display();
			} ?>
		</div>
		<h1 class="page-title">
			sale & offers
		</h1>
		<?php if (!empty($discounts)) { ?>
			<div class="discounts">
				<?php foreach ($discounts as $discount) {
					get_template_part('inc/parts/components/discount', null, $discount);
				} ?>
			</div>
		<?php } ?>
	</div>

</main><!-- #main -->

<?php
// get_sidebar();
get_footer();
