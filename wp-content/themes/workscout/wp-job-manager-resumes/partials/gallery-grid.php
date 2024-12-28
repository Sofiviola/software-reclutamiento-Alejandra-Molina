<?php
$count_gallery = workscout_count_gallery_items($post->ID);
$gallery = get_post_meta($post->ID, '_gallery', true);
$thumbnail_id = get_post_thumbnail_id($post->ID);

if (empty($gallery)) {
    return;
}
//do while until reach the count gallery value
$popup_gallery = array();
$grid_gallery = array_keys($gallery);
//move the array element that has the same value as thumbnail_id to the first element of the array
if (($key = array_search($thumbnail_id, $grid_gallery)) !== false) {
    unset($grid_gallery[$key]);
    array_unshift($grid_gallery, $thumbnail_id);
}

foreach ($grid_gallery as $key => $value) {
    $popup_gallery[] = wp_get_attachment_image_url($value, 'listeo-gallery');
}

?>

<h3 class="margin-bottom-25"><?php esc_html_e('Portfolio', 'workscout'); ?></h3>
        <div class="listeo-single-listing-gallery-grid">
            <div id="single-listing-grid-gallery" <?php if ($count_gallery == 1) {
                                                        echo 'class="slg-one-photo"';
                                                    } ?>>
                <?php
                if ($count_gallery == 1) {
                    $image0 = wp_get_attachment_image_src($grid_gallery[0], 'listeo-gallery');
                    echo '<a href="' . esc_url($image0[0]) . '" class="slg-gallery-img"><img src="' . esc_url($image0[0]) . '" /></a>';
                }
                ?>
                <?php if ($count_gallery > 1) { ?>
                    <a href="#" id="single-listing-grid-gallery-popup" data-gallery="<?php echo esc_attr(json_encode($popup_gallery)); ?>" data-gallery-count="<?php echo esc_attr($count_gallery); ?>" class="slg-button"><i class="sl sl-icon-grid"></i> <?php esc_html_e('Show All Photos', 'workscout') ?></a>
                <?php } ?>
                <?php if ($count_gallery > 1) { ?>
                    <div class="slg-half">
                        <?php $image0 = wp_get_attachment_image_src($grid_gallery[0], 'listeo-gallery'); ?>
                        <a data-grid-start-index="0" href="<?php echo $image0[0]; ?>" class="slg-gallery-img"><img src="<?php echo $image0[0]; ?>" /></a>
                    </div>
                    <?php if ($count_gallery > 2) { ?>
                        <div class="slg-half">
                            <div class="slg-grid">
                                <div class="slg-grid-top">
                                    <?php if ($count_gallery >= 3) { ?>
                                        <?php $image1 = wp_get_attachment_image_src($grid_gallery[1], 'listeo-gallery');
                                        ?>
                                        <div class="slg-grid-inner"><a data-grid-start-index="1" href="<?php echo $image1[0]; ?>" class="slg-gallery-img"><img src="<?php echo $image1[0]; ?>" /></a></div>
                                    <?php } ?>
                                    <?php if ($count_gallery >= 4) { ?>
                                        <?php $image3 = wp_get_attachment_image_src($grid_gallery[3], 'listeo-gallery'); ?>
                                        <div class="slg-grid-inner"><a data-grid-start-index="3" href="<?php echo $image3[0]; ?>" class="slg-gallery-img"><img src="<?php echo $image3[0]; ?>" /></a></div>
                                    <?php } ?>
                                </div>
                                <div class="slg-grid-bottom">
                                    <?php if ($count_gallery >= 3) { ?>
                                        <?php $image2 = wp_get_attachment_image_src($grid_gallery[2], 'listeo-gallery'); ?>
                                        <div class="slg-grid-inner"><a data-grid-start-index="2" href="<?php echo $image2[0]; ?>" class="slg-gallery-img"><img src="<?php echo $image2[0]; ?>" />></a></div>
                                    <?php } ?>
                                    <?php if ($count_gallery >= 5) { ?>
                                        <?php $image4 = wp_get_attachment_image_src($grid_gallery[4], 'listeo-gallery'); ?>
                                        <div class="slg-grid-inner"><a data-grid-start-index="4" href="<?php echo $image4[0]; ?>" class="slg-gallery-img"><img src="<?php echo $image4[0]; ?>" /></a></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } else {
                        // count gallery rowne 2 
                    ?>
                        <div class="slg-half">
                            <?php $image1 = wp_get_attachment_image_src($grid_gallery[1], 'listeo-gallery'); ?>
                            <a href="<?php echo $image1[0]; ?>" class="slg-gallery-img"><img src="<?php echo $image1[0]; ?>" /></a>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
        </div>

  