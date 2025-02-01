<?php

add_action('init', 'wc_clear_favorite_url'); // Чистка избранного
add_action('widgets_init', 'register_my_widgets'); // Регистрация сайдбаров
add_filter('woocommerce_enqueue_styles', '__return_empty_array'); // Убираем стандартные стили Woocomerce
add_action('wp_ajax_updatefavorites', 'updateFavorites'); // Обновление избранного
add_action('wp_ajax_nopriv_updatefavorites', 'updateFavorites'); // Обновление избранного

add_filter('woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count'); //Обновление счетчика товаров в козине
remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10); // Удаляем уведомления на странице магазина
remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10); // Удаляем уведомления на странице Товра
remove_action('woocommerce_before_cart', 'woocommerce_output_all_notices', 10); // Удаляем уведомления на странице оформления заказа
remove_action('woocommerce_cart_is_empty', 'woocommerce_output_all_notices', 5); // Удаляем уведомления в пустой корзине
remove_action('woocommerce_cart_is_empty', 'wc_empty_cart_message', 10); // Удаляем уведомления в пустой корзине

// SHOP PAGE

remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10); // Удаляем сайдбар
remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10); // Удаляем отображения контента на странице магазина \ категории
remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10); // Удаляем отображения контента на странице магазина \ категории

// CATEGORY PAGE

remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

add_action('woocommerce_before_shop_loop', 'custom_category_ordering_wrapper_open', 25);
add_action('woocommerce_before_shop_loop', 'custom_category_ordering_wrapper_close', 50);
add_action('woocommerce_before_shop_loop', 'custom_category_filter_button', 30); // Выводим кнопку открывающую фильтр

add_action('woocommerce_before_main_content', 'custom_category_banner_block', 5); // Выводим кастомный баннер категории
add_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 35);
// CATEGORY CARD

remove_action('woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10); //  Удаляем дефолтное отображение заголовка
remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10); // Удаляем дефолтное отображение картинки

add_action('woocommerce_shop_loop_subcategory_title', 'custom_woocommerce_template_loop_category_title', 10); // Добавляем кастомное отображение заголовка
add_action('woocommerce_before_subcategory_title', 'custom_woocommerce_subcategory_thumbnail', 10); // Добавляем кастомное отображение картинки

// PRODUCT CARD

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10); // Удаляем отображение надписи акция
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10); // Удаляем отображение изображения
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10); // Удаляем отображение заголовка
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5); // Удаляем отображение оценки
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5); // Переносим закрытие ссылки

add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15); // Переносим закрытие ссылки

// SINGLE PRODUCT

remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10); // Удаляем отображение скидки
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10); // Удаляем отображение табов
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15); //
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20); // Удаляем отображение похожих товаров

add_action('woocommerce_custom_add_to_cart', 'woocommerce_template_single_add_to_cart', 10); // Отображение Добавления в корзину
add_action('woocommerce_sidebar', 'custom_product_block', 10); // Выводим блок с картинками для товара
add_action('custom_woocommerce_product_blocks', 'woocommerce_output_related_products', 10); // Переносим отображение похожих товаров
add_action('custom_woocommerce_product_blocks', 'custom_woocommerce_product_reviews', 20); // Выводим отображение отзывов

add_filter('woocommerce_output_related_products_args', 'custom_related_args', 10); // Изменение количества постов в рекомендациях
add_action('comment_form_before', 'add_enctype_to_comment_form'); // Добавляем атрибут enctype в форму комментариев
add_filter('comment_form_field_comment', 'render_file_fields', 99, 1); // Добавляем поле файла для отызва
add_action('comment_post', 'handle_custom_review_fields', 10, 3); // Обработка формы создания отзыва
add_action('comment_form_top', 'render_name_field'); // Добавляем поле Имя

//********* CART PAGE ********/

add_action('init', 'wc_clear_cart_url'); // Чистка Корзины

//********* CHECKOUT PAGE ********/

remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20); // Удаляем варианты оплаты из итогов
remove_action('woocommerce_before_checkout_form', 'woocommerce_notices', 10); // Удаляем отображение уведомлений

add_action('custom_woocommerce_checkout_payment', 'woocommerce_checkout_payment', 10); // Переносим отображение вариантов оплаты в левую часть



function render_name_field()
{
    $emails = @settings('emails');
    if (!empty($emails)) {
        $email = $emails[0]['name'];
    }
    ob_start();
    ?>

    <div class="custom-fields">
        <div class="form-floating">
            <input type="text" name="author" class="form-control" id="author" placeholder="name*" required>
            <label for="author">name*</label>
        </div>
        <input id="email" class="email" name="email" required type="text" value="<?= isset($email) ?  $email : 'test@mail.ru' ?>">
    </div>

<?php
    echo ob_get_clean();
}

