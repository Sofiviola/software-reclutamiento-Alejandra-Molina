<?php

/**
 * Template for choosing a package during the Job Listing submission.
 *
 * This template can be overridden by copying it to yourtheme/wc-paid-listings/package-selection.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager-resumes
 * @category    Template
 * @since       1.0.0
 * @version     2.9.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if ($packages || $user_packages) :
    $checked = 1;
?>
    <ul class="resume_packages products user-packages">
        <?php if ($user_packages) : ?>

            <?php foreach ($user_packages as $key => $package) :
                $package = wc_paid_listings_get_package($package);
            ?>
                <li class="user-job-package <?php echo $package->is_featured() ? 'user-job-package-featured' : '' ?>">
                    <input type="radio" <?php checked($checked, 1); ?> name="resume_package" value="user-<?php echo $key; ?>" id="user-package-<?php echo $package->get_id(); ?>" />
                    <label for="user-package-<?php echo $package->get_id(); ?>"><?php echo $package->get_title(); ?>
                        <p>
                            <?php
                            $featured_marking = $package->is_featured() ? __('featured', 'wp-job-manager-wc-paid-listings') : '';
                            if ($package->get_limit()) {
                                // translators: 1: Posted count. 2: Featured marking. 3: Limit.
                                $package_description = _n('%1$s %2$s job posted out of %3$d', '%1$s %2$s jobs posted out of %3$d', $package->get_count(), 'wp-job-manager-wc-paid-listings');
                                printf($package_description, $package->get_count(), $featured_marking, $package->get_limit());
                            } else {
                                // translators: 1: Posted count. 2: Featured marking.
                                $package_description = _n('%1$s %2$s job posted', '%1$s %2$s jobs posted', $package->get_count(), 'wp-job-manager-wc-paid-listings');
                                printf($package_description, $package->get_count(), $featured_marking);
                            }

                            if ($package->get_duration()) {
                                printf(', ' . _n('listed for %s day', 'listed for %s days', $package->get_duration(), 'wp-job-manager-wc-paid-listings'), $package->get_duration());
                            }

                            $checked = 0;
                            ?>
                        </p>
                    </label>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>


    <h4 class="headline centered margin-bottom-25"><strong><?php
                                                            if ($user_packages) :
                                                                esc_html_e('Or Purchase New Package', 'workscout');
                                                            else :
                                                            //   esc_html_e('Choose Package', 'workscout'); 
                                                            endif; ?></strong></h4>
    <div class="clearfix"></div>
    <?php if ($packages) : ?>

        <div class="pricing-plans-container margin-top-30">
            <!-- Plan -->

            <?php foreach ($packages as $key => $package) :
                $product = wc_get_product($package);
                if (!$product->is_type(array('resume_package', 'resume_package_subscription')) || !$product->is_purchasable()) {
                    continue;
                }
                /* @var $product WC_Product_resume_package|WC_Product_resume_package_Subscription */
                if ($product->is_type('variation')) {
                    $post = get_post($product->get_parent_id());
                } else {
                    $post = get_post($product->get_id());
                }
            ?>
                <div class="pricing-plan <?php if ($product->is_featured()) echo "recommended"; ?> ">
                    <?php if ($product->is_featured()) { ?>
                        <div class="recommended-badge"><?php esc_html_e('Recommended', 'workscout') ?></div>
                    <?php } ?>
                    <h3><?php echo $product->get_title(); ?></h3>
                    <?php if ($product->get_short_description()) { ?> <p class="margin-top-10"><?php echo $product->get_short_description(); ?></p><?php } ?>

                    <div class="pricing-plan-label billed-monthly-label"><?php echo $product->get_price_html(); ?></div>
                    <div class="pricing-plan-features">

                        <ul>
                            <?php
                            $listingslimit = $product->get_limit();
                            if (!$listingslimit) {
                                echo "<li>";
                                esc_html_e('Unlimited number of listings', 'workscout');
                                echo "</li>";
                            } else { ?>
                                <li>
                                    <?php esc_html_e('This plan includes ', 'workscout');
                                    printf(_n('%d listing', '%s listings', $listingslimit, 'workscout') . ' ', $listingslimit); ?>
                                </li>
                            <?php }
                            $duration = $product->get_duration();
                            if ($duration > 0) : ?>
                                <li>
                                    <?php esc_html_e('Listings are visible ', 'workscout');
                                    printf(_n('for %s day', 'for %s days', $product->get_duration(), 'workscout'), $product->get_duration()); ?>
                                </li>
                            <?php else : ?>
                                <li>
                                    <?php esc_html_e('Unlimited availability of listings', 'workscout');  ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($product->is_featured()) : ?>
                                <li><?php esc_html_e('Highlighted in Search Results', 'workscout');  ?></li>
                            <?php endif; ?>
                        </ul>
                        <?php

                        echo $product->get_description();

                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="plan-features">
                        <input type="radio" <?php checked($checked, 1);
                                            $checked = 0; ?> name="resume_package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />
                        <label for="package-<?php echo $product->get_id(); ?>"><?php esc_html_e('Buy Package', 'workscout');  ?></label><br />
                    </div>

                </div>


            <?php endforeach; ?>
        <?php endif;
    $button_text   = 'before' !== get_option('job_manager_paid_listings_flow') ? __('Submit &rarr;', 'wp-job-manager-wc-paid-listings') : __('Listing Details &rarr;', 'wp-job-manager-wc-paid-listings'); ?>

        </div>
        <input type="submit" name="continue" class="button" value="<?php echo apply_filters('submit_job_step_choose_package_submit_text', $button_text); ?>" />

    <?php else : ?>

        <p><?php _e('No packages found', 'wp-job-manager-wc-paid-listings'); ?></p>

    <?php endif; ?>