<?php setup_postdata($post->ID); ?>
<!-- Task -->
<a href="<?php the_permalink(); ?>" class="task-listing <?php if (is_position_featured()) {
                                                            echo 'task-listing-featured';
                                                        } ?>">
    <?php if (is_position_featured()) { ?>
        <div class="listing-badge"><i class="fa fa-star"></i></div>
    <?php } ?>
    <!-- Job Listing Details -->
    <div class="task-listing-details">

        <!-- Details -->
        <div class="task-listing-description">
            <h3 class="task-listing-title">
                <?php the_title(); ?>
            </h3>
            <ul class="task-icons">
                <?php $company = get_post_meta($post->ID, '_company_id', true);
                if ($company) { ?>
                    <li><i class="icon-material-outline-business"></i> <?php echo get_the_title($company); ?></li>
                <?php } else { ?>
                    <li><i class="icon-material-outline-account-circle"></i> <?php esc_html_e('Private Person', 'workscout-freelancer'); ?></li>
                <?php } ?>
                <li><i class="icon-material-outline-location-on"></i> <?php ws_task_location(); ?></li>
                <li><i class="icon-material-outline-access-time"></i> <?php task_publish_date() ?></li>
            </ul>

            <p class="task-listing-text"><?php $content = get_the_content();
                                            echo wp_trim_words(get_the_content(), 20, '...'); ?></p>
            <?php
            $terms = get_the_terms($post->ID, 'task_skill');

            if ($terms && !is_wp_error($terms)) :
                echo '<div class="task-tags">';
                $jobcats = array();
                foreach ($terms as $term) {
                    echo "<span>" . $term->name . "</span>";
                }
            ?>
        </div>
    <?php
            endif; ?>

    </div>

    </div>

    <div class="task-listing-bid">
        <div class="task-listing-bid-inner">
            <?php

            ?>
            <div class="task-offers">
                <strong><?php echo get_workscout_task_range(); ?></strong>
                <span><?php echo get_workscout_task_type(); ?></span>
            </div>
            <span class="button button-sliding-icon ripple-effect"><?php esc_html_e('Bid Now', 'workscout-freelancer'); ?> <i class="icon-material-outline-arrow-right-alt"></i></span>
        </div>
    </div>
</a>