function get_file_fields_html()
{
    ob_start();
?>

    <div class="file-field-container">
        <label for="images" class="p2 gray">add photo</label>
        <input id="images" name="images[]" type="file" multiple accept="image/*">
        <div class="p5">
            (.png, jpeg) - maximum size 5Mb
        </div>
        <div class="files-info h6">
        </div>
    </div>
<?php
    return ob_get_clean();
}


function handle_custom_review_fields($comment_id, $comment_approved, $commentdata)
{
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) { // Проверяем, есть ли файлы
        $files = $_FILES['images'];
        $uploaded_files = [];

        // Параметры для загрузки
        $upload_overrides = array('test_form' => false);

        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                // Подготовка каждого файла для загрузки
                $file = array(
                    'name'     => $files['name'][$key],
                    'type'     => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error'    => $files['error'][$key],
                    'size'     => $files['size'][$key]
                );

                // Загрузка файла
                $movefile = wp_handle_upload($file, $upload_overrides);

                // Если файл загружен без ошибок, сохраняем его URL
                if ($movefile && !isset($movefile['error'])) {
                    $uploaded_files[] = $movefile['url'];
                }
            }
        }

        // Если есть загруженные файлы, сохраняем их в метаданные комментария
        if (!empty($uploaded_files)) {
            $res = update_comment_meta($comment_id, 'images', $uploaded_files);
        }
    }
}


function add_enctype_to_comment_form()
{
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var form = document.querySelector("#commentform");
            if(form) {
                form.setAttribute("enctype", "multipart/form-data");
            }
        });
    </script>';
}

function render_file_fields($comment_field)
{
    if (! is_product()) {
        return $comment_field;
    }

    return $comment_field . get_file_fields_html();
}

function wc_clear_cart_url()
{
    if (isset($_REQUEST['empty_cart'])) {
        WC()->cart->empty_cart();
    }
}


