<?php

$questions = get_field('questions');
if (empty($questions)) {
    return;
}

?>
<section id="faqs-block" class="faqs-block">
    <div class="container">
        <div class="questions">
            <?php foreach ($questions as $key => $item) { ?>
                <div class="question">
                    <div class="question__top">
                        <div class="h5 title">
                            <?= $item['question'] ?>
                        </div>
                        <div class="open icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.355 8L12 12.9447L6.645 8L5 9.52227L12 16L19 9.52227L17.355 8Z" fill="#1B1B1B" />
                            </svg>
                        </div>
                        <div class="close icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.355 5L12 9.94467L6.645 5L5 6.52227L12 13L19 6.52227L17.355 5Z" fill="#1B1B1B" />
                                <path d="M6.645 18L12 13.0553L17.355 18L19 16.4777L12 10L5 16.4777L6.645 18Z" fill="#1B1B1B" />
                            </svg>
                        </div>
                    </div>
                    <div class="question__bottom p4">
                        <?= $item['answer'] ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>