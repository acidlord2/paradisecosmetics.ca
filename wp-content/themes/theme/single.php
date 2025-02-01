<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Theme
 */

get_header();
?>

    <main id="primary" class="site-main">
        <div class="content">
            <?php the_content(); ?>
        </div>
    </main><!-- #main -->

<?php
get_footer();