function custom_woocommerce_product_reviews()
{
    global $product;

    $review_count = $product->get_rating_count();
    $rating = intval($product->get_average_rating());
    $comments = get_comments(array(
        'post_id' => $product->get_id(),
        'status' => 'approve',
    ));
    $total_comments = wp_count_comments($product->get_id());

?>

    <div class="product-reviews-block block">
        <div class="container">
            <div id="reviews" class="block-top block-title">
                <div class="left">
                    <h2>
                        reviews
                    </h2>
                    <div class="info">
                        <div class="h4 rating">
                            <?= $rating ?>
                        </div>
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_582_7891)">
                                <path d="M29.2046 10.7298L19.9821 9.32107L15.8484 0.516074C15.5396 -0.141426 14.4609 -0.141426 14.1521 0.516074L10.0196 9.32107L0.797101 10.7298C0.626822 10.756 0.467044 10.8286 0.335309 10.9396C0.203575 11.0506 0.104978 11.1958 0.0503442 11.3591C-0.00428933 11.5225 -0.0128467 11.6978 0.0256118 11.8657C0.0640702 12.0337 0.148057 12.1878 0.268351 12.3111L6.96835 19.1786L5.3846 28.8873C5.35632 29.061 5.37747 29.239 5.44562 29.4012C5.51377 29.5634 5.62619 29.7031 5.77001 29.8045C5.91383 29.9058 6.08325 29.9646 6.25891 29.9741C6.43457 29.9837 6.60938 29.9437 6.76335 29.8586L15.0009 25.3061L23.2384 29.8598C23.3923 29.9449 23.5671 29.985 23.7428 29.9754C23.9185 29.9658 24.0879 29.907 24.2317 29.8057C24.3755 29.7044 24.4879 29.5647 24.5561 29.4025C24.6242 29.2403 24.6454 29.0622 24.6171 28.8886L23.0334 19.1798L29.7334 12.3123C29.854 12.189 29.9383 12.0349 29.977 11.8667C30.0156 11.6986 30.0072 11.5231 29.9525 11.3595C29.8978 11.1959 29.7991 11.0506 29.6671 10.9395C29.5352 10.8284 29.3751 10.7558 29.2046 10.7298Z" fill="#FFA2A2" />
                            </g>
                            <defs>
                                <clipPath id="clip0_582_7891">
                                    <rect width="30" height="30" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        <div class="p3 count">
                            (<?= $review_count  ?> reviews)
                        </div>
                    </div>
                </div>
                <div class="right">
                    <div class="mini-btn-b" data-modal data-src="#modal-review">
                        write a review
                    </div>
                </div>
            </div>
            <?php if (!empty($comments)) { ?>
                <div class="comments-wrapper">
                    <div class="comments">
                        <?php foreach ($comments as $key => $comment) {
                            $author = $comment->comment_author;
                            $content = $comment->comment_content;
                            $date = date('d/m/Y', strtotime($comment->comment_date));
                            $rating = get_comment_meta($comment->comment_ID, 'rating', true);
                            $images = get_comment_meta($comment->comment_ID, 'images', true);

                        ?>
                            <div class="comment <?= $key > 2 ? 'disabled' : '' ?>">
                                <div class="comment__left">
                                    <div class="top">
                                        <?php if ($author != '') { ?>
                                            <div class="h4 author">
                                                <?= $author ?>
                                            </div>
                                        <?php } ?>

                                        <div class="icon">
                                            <svg width="120" height="20" viewBox="0 0 120 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_498_12901)">
                                                    <path d="M19.4694 7.15354L13.3211 6.21437L10.5652 0.344375C10.3594 -0.0939583 9.64024 -0.0939583 9.43441 0.344375L6.67941 6.21437L0.531075 7.15354C0.417556 7.17098 0.311037 7.21936 0.223214 7.29337C0.135391 7.36739 0.0696596 7.46417 0.0332373 7.57309C-0.00318507 7.68201 -0.00888996 7.79886 0.016749 7.91082C0.0423879 8.02277 0.0983792 8.12549 0.178575 8.20771L4.64524 12.786L3.58941 19.2585C3.57056 19.3743 3.58465 19.493 3.63009 19.6011C3.67552 19.7093 3.75047 19.8024 3.84634 19.87C3.94222 19.9375 4.05517 19.9767 4.17228 19.9831C4.28939 19.9895 4.40593 19.9628 4.50858 19.906L10.0002 16.871L15.4919 19.9069C15.5946 19.9636 15.7111 19.9903 15.8282 19.9839C15.9453 19.9775 16.0583 19.9383 16.1541 19.8708C16.25 19.8033 16.325 19.7101 16.3704 19.602C16.4158 19.4939 16.4299 19.3751 16.4111 19.2594L15.3552 12.7869L19.8219 8.20854C19.9023 8.12635 19.9585 8.02356 19.9843 7.91149C20.0101 7.79942 20.0044 7.68241 19.968 7.57334C19.9316 7.46427 19.8657 7.36737 19.7778 7.2933C19.6898 7.21923 19.5831 7.17087 19.4694 7.15354Z" fill="var(--primary)" />
                                                </g>
                                                <g clip-path="url(#clip1_498_12901)">
                                                    <path d="M44.4694 7.15354L38.3211 6.21437L35.5652 0.344375C35.3594 -0.0939583 34.6402 -0.0939583 34.4344 0.344375L31.6794 6.21437L25.5311 7.15354C25.4176 7.17098 25.311 7.21936 25.2232 7.29337C25.1354 7.36739 25.0697 7.46417 25.0332 7.57309C24.9968 7.68201 24.9911 7.79886 25.0167 7.91082C25.0424 8.02277 25.0984 8.12549 25.1786 8.20771L29.6452 12.786L28.5894 19.2585C28.5706 19.3743 28.5847 19.493 28.6301 19.6011C28.6755 19.7093 28.7505 19.8024 28.8463 19.87C28.9422 19.9375 29.0552 19.9767 29.1723 19.9831C29.2894 19.9895 29.4059 19.9628 29.5086 19.906L35.0002 16.871L40.4919 19.9069C40.5946 19.9636 40.7111 19.9903 40.8282 19.9839C40.9453 19.9775 41.0583 19.9383 41.1541 19.8708C41.25 19.8033 41.325 19.7101 41.3704 19.602C41.4158 19.4939 41.4299 19.3751 41.4111 19.2594L40.3552 12.7869L44.8219 8.20854C44.9023 8.12635 44.9585 8.02356 44.9843 7.91149C45.0101 7.79942 45.0044 7.68241 44.968 7.57334C44.9316 7.46427 44.8657 7.36737 44.7778 7.2933C44.6898 7.21923 44.5831 7.17087 44.4694 7.15354Z" fill="var(--<?= $rating > 1 ? 'primary' : 'gray' ?>)" />
                                                </g>
                                                <g clip-path="url(#clip2_498_12901)">
                                                    <path d="M69.4694 7.15354L63.3211 6.21437L60.5652 0.344375C60.3594 -0.0939583 59.6402 -0.0939583 59.4344 0.344375L56.6794 6.21437L50.5311 7.15354C50.4176 7.17098 50.311 7.21936 50.2232 7.29337C50.1354 7.36739 50.0697 7.46417 50.0332 7.57309C49.9968 7.68201 49.9911 7.79886 50.0167 7.91082C50.0424 8.02277 50.0984 8.12549 50.1786 8.20771L54.6452 12.786L53.5894 19.2585C53.5706 19.3743 53.5847 19.493 53.6301 19.6011C53.6755 19.7093 53.7505 19.8024 53.8463 19.87C53.9422 19.9375 54.0552 19.9767 54.1723 19.9831C54.2894 19.9895 54.4059 19.9628 54.5086 19.906L60.0002 16.871L65.4919 19.9069C65.5946 19.9636 65.7111 19.9903 65.8282 19.9839C65.9453 19.9775 66.0583 19.9383 66.1541 19.8708C66.25 19.8033 66.325 19.7101 66.3704 19.602C66.4158 19.4939 66.4299 19.3751 66.4111 19.2594L65.3552 12.7869L69.8219 8.20854C69.9023 8.12635 69.9585 8.02356 69.9843 7.91149C70.0101 7.79942 70.0044 7.68241 69.968 7.57334C69.9316 7.46427 69.8657 7.36737 69.7778 7.2933C69.6898 7.21923 69.5831 7.17087 69.4694 7.15354Z" fill="var(--<?= $rating > 2 ? 'primary' : 'gray' ?>)" />
                                                </g>
                                                <g clip-path="url(#clip3_498_12901)">
                                                    <path d="M94.4694 7.15354L88.3211 6.21437L85.5652 0.344375C85.3594 -0.0939583 84.6402 -0.0939583 84.4344 0.344375L81.6794 6.21437L75.5311 7.15354C75.4176 7.17098 75.311 7.21936 75.2232 7.29337C75.1354 7.36739 75.0697 7.46417 75.0332 7.57309C74.9968 7.68201 74.9911 7.79886 75.0167 7.91082C75.0424 8.02277 75.0984 8.12549 75.1786 8.20771L79.6452 12.786L78.5894 19.2585C78.5706 19.3743 78.5847 19.493 78.6301 19.6011C78.6755 19.7093 78.7505 19.8024 78.8463 19.87C78.9422 19.9375 79.0552 19.9767 79.1723 19.9831C79.2894 19.9895 79.4059 19.9628 79.5086 19.906L85.0002 16.871L90.4919 19.9069C90.5946 19.9636 90.7111 19.9903 90.8282 19.9839C90.9453 19.9775 91.0583 19.9383 91.1541 19.8708C91.25 19.8033 91.325 19.7101 91.3704 19.602C91.4158 19.4939 91.4299 19.3751 91.4111 19.2594L90.3552 12.7869L94.8219 8.20854C94.9023 8.12635 94.9585 8.02356 94.9843 7.91149C95.0101 7.79942 95.0044 7.68241 94.968 7.57334C94.9316 7.46427 94.8657 7.36737 94.7778 7.2933C94.6898 7.21923 94.5831 7.17087 94.4694 7.15354Z" fill="var(--<?= $rating > 3 ? 'primary' : 'gray' ?>)" />
                                                </g>
                                                <g clip-path="url(#clip4_498_12901)">
                                                    <path d="M119.469 7.15354L113.321 6.21437L110.565 0.344375C110.359 -0.0939583 109.64 -0.0939583 109.434 0.344375L106.679 6.21437L100.531 7.15354C100.418 7.17098 100.311 7.21936 100.223 7.29337C100.135 7.36739 100.07 7.46417 100.033 7.57309C99.9968 7.68201 99.9911 7.79886 100.017 7.91082C100.042 8.02277 100.098 8.12549 100.179 8.20771L104.645 12.786L103.589 19.2585C103.571 19.3743 103.585 19.493 103.63 19.6011C103.676 19.7093 103.75 19.8024 103.846 19.87C103.942 19.9375 104.055 19.9767 104.172 19.9831C104.289 19.9895 104.406 19.9628 104.509 19.906L110 16.871L115.492 19.9069C115.595 19.9636 115.711 19.9903 115.828 19.9839C115.945 19.9775 116.058 19.9383 116.154 19.8708C116.25 19.8033 116.325 19.7101 116.37 19.602C116.416 19.4939 116.43 19.3751 116.411 19.2594L115.355 12.7869L119.822 8.20854C119.902 8.12635 119.959 8.02356 119.984 7.91149C120.01 7.79942 120.004 7.68241 119.968 7.57334C119.932 7.46427 119.866 7.36737 119.778 7.2933C119.69 7.21923 119.583 7.17087 119.469 7.15354Z" fill="var(--<?= $rating > 4 ? 'primary' : 'gray' ?>)" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_498_12901">
                                                        <rect width="20" height="20" fill="white" />
                                                    </clipPath>
                                                    <clipPath id="clip1_498_12901">
                                                        <rect width="20" height="20" fill="white" transform="translate(25)" />
                                                    </clipPath>
                                                    <clipPath id="clip2_498_12901">
                                                        <rect width="20" height="20" fill="white" transform="translate(50)" />
                                                    </clipPath>
                                                    <clipPath id="clip3_498_12901">
                                                        <rect width="20" height="20" fill="white" transform="translate(75)" />
                                                    </clipPath>
                                                    <clipPath id="clip4_498_12901">
                                                        <rect width="20" height="20" fill="white" transform="translate(100)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="bottom gray p3">
                                        <?= $date ?>
                                    </div>
                                </div>
                                <div class="comment__center p2">
                                    <?= $content ?>
                                </div>
                                <div class="comment__right">
                                    <?php if (!empty($images)) { ?>
                                        <?php if (count($images) > 2) { ?>
                                            <div class="review-button-prev-<?= $key ?> button-prev">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.21111 15.4939L16.7754 23.7964C17.056 24.0683 17.5103 24.0679 17.7904 23.795C18.0703 23.5223 18.0696 23.0804 17.7889 22.8083L9.73445 15L17.7892 7.19165C18.0698 6.91954 18.0705 6.47795 17.7907 6.20514C17.724 6.14004 17.6448 6.08839 17.5576 6.05319C17.4703 6.01798 17.3768 5.9999 17.2823 6C17.1882 5.99989 17.095 6.01782 17.008 6.05278C16.921 6.08774 16.842 6.13904 16.7754 6.20373L8.21111 14.506C8.07596 14.6367 8.00012 14.8146 8.00012 15C8.00012 15.1853 8.07618 15.363 8.21111 15.4939Z" fill="#1B1B1B" />
                                                </svg>
                                            </div>
                                            <div class="review-button-next-<?= $key ?> button-next">
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.789 14.5061L12.2248 6.20355C11.9442 5.93169 11.4899 5.93214 11.2097 6.20496C10.9298 6.47773 10.9306 6.91961 11.2112 7.19168L19.2657 15L11.2109 22.8083C10.9303 23.0805 10.9296 23.5221 11.2095 23.7949C11.2761 23.86 11.3553 23.9116 11.4425 23.9468C11.5298 23.982 11.6233 24.0001 11.7178 24C11.8119 24.0001 11.9052 23.9822 11.9921 23.9472C12.0791 23.9123 12.1582 23.861 12.2247 23.7963L20.789 15.494C20.9242 15.3633 21 15.1854 21 15C21 14.8147 20.9239 14.637 20.789 14.5061Z" fill="#1B1B1B" />
                                                </svg>
                                            </div>
                                        <?php } ?>
                                        <div class="swiper review-swiper-<?= $key ?>">
                                            <div class="swiper-wrapper">
                                                <?php foreach ($images as $image) { ?>
                                                    <div class="swiper-slide">
                                                        <div class="image" data-fancybox="review-<?= $key ?>" data-src="<?= $image ?>">
                                                            <img src="<?= $image ?>" alt="">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    <?php } ?>
                                </div>
                                <script>
                                    jQuery(document).ready(function($) {

                                        const reviewSwiper<?= $key ?> = new Swiper('.review-swiper-<?= $key ?>', {
                                            slidesPerView: 2,
                                            spaceBetween: 10,

                                            navigation: {
                                                nextEl: '.review-button-next-<?= $key ?>',
                                                prevEl: '.review-button-prev-<?= $key ?>',
                                            },
                                        });
                                    });
                                </script>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (count($comments) > 3) { ?>
                        <div id="show-reviews" class="mini-btn-transparent">
                            load more
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
}

function custom_related_args($args)
{
    $args['columns'] = 4;
    $args['posts_per_page'] = 10;
    return $args;
}

function custom_product_block()
{
    if (!is_product()) {
        return;
    }

    global $product;
    $product_post = get_post($product->get_id());
    $block = get_field('images_block', $product_post);
    if (empty($block)) {
        return;
    }

    if ($block['big_image'] == '' || empty($block['images'])) {
        return;
    }

    $title = $block['title'];
    $big_image = $block['big_image'];
    $images = $block['images'];
    $products_title = $block['products_title'];
    $products = $block['products'];

?>
    <div class="product-images-block">
        <div class="container">
            <div class="content">
                <?php if ($big_image != '') { ?>
                    <div class="content__left">
                        <img src="<?= $big_image ?>" alt="">
                    </div>
                <?php } ?>
                <div class="content__right">
                    <div class="wrapper">
                        <?php if ($title != '') { ?>
                            <h2 class="title">
                                <?= $title ?>
                            </h2>
                        <?php } ?>
                        <?php if (!empty($images)) { ?>
                            <div class="images">
                                <?php foreach ($images as $image) { ?>
                                    <img src="<?= $image ?>" alt="">
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (!empty($products)) { ?>
                        <div class="products-title p1">
                            <?= $products_title ?>
                        </div>
                        <div class="products-swiper-wrapper">
                            <div class="swiper products-page-swiper">
                                <div class="swiper-wrapper">
                                    <?php foreach ($products as $item) { ?>
                                        <div class="swiper-slide">
                                            <?php
                                            $product = wc_get_product($item->ID);

                                            $post_object = get_post($product->get_id());

                                            setup_postdata($GLOBALS['post'] = &$post_object);

                                            wc_get_template_part('content', 'product');
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if (count($products) > 2) { ?>
                                <div class="page-products-button-prev button-prev">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.21111 15.4939L16.7754 23.7964C17.056 24.0683 17.5103 24.0679 17.7904 23.795C18.0703 23.5223 18.0696 23.0804 17.7889 22.8083L9.73445 15L17.7892 7.19165C18.0698 6.91954 18.0705 6.47795 17.7907 6.20514C17.724 6.14004 17.6448 6.08839 17.5576 6.05319C17.4703 6.01798 17.3768 5.9999 17.2823 6C17.1882 5.99989 17.095 6.01782 17.008 6.05278C16.921 6.08774 16.842 6.13904 16.7754 6.20373L8.21111 14.506C8.07596 14.6367 8.00012 14.8146 8.00012 15C8.00012 15.1853 8.07618 15.363 8.21111 15.4939Z" fill="#1B1B1B" />
                                    </svg>
                                </div>
                                <div class="page-products-button-next button-next">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.789 14.5061L12.2248 6.20355C11.9442 5.93169 11.4899 5.93214 11.2097 6.20496C10.9298 6.47773 10.9306 6.91961 11.2112 7.19168L19.2657 15L11.2109 22.8083C10.9303 23.0805 10.9296 23.5221 11.2095 23.7949C11.2761 23.86 11.3553 23.9116 11.4425 23.9468C11.5298 23.982 11.6233 24.0001 11.7178 24C11.8119 24.0001 11.9052 23.9822 11.9921 23.9472C12.0791 23.9123 12.1582 23.861 12.2247 23.7963L20.789 15.494C20.9242 15.3633 21 15.1854 21 15C21 14.8147 20.9239 14.637 20.789 14.5061Z" fill="#1B1B1B" />
                                    </svg>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php
}

function wc_refresh_mini_cart_count($fragments)
{
    $cart = WC()->cart->get_cart();
    $total_price = 0;
    ob_start();
?>
    <div class="cart-btn">
        <a href="<?= wc_get_cart_url(); ?>" class="woo-btn">
            <div class="cart-count" id="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></div>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.3342 15.4366H9.11371C8.42271 15.4363 7.75313 15.2004 7.21872 14.7691C6.68432 14.3378 6.31806 13.7376 6.18217 13.0705L4.16798 3.16401H0.997123C0.73267 3.16401 0.479048 3.06057 0.292051 2.87645C0.105054 2.69232 0 2.4426 0 2.1822C0 1.92181 0.105054 1.67209 0.292051 1.48796C0.479048 1.30384 0.73267 1.2004 0.997123 1.2004H4.98562C5.21889 1.19576 5.44643 1.27182 5.62862 1.41534C5.81081 1.55886 5.93612 1.76074 5.98274 1.98584L8.13653 12.6876C8.18314 12.9127 8.30846 13.1145 8.49065 13.2581C8.67284 13.4016 8.90038 13.4776 9.13365 13.473H19.3342C19.5661 13.4781 19.7925 13.4034 19.9745 13.2618C20.1565 13.1203 20.2827 12.9207 20.3313 12.6974L21.9367 5.33381C21.9684 5.18882 21.9663 5.03864 21.9306 4.89457C21.8948 4.75049 21.8264 4.61625 21.7305 4.50193C21.6345 4.38761 21.5135 4.29618 21.3765 4.2345C21.2396 4.17283 21.0902 4.14251 20.9396 4.14582H10.9684C10.7039 4.14582 10.4503 4.04238 10.2633 3.85826C10.0763 3.67413 9.97123 3.42441 9.97123 3.16401C9.97123 2.90362 10.0763 2.65389 10.2633 2.46977C10.4503 2.28564 10.7039 2.1822 10.9684 2.1822H20.9396C21.3924 2.17191 21.8416 2.26299 22.2535 2.44858C22.6653 2.63416 23.029 2.9094 23.317 3.25354C23.6051 3.59768 23.81 4.00173 23.9164 4.43522C24.0227 4.86871 24.0277 5.32032 23.931 5.75599L22.3057 13.1196C22.1583 13.7843 21.7819 14.3782 21.2402 14.8006C20.6985 15.223 20.0251 15.4477 19.3342 15.4366ZM9.97123 22.8002C9.3796 22.8002 8.80125 22.6274 8.30932 22.3038C7.81739 21.9802 7.43398 21.5201 7.20757 20.9819C6.98116 20.4437 6.92192 19.8515 7.03734 19.2801C7.15276 18.7088 7.43766 18.184 7.85602 17.772C8.27437 17.3601 8.80738 17.0796 9.38765 16.9659C9.96792 16.8523 10.5694 16.9106 11.116 17.1335C11.6626 17.3565 12.1298 17.734 12.4585 18.2184C12.7872 18.7028 12.9626 19.2722 12.9626 19.8548C12.9626 20.6359 12.6474 21.3851 12.0865 21.9375C11.5255 22.4899 10.7646 22.8002 9.97123 22.8002ZM9.97123 18.873C9.77402 18.873 9.58124 18.9305 9.41726 19.0384C9.25329 19.1463 9.12548 19.2996 9.05001 19.479C8.97454 19.6584 8.9548 19.8559 8.99327 20.0463C9.03174 20.2368 9.12671 20.4117 9.26616 20.549C9.40561 20.6863 9.58328 20.7798 9.7767 20.8177C9.97013 20.8556 10.1706 20.8362 10.3528 20.7618C10.535 20.6875 10.6907 20.5617 10.8003 20.4002C10.9099 20.2388 10.9684 20.049 10.9684 19.8548C10.9684 19.5944 10.8633 19.3446 10.6763 19.1605C10.4893 18.9764 10.2357 18.873 9.97123 18.873ZM18.9453 22.8002C18.3537 22.8002 17.7754 22.6274 17.2834 22.3038C16.7915 21.9802 16.4081 21.5201 16.1817 20.9819C15.9553 20.4437 15.896 19.8515 16.0115 19.2801C16.1269 18.7088 16.4118 18.184 16.8301 17.772C17.2485 17.3601 17.7815 17.0796 18.3618 16.9659C18.942 16.8523 19.5435 16.9106 20.0901 17.1335C20.6367 17.3565 21.1039 17.734 21.4326 18.2184C21.7613 18.7028 21.9367 19.2722 21.9367 19.8548C21.9367 20.6359 21.6216 21.3851 21.0606 21.9375C20.4996 22.4899 19.7387 22.8002 18.9453 22.8002ZM18.9453 18.873C18.7481 18.873 18.5553 18.9305 18.3914 19.0384C18.2274 19.1463 18.0996 19.2996 18.0241 19.479C17.9487 19.6584 17.9289 19.8559 17.9674 20.0463C18.0059 20.2368 18.1008 20.4117 18.2403 20.549C18.3797 20.6863 18.5574 20.7798 18.7508 20.8177C18.9442 20.8556 19.1447 20.8362 19.3269 20.7618C19.5091 20.6875 19.6649 20.5617 19.7744 20.4002C19.884 20.2388 19.9425 20.049 19.9425 19.8548C19.9425 19.5944 19.8374 19.3446 19.6504 19.1605C19.4634 18.9764 19.2098 18.873 18.9453 18.873Z" fill="white" />
            </svg>
        </a>
        <div class="cart-info">
            <div class="cart-info__top">
                <div class="h4 title">
                    cart
                </div>
                <a href="<?= wc_get_cart_url(); ?>" class="h6 link">
                    view all
                </a>
            </div>
            <?php if (!empty($cart)) {
                $total_price = 0;
            ?>
                <div class="products">
                    <?php foreach ($cart as $item) {
                        $product = $item['data'];
                        $product_post = get_post($product->get_id());
                        $image_id = $product->get_image_id();
                        $image = wp_get_attachment_image_url($image_id, 'medium');
                        $name = $product->get_name();
                        $quantity = $item['quantity'];
                        $amount_price = $item['line_subtotal'];
                        $total_price += $amount_price;
                        $price =  $product->get_price();
                        $link = get_permalink($product_post);
                    ?>
                        <div class="cart-item">
                            <div class="cart-item__image">
                                <?php if ($image != '') { ?>
                                    <img src="<?= $image ?>" alt="">
                                <?php } ?>
                                <div class="quantity">
                                    <?= $quantity ?>
                                </div>
                            </div>
                            <a href="<?= $link ?>" class="cart-item__center">
                                <div class="h6 name">
                                    <?= $name ?>
                                </div>
                                <div class="info">
                                    <div class="price p5">
                                        $<?= number_format($price, 2, '.', ' ')  ?>
                                    </div>
                                    <div class="p5 gray quantity">
                                        x<?= $quantity ?>
                                    </div>
                                </div>
                            </a>
                            <div class="cart-item__price">
                                <div class="p3 amount-price">
                                    $<?= number_format($amount_price, 2, '.', ' ')  ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="cart-info__bottom">
                <div class="total">
                    <div class="p1 count gray">
                        products in cart: <?= WC()->cart->get_cart_contents_count(); ?>
                    </div>
                    <div class="p1 price">
                        $<?= number_format($total_price, 2, '.', ' ')  ?>
                    </div>
                </div>
                <a href="<?= wc_get_cart_url(); ?>" class="mini-btn-b link">
                    view basket & checkout
                </a>
            </div>
        </div>
    </div>
<?php
    $fragments['.cart-btn'] = ob_get_clean();
    return $fragments;
}


function updateFavorites()
{
    echo WCFAVORITES()->count_items();
    exit();
}


function wc_clear_favorite_url()
{
    if (isset($_REQUEST['clear-fav'])) {
        WCFAVORITES()->remove_all_favorites();
    }
}

function custom_category_filter_button()
{
    if (is_shop()) {
        return;
    }
?>
    <div id="open-filter" class="mini-btn-transparent">
        filters
    </div>
<?php
}

function register_my_widgets()
{
    register_sidebar(
        array(
            'name'          => 'Product filter',
            'id'            => 'sidebar-shop',
            'description'   => '',
            'class'         => '',
            'before_sidebar' => '',
            'after_sidebar'  => '',
        )
    );
}



function custom_category_ordering_wrapper_open()
{
?>
    <div class="sort-wrappper">
    <?php
}

function custom_category_ordering_wrapper_close()
{
    ?>
    </div>
<?php
}

function custom_woocommerce_template_loop_category_title($category)
{
    $name = $category->name;
?>
    <div class="h4 category-name">
        <?= $name ?>
    </div>
    <?php
}

function custom_woocommerce_subcategory_thumbnail($category)
{
    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
    $image = wp_get_attachment_image_url($thumbnail_id, 'hd');
    if ($image != '') { ?>
        <div class="category-image">
            <img src="<?= $image ?>" alt="">
            <div class="btn-w">
                <svg width="30" height="16" viewBox="0 0 30 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_61_691)">
                        <path d="M0 8H29" stroke="#1B1B1B" />
                        <path d="M29.5 8.25L22.5 1" stroke="#1B1B1B" />
                        <path d="M22.5 15L29.5 7.75" stroke="#1B1B1B" />
                    </g>
                    <defs>
                        <clipPath id="clip0_61_691">
                            <rect width="30" height="15" fill="white" transform="translate(0 0.5)" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
        </div>
    <?php }
}

function custom_category_banner_block()
{
    if (!is_tax()) {
        return;
    }
    $category = get_queried_object();

    $title = $category->name;
    $image = get_field('banner_image', $category);
    $subcats = get_terms([
        'taxonomy' => 'product_cat',
        'parent'   => $category->term_id,
        'hide_empty' => false
    ]);
    if($image == ''){
        return;
    }
    ?>
    <div class="category-banner-block">
        <?php if ($image != '') { ?>
            <img src="<?= $image ?>" alt="" class="bg">
        <?php } ?>
        <h1 class="title">
            <?= $title ?>
        </h1>
        <?php if (!empty($subcats)) { ?>
            <div class="subcats">
                <?php foreach ($subcats as $subcat) { ?>
                    <a href="<?= get_term_link($subcat) ?>" class="subcat p4">
                        <?= $subcat->name ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php

}


?>