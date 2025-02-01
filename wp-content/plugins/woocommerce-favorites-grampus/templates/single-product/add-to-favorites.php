<?php
global $product;

$in_favorites = WCFAVORITES()->check_item($product->get_id());
$text = get_option('favorites_single_product_text','В избранные');
?>
<button type="button" data-product_id="<?=$product->get_id()?>" class="favorites single_add_to_favorites_button ajax_add_to_favorites button alt <?php if($in_favorites) { echo 'added'; } ?>"><?=$text?></button>