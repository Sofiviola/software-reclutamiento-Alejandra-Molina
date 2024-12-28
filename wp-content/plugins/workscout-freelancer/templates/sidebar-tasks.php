<form action="" id="workscout-frelancer-search-form-tasks" class="ajax-search">
    <!-- Location -->
    <div class="sidebar-widget">


        <div class="search_location  widget  task-widget-location widget_range_filter">
            <h4><?php esc_html_e('Location', 'workscout-freelancer'); ?></h4>
            <?php
            if (!empty($_GET['search_location'])) {
                $location = sanitize_text_field($_GET['search_location']);
            } else {
                $location = '';
            } ?>
            <div class="sidebar-search_location-container">
                <input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e('Location', 'workscout-freelancer'); ?>" value="<?php echo esc_attr($location); ?>" />
                <a href="#"><i title="<?php esc_html_e('Find My Location', 'workscout-freelancer') ?>" class="tooltip left la la-map-marked-alt"></i></a>
                <?php if (get_option('workscout_map_address_provider', 'osm') == 'osm') : ?><span class="type-and-hit-enter"><?php esc_html_e('type and hit enter', 'workscout-freelancer') ?></span> <?php endif; ?>
            </div>

            <?php

            $geocode = get_option('workscout_maps_api_server', 0);
            $default_radius = get_option('workscout_maps_default_radius');
            if ($geocode) : ?>
                <h4 class="checkboxes" style="margin-bottom: 0;">
                    <input type="checkbox" name="filter_by_radius_check" id="radius_check" class="filter_by_radius" <?php if (get_option('workscout_radius_state') == 'enabled') echo "checked"; ?>>
                    <label for="radius_check"><?php esc_html_e('Search by Radius', 'workscout-freelancer'); ?></label>
                </h4>


                <div class="widget_range_filter-inside">
                    <span class="range-slider-subtitle"><?php esc_html_e('Radius around selected destination', 'workscout-freelancer') ?></span>
                    <input name="search_radius" id="search_radius" data-slider-currency="<?php echo get_option('workscout_radius_unit'); ?>" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="50">
                    <div class=" margin-bottom-50"></div>
                </div>
                <div class="clearfix"></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="sidebar-widget widget">
        <?php
        if (!empty($_GET['search_keywords'])) {
            $keywords = sanitize_text_field($_GET['search_keywords']);
        } else {
            $keywords = '';
        }
        ?>
        <div class="search_keywords">
            <h4><?php esc_html_e('Keywords', 'workscout-freelancer') ?></h4>
            <input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('job title, keywords or company', 'workscout-freelancer'); ?>" value="<?php echo esc_attr($keywords); ?>" />
            <div class="clearfix"></div>
        </div>
    </div>

    <!-- Category -->
    <div class="widget sidebar-widget">
        <h4><?php esc_html_e('Category', 'workscout-freelancer') ?></h4>
        <?php

        if (!empty($_GET['tax-task_category'])) {
            $selected_category = sanitize_text_field($_GET['tax-task_category']);
        } else {
            $selected_category = "";
        }
        $dropdown = job_manager_dropdown_categories(
            array(
                'taxonomy' => 'task_category',
                'hierarchical' => 1,
                'depth' => -1,
                'class' =>  'select2-multiple job-manager-category-dropdown ' . (is_rtl() ? 'chosen-rtl' : ''),
                'name' => 'tax-task_category',
                'orderby' => 'name',
                'selected' => $selected_category,
                'placeholder'     => __('Choose a category', 'workscout-freelancer'),
                'hide_empty' => false,
                'echo' => false
            )
        );

        $fixed_dropdown = str_replace('&nbsp;&nbsp;&nbsp;', '- ', $dropdown);
        echo $fixed_dropdown; ?>
    </div>
    <!-- Budget -->
    <div class="widget sidebar-widget">

        <div class="widget widget_range_filter widget-fixed_rate-filter">

            <h4 class="checkboxes" style="margin-bottom: 0;">
                <input type="checkbox" name="filter_by_fixed_check" id="fixed_rate" class="filter_by_check">
                <label for="fixed_rate"><?php esc_html_e('Fixed Price', 'workscout-freelancer'); ?></label>
            </h4>


            <div class="widget_range_filter-inside">

                <input class="range-slider" name="filter_by_fixed" type="text" value="" data-slider-currency="<?php echo get_workscout_currency_symbol(); ?>" data-slider-min="10" data-slider-max="2500" data-slider-step="25" data-slider-value="[10,2500]" />
            </div>

        </div>
    </div>

    <!-- Hourly Rate -->
    <div class="widget sidebar-widget">

        <div class="widget widget_range_filter widget-hourly_rate-filter">

            <h4 class="checkboxes" style="margin-bottom: 0;">
                <input type="checkbox" name="filter_by_hourly_rate_check" id="hourly_rate" class="filter_by_check">
                <label for="hourly_rate"><?php esc_html_e('Hourly Rate', 'workscout-freelancer'); ?></label>
            </h4>


            <div class="widget_range_filter-inside">
                <input class="range-slider" name="filter_by_hourly_rate" type="text" value="" data-slider-currency="<?php echo get_workscout_currency_symbol(); ?>" data-slider-min="10" data-slider-max="150" data-slider-step="5" data-slider-value="[10,200]" />
            </div>

        </div>


        <!-- Range Slider -->

    </div>

    <!-- Tags -->
    <div class="widget sidebar-widget">

        <h4><?php esc_html_e('Skills', 'workscout-freelancer') ?></h4>

        <div class="tags-container">
            <?php

            $tasks = workscout_get_options_array('taxonomy', 'task_skill');
            $selected = array();
            // if(is_tax('task_skill')){
            // 	$selected[get_query_var('task_skill')] = 'on';
            // }	
            foreach ($tasks as $key => $value) {
            ?>
                <div class="tag">
                    <input <?php if (array_key_exists($value['slug'], $selected)) {
                                echo 'checked="checked"';
                            } ?> id="tax-<?php echo esc_html($value['slug']) ?>-task_skill" value="<?php echo esc_html($value['id']) ?>" type="checkbox" name="tax-task_skill<?php echo '[' . esc_html($value['slug']) . ']'; ?>">
                    <label for="tax-<?php echo esc_html($value['slug']) ?>-task_skill"><?php echo esc_html($value['name']) ?></label>
                </div>
            <?php }
            ?>

        </div>
        <div class="clearfix"></div>
    </div>
    <!-- Keywords -->


</form>