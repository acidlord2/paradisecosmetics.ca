<?php

$blocks = get_field('blocks');
if (empty($blocks)) {
    return;
}

?>
<section id="delivery-pay-block" class="delivery-pay-block">
    <div class="container">
        <div class="blocks">
            <?php foreach ($blocks as $block) { ?>
                <div class="block-item">
                    <h3 class="title">
                        <?= $block['title'] ?>
                    </h3>
                    <?php if (!empty($block['list'])) { ?>
                        <div class="list">
                            <?php foreach ($block['list'] as $key => $item) { ?>
                                <div class="item">
                                    <div class="item__title h4">
                                        <span class="h4">0<?= $key + 1 ?>.</span> <?= $item['title'] ?>
                                    </div>
                                    <div class="item__subtitle p2">
                                        <?= $item['subtitle'] ?>
                                    </div>
                                    <div class="item__text p2">
                                        <?= $item['text'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>