<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// $field = $data->field;
// $key = $data->key;

// if (isset($field['description'])) {
//     echo '<div class="notification closeable notice"><p class="description" id="' . $key . '-description">' . $field['description'] . '</p></div>';
// }
$field_key = $key;
?>

<?php

if (isset($field['value']) && is_array(($field['value']))) {
    $i = 0;
    foreach ($field['value'] as $key => $value) {
        $images[$i]['src'] = wp_get_attachment_image_src($key, 'full');
        $thumb = wp_get_attachment_image_src($key);
        $images[$i]['thumb'] = $thumb[0];
        $images[$i]['attachment_id'] = $key;
        $images[$i]['size'] = filesize(get_attached_file($key));
        $images[$i]['name'] = basename($images[$i]['src'][0]);
        $i++;
    }
    
?>
    <script>
        var images = '<?php echo json_encode($images); ?>';
    </script>
<?php }  ?>
<div id="media-uploader" <?php if (isset($field['max_files']) && $field['max_files'] > 0) { ?> data-maxfiles="<?php echo $field['max_files']; ?>" <?php } ?> class="dropzone <?php echo esc_attr($field_key); ?>">
    <div class="dz-default dz-message"><span><i class="sl sl-icon-plus"></i> <?php esc_html_e('Click here or drop files to upload', 'workscout') ?></span></div>
</div>
<div data-elementkey="<?php echo esc_attr($key); ?>" id="media-uploader-ids">
    <?php
    if (isset($field['value']) && is_array(($field['value']))) {
        foreach ($field['value'] as $key => $value) { ?>
            <input id="gallery<?php echo esc_attr($key); ?>" type="hidden" name="gallery[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>">
    <?php }
    } ?>
</div>