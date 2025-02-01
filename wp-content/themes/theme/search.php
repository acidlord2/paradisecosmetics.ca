<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Dopog
 */

get_header();
?>

	<main id="main" class="site-main search-page">
		<div class="container">
			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title">
						Результаты поиска: <span><?=get_search_query();?></span>
					</h1>
				</header><!-- .page-header -->
				<div class="search__holder">
					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post(); 

						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						?>
						<a href="<?=get_permalink();?>?search=true" class="search__item">
							<?php the_title();?>
						</a>

					<?php endwhile;

					// the_posts_navigation();
					?>
				</div>
					<?php
				else : ?>
					<h1>Тем не найдено!</h1>
			<?php
			endif;
			?>
		</div>


	</main><!-- #main -->

<?php
// get_sidebar();
get_footer();
