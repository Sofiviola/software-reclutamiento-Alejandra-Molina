<?php

/**
 * Template for choosing a package during the Resume submission.
 *
 * This template can be overridden by copying it to yourtheme/wc-paid-listings/task-package-selection.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager-tasks
 * @category    Template
 * @since       1.0.0
 * @version     2.7.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if ($packages || $user_packages) :
    $checked = 1;
?>
    <form method="post" id="task_package_selection">
        
            <!-- <input type="submit" name="continue" class="button" value="<?php echo apply_filters('submit_task_step_choose_package_submit_text', $button_text); ?>" /> -->
            <input type="hidden" name="task_id" value="<?php echo esc_attr($task_id); ?>" />
            <input type="hidden" name="job_id" value="0" />
            <input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
            <input type="hidden" name="workscout_freelancer_form" value="<?php echo $form_name; ?>" />

        
        <div class="job_task_packages margin-top-40">
            <ul class="job_packages products user-packages">
                <?php if ($user_packages) : ?>
                    <h2><?php _e('Your active packages', 'workscout-freelancer'); ?></h2>
                    <?php foreach ($user_packages as $key => $package) :
                        $package = wc_paid_listings_get_package($package);
                    ?>
                        <li class="user-job-package <?php echo $package->is_featured() ? 'user-job-package-featured' : '' ?>">
                            <input type="radio" <?php checked($checked, 1); ?> name="task_package" value="user-<?php echo $key; ?>" id="user-package-<?php echo $package->get_id(); ?>" />
                            <label for="user-package-<?php echo $package->get_id(); ?>"><?php echo $package->get_title(); ?>
                                <p />
                                <?php
                                $featured_marking = $package->is_featured() ? __('featured', 'workscout-freelancer') : '';
                                if ($package->get_limit()) {
                                    // translators: 1: Posted count. 2: Featured marking. 3: Limit.
                                    $package_description = _n('%1$s %2$s task posted out of %3$d', '%1$s %2$s tasks posted out of %3$d', $package->get_count(), 'workscout-freelancer');
                                    printf($package_description, $package->get_count(), $featured_marking, $package->get_limit());
                                } else {
                                    // translators: 1: Posted count. 2: Featured marking.
                                    $package_description = _n('%1$s %2$s task posted', '%1$s %2$s tasks posted', $package->get_count(), 'workscout-freelancer');
                                    printf($package_description, $package->get_count(), $featured_marking);
                                }

                                if ($package->get_duration()) {
                                    printf(', ' . _n('listed for %s day', 'listed for %s days', $package->get_duration(), 'workscout-freelancer'), $package->get_duration());
                                }

                                $checked = 0;
                                ?>
                                </p>
                            </label>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <h4 class="headline centered margin-bottom-25"><strong>
                    <?php
                    if ($user_packages) :
                        esc_html_e('Or Purchase New Package', 'workscout-freelancer');
                    else :
                        esc_html_e('Choose Package', 'workscout-freelancer'); ?>
                    <?php endif; ?>
                </strong></h4>
            <div class="clearfix"></div>

            <?php if ($packages) :
                $counter = 0; ?>
                <div class="pricing-plans-container margin-top-40">

                    <?php foreach ($packages as $key => $package) :
                        $product = wc_get_product($package);
                        if (!$product->is_type(array('task_package', 'task_package_subscription')) || !$product->is_purchasable()) {
                            continue;
                        }
                        /* @var $product WC_Product_Resume_Package|WC_Product_Resume_Package_Subscription */
                        if ($product->is_type('variation')) {
                            $post = get_post($product->get_parent_id());
                        } else {
                            $post = get_post($product->get_id());
                        }


                    ?>
                        <!-- Pricing Plans Container -->

                        <!-- Plan -->
                        <div class="pricing-plan <?php if ($product->is_featured()) echo "recommended"; ?> ">
                            <?php if ($product->is_featured()) { ?>
                                <div class="recommended-badge"><?php esc_html_e('Recommended', 'workscout-freelancer') ?></div>
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
                                        esc_html_e('Unlimited number of listings', 'workscout-freelancer');
                                        echo "</li>";
                                    } else { ?>
                                        <li>
                                            <?php esc_html_e('This plan includes ', 'workscout-freelancer');
                                            printf(_n('%d listing', '%s listings', $listingslimit, 'workscout-freelancer') . ' ', $listingslimit); ?>
                                        </li>
                                    <?php }
                                    $duration = $product->get_duration();
                                    if ($duration > 0) : ?>
                                        <li>
                                            <?php esc_html_e('Listings are visible ', 'workscout-freelancer');
                                            printf(_n('for %s day', 'for %s days', $product->get_duration(), 'workscout-freelancer'), $product->get_duration()); ?>
                                        </li>
                                    <?php else : ?>
                                        <li>
                                            <?php esc_html_e('Unlimited availability of listings', 'workscout-freelancer');  ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($product->is_featured()) : ?>
                                        <li><?php esc_html_e('Highlighted in Search Results', 'workscout-freelancer');  ?></li>
                                    <?php endif; ?>
                                </ul>
                                <?php

                                echo $product->get_description();

                                ?>
                                <div class="clearfix"></div>
                            </div>
                            <div class="plan-features">
                                <input type="radio" <?php if (!$user_packages && $counter == 0) : ?> checked="checked" <?php endif; ?> name="task_package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />
                                <label class="button full-width margin-top-20" for="package-<?php echo $product->get_id(); ?>"><?php ($product->get_price()) ? esc_html_e('Buy this package', 'workscout-freelancer') : esc_html_e('Choose this package', 'workscout-freelancer');  ?></label>


                            </div>
                        </div>


                    <?php $counter++;
                    endforeach; ?>
                <?php endif; ?>
                </div>
            <?php else : ?>

                <p><?php _e('No packages found', 'workscout-freelancer'); ?></p>

            <?php endif; ?>

            <div class="submit-page">

                <p>
                    <input type="submit" name="continue" class="button" value="<?php echo apply_filters('submit_task_step_choose_package_submit_text', $button_text); ?>" />
                    <input type="hidden" name="task_id" value="<?php echo esc_attr($task_id); ?>" />
                    <input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
                    <input type="hidden" name="workscout_freelancer_form" value="<?php echo $form_name; ?>" />

                </p>
            </div>
    </form>