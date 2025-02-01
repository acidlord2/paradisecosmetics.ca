<?php

$title = get_field('title');
$discounts = get_field('discounts');

if (empty($discounts)) {
    return;
}

?>
<section id="discount-block" class="discount-block">
    <div class="container">
        <div class="top-title">
            <?php if ($title != '') { ?>
                <h2 class="title">
                    <?= $title ?>
                </h2>
            <?php } ?>
            <a href="/discount" class="info">
                <div class="p1">
                    view all discount
                </div>
                <div class="mini-btn">
                    <svg width="30" height="15" viewBox="0 0 30 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_30_3333)">
                            <path d="M0 7.5H29" stroke="#fff" />
                            <path d="M29.5 7.75L22.5 0.5" stroke="#fff" />
                            <path d="M22.5 14.5L29.5 7.25" stroke="#fff" />
                        </g>
                        <defs>
                            <clipPath id="clip0_30_3333">
                                <rect width="30" height="15" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                    <svg width="30" height="15" viewBox="0 0 30 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_30_3333)">
                            <path d="M0 7.5H29" stroke="#1B1B1B" />
                            <path d="M29.5 7.75L22.5 0.5" stroke="#1B1B1B" />
                            <path d="M22.5 14.5L29.5 7.25" stroke="#1B1B1B" />
                        </g>
                        <defs>
                            <clipPath id="clip0_30_3333">
                                <rect width="30" height="15" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </a>
        </div>
        <div class="discounts">
            <?php foreach ($discounts as $discount) {
                get_template_part('inc/parts/components/discount', null, $discount);
            } ?>
        </div>
    </div>
</section>