<?php

/**
 * Shows the `keyword input` form field on job listing forms.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/form-fields/text-field.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Purethemes
 * @package     wp-job-manager
 * @category    Template
 * @version     1.31.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$field_name = isset($field['name']) ? $field['name'] : $key;
            // Get selected value.
if (isset($field['value'])) {
    $selected = $field['value'];
} elseif (is_int($field['default'])) {
    $selected = $field['default'];
} elseif (!empty($field['default']) && ($term = get_term_by('slug', $field['default'], $field['taxonomy']))) {
    $selected = $term->term_id;
} else {
    $selected = '';
}
// turn array into string with comma separated values
$selected_terms = array();

?>
<div class="keywords-container">
    <div class="keyword-input-container">
        <input type="text" class="keyword-input with-border" placeholder="<?php esc_html_e('Add Skills', 'workscout-freelancer'); ?>" />
        <button class="keyword-input-button ripple-effect"><i class="icon-material-outline-add"></i></button>
    </div>
    <div class="keywords-list">
        <!-- keywords go here -->
        <?php if(is_array($selected)){
            foreach($selected as $key ){
                $term = get_term_by('id', $key, 'task_skill');
                $selected_terms[] = $term->name;
                echo '<span class="keyword"><span class="keyword-remove"></span><span class="keyword-text">'. $term->name.'</span></span>';
            }
            }
          ?>
        
    </div>
    <input type="hidden" name="<?php echo esc_attr($field_name); ?>" class="keyword-input-real" id="<?php echo esc_attr($key); ?>" placeholder="" value="<?php echo isset($field['value']) ?  implode(',', $selected_terms) : ''; ?>" <?php if (!empty($field['required'])) echo 'required'; ?> />
    <div class="clearfix"></div>
</div>