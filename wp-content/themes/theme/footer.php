<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Company
 */

$logo = wp_get_attachment_image_url(theme('logo'), 'full');
$socials = @settings('socials');
$copy = theme('copy');

?>

<footer id="footer" class="site-footer">
	<div class="container">
		<div class="footer">
			<div class="footer__top">
				<div class="left">
					<?php if ($logo != '') { ?>
						<a href="/" class="logo">
							<img src="<?= $logo ?>" alt="">
						</a>
					<?php } ?>
					<?php if (!empty($socials)) { ?>
						<div class="socials">
							<?php foreach ($socials as $social) { ?>
								<a target="_blank" href="<?= $social['value'] ?>" class="social">
									<img src="<?= wp_get_attachment_image_url($social['icon'], 'hd'); ?>" alt="">
								</a>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="menus">
						<?php
						if (has_nav_menu('footMenu')) {
							wp_nav_menu(
								array(
									'theme_location'  => 'footMenu',
									'menu' => 'main-mnu',
									'depth' => 2,
									'menu_class' => 'footer_menu',
								)
							);
						}
						?>
						<div class="bottom-menu">
							<a target="_blank" href="/privacy-policy" class="p3 poilicy">
								privacy policy
							</a>
							<a target="_blank" href="/cookie-policy" class="p3 poilicy">
								cookie policy
							</a>
							<?php if ($copy != '') { ?>
								<div class="p3 copy">
									<?= $copy ?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="right">
					<div class="title">
						sign up for updates:
					</div>
					<?php get_form('footer') ?>
				</div>
			</div>
		</div>
	</div>
</footer>
<?php woocommerce_output_all_notices(); ?>
<?php get_template_part('inc/parts/modals'); ?>

<?php wp_footer(); ?>

</body>

</html>