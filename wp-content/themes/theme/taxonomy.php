<?php

// the_post();
get_header();

$item = get_queried_object();
$taxonomy = $item->taxonomy;
$term_id = $item->term_id;
$termSlug = $item->slug;

$subCats = get_terms([
	'taxonomy' => $taxonomy,
	'parent' => $term_id,
	'hide_empty' => false,
]);
?>
    <main id="main" class="base-page category-page">
        <div class="container">
            <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
				<?php if (function_exists('bcn_display')) {
					bcn_display();
				} ?>
            </div>
            <h1 class="page-title">
				<?=$item->name; ?>
            </h1>
			<?php if ($subCats):
				$posts = null;
				?>
                <div id="subcats-holder" class="subcats__holder">
					<?php foreach ($subCats as $subCat) {
						?>
                        <a href="" class="cat__item">

                        </a>
					<?php } ?>
                </div>
			<?php endif ?>
			<?php if ($posts) {
				global $post;
				?>
                <div id="category-holder" class="category__holder">
					<?php
					/* Start the Loop */
					while (have_posts()) :
						the_post();
						?>

                        <a href="" class="cat__item">

                        </a>
					
					<?php endwhile; ?>
                </div>
				<?php get_template_part('inc/parts/pagination'); ?>
			<?php } elseif (!$subCats && !$posts) { ?>
                <h2 class="not_founded">
                    Товаров не найдено
                </h2>
			<?php } ?>
        </div>
    </main>
<?php get_footer(); ?>