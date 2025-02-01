<?php

$image = get_field('image');
$title = get_field('title');
$subtitle = get_field('subtitle');
$text = get_field('text');
$link = get_field('link');

?>
<section id="present-block" class="present-block">
    <div class="container">
        <div class="present">
            <?php if ($image != '') { ?>
                <img src="<?= $image ?>" alt="" class="bg">
            <?php } ?>
            <div class="present__content">
                <?php if ($subtitle != '') { ?>
                    <div class="h6 subtitle">
                        <?= $subtitle ?>
                    </div>
                <?php } ?>
                <?php if ($title != '') { ?>
                    <h2 class="title">
                        <?= $title ?>
                    </h2>
                <?php } ?>
                <?php if ($text != '') { ?>
                    <div class="text p2">
                        <?= $text ?>
                    </div>
                <?php } ?>
                <?php if (!empty($link)) { ?>
                    <a href="<?= $link['url'] ?>" class="btn p1">
                        <?= $link['title'] ?>
                        <svg width="30" height="16" viewBox="0 0 30 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_30_109)">
                                <path d="M0 8H29" stroke="#1B1B1B" />
                                <path d="M29.5 8.25L22.5 1" stroke="#1B1B1B" />
                                <path d="M22.5 15L29.5 7.75" stroke="#1B1B1B" />
                            </g>
                            <defs>
                                <clipPath id="clip0_30_109">
                                    <rect width="30" height="15" fill="white" transform="translate(0 0.5)" />
                                </clipPath>
                            </defs>
                        </svg>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</section>