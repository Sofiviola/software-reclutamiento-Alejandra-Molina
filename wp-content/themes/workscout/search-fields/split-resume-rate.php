<!-- Panel Dropdown -->
<div class="panel-dropdown checkboxes" id="rate-panel">
	<a href="#"><?php esc_html_e('Rate', 'workscout'); ?></a>
	<div class="panel-dropdown-content ">

		<div class="widget_range_filter-inside">
			<div class="range-slider-subtitle"><?php esc_html_e('Select min and max rate range', 'workscout'); ?></div>


			<?php
			$rate_min = workscout_get_min_meta('_rate_min', 'resume');
			$rate_max = workscout_get_max_meta('_rate_min', 'resume');

			// find step for slider between rate_min and rate_max
			$range = $rate_max - $rate_min;
			if ($range <= 1000) {
				$step = 1; // Set a small step for a narrow range
			} else if ($range <= 10000) {
				$step = 100; // Set a medium step for a moderate range
			} else {
				$step = 500; // Set a larger step for a wide range
			}
			?>
			<input class="range-slider" id="rate-range" name="filter_by_rate" type="text" value="" data-slider-currency="<?php echo get_workscout_currency_symbol(); ?>" data-slider-min="<?php echo esc_attr($rate_min); ?>" data-slider-max="<?php echo esc_attr($rate_max); ?>" data-slider-step="<?php echo esc_attr($step); ?>" data-slider-value="[<?php echo esc_attr($rate_min); ?>,<?php echo esc_attr($rate_max); ?>]" />

		</div>
		<!-- Panel Dropdown -->
		<div class="panel-buttons">

			<input type="checkbox" name="filter_by_rate_check" id="filter_by_rate" class="filter_by_check">
			<label for="filter_by_rate"><?php esc_html_e('Enable Rate Filter', 'workscout'); ?></label>

		</div>
	</div>
</div>
<!-- Panel Dropdown -->