<?php

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Theme
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
            <?php if (function_exists('bcn_display')) {
                bcn_display();
            } ?>
        </div>
        <h1 class="page-title">
            <?php the_title(); ?>
        </h1>
    </div>
    <?php if (is_page('privacy-policy') || is_page('cookie-policy')) { ?>
        <div class="container">
            <div class="content block seo-content">
                <?php the_content(); ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="content">
            <?php the_content(); ?>
        </div>
    <?php } ?>
</main><!-- #main -->

<?php
get_footer();
