<?php

/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Theme
 */

get_header();

$image = theme('error_image');

?>

<main id="main" class="site-main error-page">
	<div class="container">
		<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
			<?php if (function_exists('bcn_display')) {
				bcn_display();
			} ?>
		</div>
		<h1 class="page-title">
			404
		</h1>
	</div>
	<?php if ($image != '') { ?>
		<div class="error">
			<img src="<?= $image ?>" alt="" class="bg">
			<div class="content">
				<div class="h3 title">
					error 404
				</div>
				<a href="/shop" class="subtitle p1">
					sorry the page you were looking for does not exist
				</a>
				<div class="link">
					<div class="p1">
						back to shop
					</div>
					<?php print_w_btn('/shop') ?>
				</div>
			</div>
		</div>
	<?php } ?>

</main><!-- #main -->

<?php
get_footer();
