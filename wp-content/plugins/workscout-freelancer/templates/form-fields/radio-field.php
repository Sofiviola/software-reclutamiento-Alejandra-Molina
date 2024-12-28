<?php

/**
 * Shows the `radio` form field on job listing forms.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/form-fields/radio-field.php.
 *
 * Example definition:
 *
 * 'test_radio' => array(
 * 		'label'    => __( 'Test Radio', 'wp-job-manager' ),
 * 		'type'     => 'radio',
 * 		'required' => false,
 * 		'default'  => 'option2',
 * 		'priority' => 1,
 * 		'options'  => array(
 * 			'option1' => 'This is option 1',
 * 		 	'option2' => 'This is option 2'
 * 		)
 * 	)
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager
 * @category    Template
 * @version     1.31.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$field['default'] = empty($field['default']) ? current(array_keys($field['options'])) : $field['default'];
$default          = !empty($field['value']) ? $field['value'] : $field['default'];
?>
<div class="feedback-yes-no margin-top-0">


	<?php
	$i = 0;
	foreach ($field['options'] as $option_key => $value) : 
	$i++; ?>
		<div class="radio">
			<input type="radio" id="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); echo $i;?>" name="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); ?>" value="<?php echo esc_attr($option_key); ?>" <?php checked($default, $option_key); ?> />
			<label for="<?php echo esc_attr(isset($field['name']) ? $field['name'] : $key); echo $i; ?>"><span class="radio-label"></span> <?php echo esc_html($value); ?></label>
		</div>
	<?php endforeach; ?>
</div>