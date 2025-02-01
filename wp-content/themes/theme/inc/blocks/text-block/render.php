<?php

$content = get_field('content');

if($content == ''){
    return;
}

?>
<section id="text-block" classa="text-block">
    <div class="container">
        <div class="content seo-content">
            <?= $content ?>
        </div>
    </div>
</section>