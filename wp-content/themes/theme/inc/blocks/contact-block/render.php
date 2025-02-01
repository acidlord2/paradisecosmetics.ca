<?php

$title = get_field('title');
$emails = @settings('emails');

?>
<section id="contact-block" class="contact-block">
    <div class="contact-container">

        <?php if ($title != '') { ?>
            <div class="content">
                <?= $title ?>
            </div>
        <?php } ?>
        <?= get_form('contact') ?>
    </div>
</section>