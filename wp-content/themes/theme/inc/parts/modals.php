<div id="modal-callback" class="theme-modal">
	<div class="close-modal">+</div>
	<div class="form__holder"></div>
</div>
<?php if (is_product()) { ?>
	<div id="modal-review" class="theme-modal modal-review">
		<div class="close-modal">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M19.65 3L12 9.79892L4.35 3L2 5.09312L12 14L22 5.09312L19.65 3Z" fill="#1B1B1B" />
				<path d="M4.35 21L12 14.2011L19.65 21L22 18.9069L12 10L2 18.9069L4.35 21Z" fill="#1B1B1B" />
			</svg>
		</div>
		<div class="form__holder">
			<div class="h3 title">
				write a review
			</div>
			<div class="p2 subtitle">
				after your review passes moderation, it will appear on the site
			</div>
			<?php if (get_option('woocommerce_review_rating_verification_required') == 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())) : ?>
				<div class="review_form_wrapper" id="review_form_wrapper">
					<div id="review_form">
						<?php
						$commenter    = wp_get_current_commenter();
						$comment_form = array(
							/* translators: %s is product title */
							'title_reply'         => have_comments() ? esc_html__('Add a review', 'woocommerce') : sprintf(esc_html__('Be the first to review &ldquo;%s&rdquo;', 'woocommerce'), get_the_title()),
							/* translators: %s is product title */
							'title_reply_to'      => esc_html__('Leave a Reply to %s', 'woocommerce'),
							'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
							'title_reply_after'   => '</span>',
							'label_submit'        => 'send',
							'logged_in_as'        => '',
							'comment_field'       => '',
							'comment_notes_after' => '',
						);

						$name_email_required = (bool) get_option('require_name_email', 1);
						$fields              = array();


						$comment_form['fields'] = array();

						foreach ($fields as $key => $field) {
							$field_html  = '<p class="comment-form-' . esc_attr($key) . '">';
							$field_html .= '<label for="' . esc_attr($key) . '">' . esc_html($field['label']);

							if ($field['required']) {
								$field_html .= '&nbsp;<span class="required">*</span>';
							}

							$field_html .= '</label><input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($field['value']) . '" size="30" ' . ($field['required'] ? 'required' : '') . ' /></p>';

							$comment_form['fields'][$key] = $field_html;
						}

						$account_page_url = wc_get_page_permalink('myaccount');
						if ($account_page_url) {
							/* translators: %s opening and closing link tags respectively */
							$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(esc_html__('You must be %1$slogged in%2$s to post a review.', 'woocommerce'), '<a href="' . esc_url($account_page_url) . '">', '</a>') . '</p>';
						}

						if (wc_review_ratings_enabled()) {
							$comment_form['comment_field'] = '<div class="comment-form-rating"><select name="rating" id="rating" required>
						<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
						<option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
						<option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
						<option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
						<option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
						<option value="1">' . esc_html__('Very poor', 'woocommerce') . '</option>
					</select>
					<label for="rating" class="p5">give a rating for the product</label></div>';
						}

						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Your review', 'woocommerce') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

						comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
						?>
						<script>
							jQuery(document).ready(function($) {
								$('.form-submit').append('<div class="p4 policy">I confirm that I have read the <a target="_blank" href="/privacy-policy">privacy policy</a></div>');
							});
						</script>
					</div>
				</div>
			<?php else : ?>
				<p class="woocommerce-verification-required"><?php esc_html_e('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'); ?></p>
			<?php endif; ?>
		</div>
	</div>
<?php } ?>
<div id="modal-success" class="theme-modal">
	<div class="close-modal">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M19.65 3L12 9.79892L4.35 3L2 5.09312L12 14L22 5.09312L19.65 3Z" fill="#1B1B1B" />
			<path d="M4.35 21L12 14.2011L19.65 21L22 18.9069L12 10L2 18.9069L4.35 21Z" fill="#1B1B1B" />
		</svg>
	</div>
	<div class="title h3">
		Thanks for the feedback
	</div>
	<div class="subtitle p2">
		it will appear on the site immediately after it passes moderation
	</div>
</div>
<div id="modal-error" class="theme-modal">
	<div class="close-modal">+</div>
	<div class="title h3">
		error!
	</div>
	<div class="subtitle p2">
		it will appear on the site immediately after it passes moderation
	</div>
</div>
<?php
