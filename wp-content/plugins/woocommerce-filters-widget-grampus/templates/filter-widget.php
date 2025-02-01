<form class="filters-form" method="get" data-auto="<?= wc_bool_to_string($instance['auto']) ?>" action="<?= esc_url($instance['current_url']) ?>">
	<div class="filter-top">
		<div class="h4">
			filters
		</div>
		<div id="close-filter" class="close-filter">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M19.65 3L12 9.79892L4.35 3L2 5.09312L12 14L22 5.09312L19.65 3Z" fill="#1B1B1B" />
				<path d="M4.35 21L12 14.2011L19.65 21L22 18.9069L12 10L2 18.9069L4.35 21Z" fill="#1B1B1B" />
			</svg>
		</div>
	</div>
	<?php
	if (get_query_var('s')) {
	?>
		<input type="hidden" name="s" value="<?= get_query_var('s') ?>">
		<input type="hidden" name="post_type" value="product">
		<?php
	}
	foreach ($this->filters as $attr => $data) {
		if ($attr == 'price') {
			if ($data['price_min'] >= 0 and $data['price_max'] > 0 and ($data['price_min'] != $data['price_max'])) {
		?>
				<div class="filter-block filter-block-price opened">
					<div class="filter-block-content">
						<div class="filter-block-title h5">price ($)</div>
						<div class="range-slider" data-filter-node="slider" data-min="<?= $data['price_min'] ?>" data-max="<?= $data['price_max'] ?>"></div>
						<div class="inputs price range" data-mode="<?= $data['type'] ?>">
							<?php if ($data['type'] != 'ranges') { ?>
								<div class="group">
									<input type="number" data-label="Мин. цена" data-type="min" data-min="<?= $data['price_min'] ?>" data-name="min_price" value="<?= $data['current_min_price'] ?>" <?php if ($data['current_min_price'] != $data['price_min'] and $data['current_min_price'] != '') { ?>name="min_price" <?php } ?>>
								</div>
								<div class="group">
									<input type="number" data-label="Макс. цена" data-type="max" data-max="<?= $data['price_max'] ?>" data-name="max_price" value="<?= $data['current_max_price'] ?>" <?php if ($data['current_max_price'] != $data['price_max'] and $data['current_max_price'] != '') { ?>name="max_price" <?php } ?>>
								</div>
							<?php } else { ?>
								<input type="hidden" data-type="min" data-min="<?= $data['price_min'] ?>" data-name="min_price" value="<?= $data['current_min_price'] ?>" <?php if ($data['current_min_price'] != $data['price_min'] and $data['current_min_price'] != '') { ?>name="min_price" <?php } ?>>
								<input type="hidden" data-type="max" data-max="<?= $data['price_max'] ?>" data-name="max_price" value="<?= $data['current_max_price'] ?>" <?php if ($data['current_max_price'] != $data['price_max'] and $data['current_max_price'] != '') { ?>name="max_price" <?php } ?>>
							<?php } ?>
						</div>

						<?php if ($data['type'] != 'slider') { ?>
							<div class="inputs radios" data-filter-node="ranges">
								<?php
								foreach ($data['ranges'] as $index => $params) {
									$vid = esc_attr("filter_{$attr}_{$index}");
									$checked = ($params['active']) ? ' checked="checked"' : '';
								?>
									<div class="group">
										<input type="radio" id="<?= $vid ?>" data-min="<?= $params['min'] ?>" data-max="<?= $params['max'] ?>" <?= $checked ?>>
										<label for="<?= $vid ?>"><?= esc_html($params['name']) ?></label>
									</div>
								<?php
								}
								?>
							</div>
						<?php } else { ?>
						<?php } ?>
					</div>
				</div>
			<?php
			}
		} else {
			$vc = count($data['values']);
			if ($vc > 0) {
				$has_search = $data['search'] && $vc > 2;
				$search_id = '';
				$search_class = '';
				if ($has_search) {
					$search_id = ' id="fl_' . $attr . '"';
					$search_class = 'filterable';
				}
				$opened = ($data['has_active']) ? ' opened' : '';
			?>
				<div class="filter-block<?= $opened ?>">
					<div class="filter-block-header">
						<div class="filter-block-title"><?= esc_html($data['name']) ?></div>
						<div class="filter-block-toggler">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12 20.624C10.8281 20.624 10.8281 20.624 10.8281 19.4522V4.54779C10.8281 3.37592 10.8281 3.37592 12 3.37592C13.1719 3.37591 13.1719 3.37592 13.1719 4.54779V19.4522C13.1719 20.6241 13.1719 20.624 12 20.624Z" fill="#1B1B1B" />
								<path d="M19.4522 13.1719H4.54785C3.37598 13.1719 3.37598 13.1719 3.37598 12C3.37598 10.8281 3.37598 10.8281 4.54785 10.8281H19.4522C20.6241 10.8281 20.6241 10.8281 20.6241 12C20.6241 13.1719 20.6241 13.1719 19.4522 13.1719Z" fill="#1B1B1B" />
							</svg>
						</div>
					</div>
					<div <?= $search_id ?> class="filter-block-content <?= $search_class ?>" <?php if (!$opened) {
																									echo 'style="display:none"';
																								} ?>>
						<?php /* if ($has_search) { ?>
							<div class="local-search" <?php if (!$opened) {
															echo 'style="display:none"';
														} ?>>
								<input class="search" type="text" autocorrect="off" spellcheck="true" placeholder="Поиск по <?= esc_attr($data['name']) ?>">
							</div>
						<?php } */ ?>
						<div class="inputs checkboxes list">
							<?php
							foreach ($data['values'] as $params) {
								$vid = esc_attr("filter_{$attr}_{$params['value']}");
								$checked = ($params['active']) ? ' checked="checked"' : '';
								$pl = ($params['depth'] > 0) ? ' style="padding-left:' . ($params['depth']) . 'em;"' : '';
							?>
								<div class="group" <?= $pl ?>>
									<input type="checkbox" name="f_<?= $attr ?>[]" id="<?= $vid ?>" value="<?= $params['value'] ?>" <?= $checked ?>>
									<label class="group-label" for="<?= $vid ?>"><span class="indicator"></span><?= esc_html($params['name']) ?></label>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
	<?php
			}
		}
	}
	?>
	<?php if (!$instance['auto']) { ?>
		<div class="buttons">
			<button id="apply-filter" class="button mini-btn-transparent" type="submit" value="filter">apply filters</button>
			<button class="button link reset p5" type="reset" value="filter">
				<svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M8 2.5L2 8.5M8 8.5L2 2.5" stroke="#A0A0A0" stroke-width="1.5" stroke-linecap="round" />
				</svg>
				clear filters
			</button>
		</div>
	<?php } ?>
</form>