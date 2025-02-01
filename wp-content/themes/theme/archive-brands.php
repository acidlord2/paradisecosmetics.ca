<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Theme
 */

get_header();



$brands = get_terms([
	'taxonomy' => 'pa_brand',
	'numberposts' => -1,
	'hide_empty' => false
]);

?>
<main id="primary" class="archive archive-brands">
	<div class="container">
		<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
			<?php if (function_exists('bcn_display')) {
				bcn_display();
			} ?>
		</div>
		<h1 class="page-title">
			brands
		</h1>
		<?php if (!empty($brands)) { ?>
			<div class="brands">
				<?php foreach ($brands as $brand) {
					$image = get_field('icon', $brand);
				?>
					<?php if ($image != '') { ?>
						<a href="<?= get_term_link($brand) ?>" class="brand">
							<img src="<?= $image ?>" alt="">
						</a>
					<?php } ?>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?= get_page_content(117) ?>

</main><!-- #main -->

<?php
// get_sidebar();
get_footer();
