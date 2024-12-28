<!-- Listings -->
<?php
$ajax_browsing  = get_option('hireo_ajax_browsing');
$search_data = '';

if (isset($data)) :

    switch ($data->style) {
        case 'list':
            
            $list_class = '';
            break;
        case 'compact':
            
            $list_class = 'compact-list';
            break;

        case 'grid':
            
            $list_class = 'tasks-grid-layout';
            break;

        default:
            $template = '';
            $list_class = '';
            break;
    }
    $custom_class     = (isset($data->class)) ? $data->class : '';
    $in_rows         = (isset($data->in_rows)) ? $data->in_rows : '';
    $grid_columns    = (isset($data->grid_columns)) ? $data->grid_columns : '';
    $per_page        = (isset($data->per_page)) ? $data->per_page : get_option('hireo_listings_per_page', 10);
    $ajax_browsing  = (isset($data->ajax_browsing)) ? $data->ajax_browsing : get_option('hireo_ajax_browsing');

    if (isset($data->{'tax-region'})) {
        $search_data .= ' data-region="' . esc_attr($data->{'tax-region'}) . '" ';
    }

    if (isset($data->{'tax-listing_category'})) {
        $search_data .= ' data-category="' . esc_attr($data->{'tax-listing_category'}) . '" ';
    }

    if (isset($data->{'tax-listing_feature'})) {
        $search_data .= ' data-feature="' . esc_attr($data->{'tax-listing_feature'}) . '" ';
    }


endif;

?>
<div data-style="<?php if(isset($data->style)) echo $data->style; ?>" class="tasks-list-container <?php echo $list_class; ?> margin-top-35">
    <div class="loader-ajax-container">
        <div class="loader-ajax"></div>
    </div>