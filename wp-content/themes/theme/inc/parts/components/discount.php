<?php
$discount = $args;
$title = get_the_title($discount);
$time = get_field('time', $discount);
$text = get_field('text', $discount);
$link = get_field('link', $discount);
$image = get_the_post_thumbnail_url($discount, 'hd');

?>

<div class="discount">
    <?php if ($image != '') { ?>
        <img src="<?= $image ?>" alt="" class="bg">
    <?php } ?>
    <div class="discount__content">
        <?php if ($time != '') { ?>
            <div class="p5 time">
                <?= $time ?>
            </div>
        <?php } ?>
        <div class="h3 title">
            <?= $title ?>
        </div>
        <div class="desc text">
            <div class="desc-text">
                <?php if (strlen($text) >= 350) {
                    $shortText = mb_strimwidth($text, 0, (350 / 2), '...');
                ?>
                    <div class="short__text">
                        <p class="p2">
                            <?= $shortText; ?>
                        </p>
                    </div>
                    <div class="full__text p2" style="display: none;">
                        <p><?= $text; ?></p>
                    </div>
                    <div type="button" class="structure-text-all h6">
                        show more
                    </div>
                <?php } else { ?>
                    <div class="full__text">
                        <p class="p2"><?= $text; ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <a href="<?= $link != '' ? $link : '/shop' ?>" class="big-btn-b">
            <svg width="30" height="16" viewBox="0 0 30 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_30_5313)">
                    <path d="M0 8H29" stroke="#1B1B1B" />
                    <path d="M29.5 8.25L22.5 1" stroke="#1B1B1B" />
                    <path d="M22.5 15L29.5 7.75" stroke="#1B1B1B" />
                </g>
                <defs>
                    <clipPath id="clip0_30_5313">
                        <rect width="30" height="15" fill="white" transform="translate(0 0.5)" />
                    </clipPath>
                </defs>
            </svg>
        </a>
    </div>
</div>