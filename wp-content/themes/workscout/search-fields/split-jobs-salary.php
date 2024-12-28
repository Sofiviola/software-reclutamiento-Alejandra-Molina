<!-- Panel Dropdown -->
<div class="panel-dropdown checkboxes" id="salary-panel">
	<a href="#"><?php esc_html_e('Salary', 'workscout'); ?></a>
	<div class="panel-dropdown-content ">

		<div class="widget_range_filter-inside">
			<div class="range-slider-subtitle"><?php esc_html_e('Select min and max salary range', 'workscout'); ?></div>

			<?php
			$salary_min = workscout_get_min_meta('_salary_min');
			$salary_max = workscout_get_max_meta('_salary_max');

			// find step for slider between rate_min and rate_max
			$range = $salary_max - $salary_min;
			if ($range <= 1000) {
				$step = 1; // Set a small step for a narrow range
			} else if ($range <= 10000) {
				$step = 100; // Set a medium step for a moderate range
			} else {
				$step = 500; // Set a larger step for a wide range
			}

			?>
			<input class="range-slider" id="salary-range" name="filter_by_salary" type="text" value="" data-slider-currency="<?php echo get_workscout_currency_symbol(); ?>" data-slider-min="<?php echo esc_attr($salary_min); ?>" data-slider-max="<?php echo esc_attr($salary_max); ?>" data-slider-step="<?php echo esc_attr($step); ?>" data-slider-value="[<?php echo esc_attr($salary_min); ?>,<?php echo esc_attr($salary_max); ?>]" />


		</div>
		<!-- Panel Dropdown -->
		<div class="panel-buttons">

			<input type="checkbox" name="filter_by_salary_check" id="salary_check" class="filter_by_check" autocomplete="off">
			<label for="salary_check"><?php esc_html_e('Enable Salary Filter', 'workscout'); ?></label>



		</div>
	</div>
</div>