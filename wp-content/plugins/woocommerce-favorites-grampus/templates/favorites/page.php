<?php
global $wp_query;

get_header();

$products =  WCFAVORITES()->get_products();

$totalPrices = 0;
$totalOldPrices = 0;
?>
<div class="favorites-page woocommerce">
    <div class="container">
        <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
            <?php if (function_exists('bcn_display')) {
                bcn_display();
            } ?>
        </div>
        <div class="page-title">
            <h1><?php the_title(); ?></h1>
        </div>
        <?php if ($products) { ?>
            <div class="favorites-wrapper">
                <div class="info-wrapper">
                    <div class="item-info count">
                        <div class="item-title p1">
                            products in wishlist: <?= WCFAVORITES()->count_items(); ?>
                        </div>
                    </div>

                    <form action="<?= get_permalink(get_the_ID()); ?>">
                        <button type="submit" class="clear-fav" name="clear-fav">
                            <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 2.5L2 8.5M8 8.5L2 2.5" stroke="#A0A0A0" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            clear wishlist
                        </button>
                    </form>
                </div>
                <div class="products-wrapper">
                    <?php foreach ($products as $productID) {
                        $product = wc_get_product($productID);

                        $post_object = get_post($product->get_id());

                        setup_postdata($GLOBALS['post'] = &$post_object);

                        wc_get_template_part('content', 'product');
                    } ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="not-founded">
                There are no products in favorites!
            </div>
        <?php } ?>
    </div>
</div>
<script>
    jQuery(function($) {
        $('body').on('removed_from_favorites', function() {
            location.reload();
        });
    });
</script>
<?php
get_footer();
