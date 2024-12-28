<?php $current_user = wp_get_current_user(); ?>
<div class="submit-field">
    <div class="bidding-widget">
        <!-- Headline -->
        <span class="bidding-detail"> <?php esc_html_e('Set your minimal hourly rate', 'workscout-freelancer'); ?></span>
        <?php
        $default_freelancer_rate  = ($current_user->freelancer_rate) ? $current_user->freelancer_rate : 45; ?>
        <!-- Slider -->
        <div class="bidding-value margin-bottom-10">$<span id="biddingVal"></span></div>
        <input name="freelancer_rate" class="bidding-slider" type="text" value="" data-slider-handle="custom" data-slider-currency="$" data-slider-min="5" data-slider-max="150" data-slider-value="<?php echo $default_freelancer_rate; ?>" data-slider-step="1" data-slider-tooltip="hide" />
    </div>
</div>

<div class="submit-field">
    <h5>Skills <i class="help-icon" data-tippy-placement="right" title="<?php esc_html_e('Add up to 10 skills', 'workscout-freelancer'); ?>"></i></h5>

    <!-- Skills List -->
    <div class="keywords-container">
        <div class="keyword-input-container">
            <input type="text" class="keyword-input with-border" placeholder="<?php esc_html_e('e.g. Angular, Laravel', 'workscout-freelancer'); ?>" />
            <button class="keyword-input-button ripple-effect"><i class="icon-material-outline-add"></i></button>
        </div>
        <?php //get terms  from 
        $terms = get_terms(array(
            'taxonomy' => 'user_skill',
            'hide_empty' => false,
        ));
        ?>
        <div class="keywords-list">
            <!-- <span class="keyword"><span class="keyword-remove"></span><span class="keyword-text">Angular</span></span>
            <span class="keyword"><span class="keyword-remove"></span><span class="keyword-text">Vue JS</span></span>
            <span class="keyword"><span class="keyword-remove"></span><span class="keyword-text">iOS</span></span>
            <span class="keyword"><span class="keyword-remove"></span><span class="keyword-text">Android</span></span>
            <span class="keyword"><span class="keyword-remove"></span><span class="keyword-text">Laravel</span></span> -->
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<div class="submit-field">
    <h5><?php esc_html_e('Attachments'); ?></h5>

    <!-- Attachments -->
    <?php $freelancer_attachemnts = get_user_meta($current_user->ID, 'freelancer_attachments', true);
    if ($freelancer_attachemnts) :
        $attachment_ids = explode(',', $freelancer_attachemnts); ?>
        <div class="attachments-container">

            <?php foreach ($attachment_ids as $attachment_id) {
                $attachment = get_post($attachment_id);
                $filetype = wp_check_filetype($attachment->guid);
                $filetype = $filetype['ext'];
                $filename = basename($attachment->guid);
                $file_url = wp_get_attachment_url($attachment_id);
            ?>
                <div class="attachment-box ripple-effect">
                    <span><?php
                            // make the filename max 20 characters long and add ... if it's longer
                            if (strlen($filename) > 20) {
                                $filename = substr($filename, 0, 20) . '...';
                            }
                            echo $filename; ?></span>

                    <i><?php echo $filetype; ?></i>
                    <button class="remove-attachment" data-tippy-placement="top" title="<?php echo esc_html('Remove', 'workscout-freelancer'); ?>"></button>
                </div>


            <?php } ?>
        </div>

        <div class="clearfix"></div>
    <?php endif; ?>



    <!-- Upload Button -->
    <div class="uploadButton margin-top-0">
        <input class="uploadButton-input" name="freelancer_attachments[]" type="file" accept="image/*, application/pdf" id="upload" multiple />
        <label class="uploadButton-button ripple-effect" for="upload"><?php esc_html_e('Upload Files','workscout-freelancer'); ?></label>
        <s<span class="uploadButton-file-name"><?php printf(esc_html__('Maximum file size: %s.', 'workscout-freelancer'), size_format(wp_max_upload_size())); ?></span>
    </div>

</div>

<div class="submit-field">
    <h5><?php esc_html_e('Tagline'); ?></h5>
    <input name="freelancer_tagline" type="text" class="with-border" value="<?php echo $current_user->freelancer_tagline ? $current_user->freelancer_tagline : ''; ?>" placeholder="<?php esc_html_e('iOS Expert + Node Dev'); ?>">
</div>

<div class="submit-field">
    <h5><?php esc_html_e('Nationality', 'workscout-freelancer'); ?></h5>
    <select name="freelancer_country" class="selectpicker with-border" data-size="7" data-live-search="true">
        <option value=""><?php esc_html_e('Select Country', 'workscout-freelancer'); ?></option>
        <?php
        $countries = workscoutGetCountries();
        foreach ($countries as $key => $value) {
            $selected = ($current_user->freelancer_country == $key) ? 'selected' : '';
            echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        ?>

    </select>
</div>