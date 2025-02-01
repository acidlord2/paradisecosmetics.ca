<?php

$image = get_field('image');
$content = get_field('content');
$left_content = get_field('left_content');
$right_content = get_field('right_content');

?>
<section id="about-block" class="about-block">
    <div class="container">
        <?php if ($image != '') { ?>
            <img src="<?= $image ?>" alt="" class="image">
        <?php } ?>
        <div class="<?= is_page('about') ? 'about-content' : 'content' ?>">
            <?php if ($left_content != '') { ?>
                <div class="left_content seo-content <?= $right_content == '' ? 'full' : '' ?>">
                    <?= $left_content ?>
                </div>
            <?php } ?>
            <div class="right_content seo-content">
                <?= $right_content ?>
            </div>
        </div>
    </div>
</section>