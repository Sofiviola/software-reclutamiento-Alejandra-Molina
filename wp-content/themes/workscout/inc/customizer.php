<?php

/**
 * WorkScout Theme Customizer.
 *
 * @package WorkScout
 */


Kirki::add_config('workscout', array(
    'capability'    => 'edit_theme_options',
    'option_type'   => 'option',
    'option_name'   => 'workscout',
    'disable_output'   => false,
));



/**
 * Customizer additions.
 */

require get_template_directory() . '/inc/customizer/header.php';
require get_template_directory() . '/inc/customizer/jobs.php';
require get_template_directory() . '/inc/customizer/resumes.php';
require get_template_directory() . '/inc/customizer/tasks.php';

if (get_option('workscout_homesearch_kirki_status') == 'show') {
    require get_template_directory() . '/inc/customizer/homesearch.php';
    require get_template_directory() . '/inc/customizer/contact.php';
}

require get_template_directory() . '/inc/customizer/dashboard.php';
require get_template_directory() . '/inc/customizer/maps.php';
require get_template_directory() . '/inc/customizer/colors.php';
require get_template_directory() . '/inc/customizer/layout.php';

require get_template_directory() . '/inc/customizer/blog.php';
require get_template_directory() . '/inc/customizer/shop.php';
require get_template_directory() . '/inc/customizer/footer.php';
require get_template_directory() . '/inc/customizer/typography.php';



require get_template_directory() . '/inc/customizer/title_tagline.php';
/*section blog*/


/*


Max Zoom In Level
Max Zoom Out Level*/

add_action('wp_head', 'workscout_stylesheet_content');


function workscout_generate_typo_css($typo)
{
    if ($typo) {
        $wpv_ot_default_fonts = array('arial', 'georgia', 'helvetica', 'palatino', 'tahoma', 'times', 'trebuchet', 'verdana');
        $ot_google_fonts = get_theme_mod('ot_google_fonts', array());
        foreach ($typo as  $key => $value) {
            if (isset($value) && !empty($value)) {
                if ($key == 'font-color') {
                    $key = "color";
                }
                if ($key == 'font-family') {
                    if (!in_array($value, $wpv_ot_default_fonts)) {
                        $value = $ot_google_fonts[$value]['family'];
                    }
                }
                echo $key . ":" . $value . ";";
            }
        }
    }
}

function workscout_generate_bg_css($typo)
{
    if ($typo) {
        foreach ($typo as  $key => $value) {
            if (isset($value) && !empty($value)) {
                if ($key == 'background-image') $value = "url('" . $value . "')";
                return esc_attr($key) . ":" . $value . ";";
            }
        }
    }
}




function workscout_stylesheet_content()
{

    $maincolor = Kirki::get_option('workscout', 'pp_main_color');
    $mapheight = Kirki::get_option('workscout', 'pp_map_height', '400px');
    $logo_height = Kirki::get_option('workscout', 'pp_retina_logo_height', 36);
    $logo_mobile_height = Kirki::get_option('workscout', 'pp_mobile_logo_height', 36);
    $logo_width = Kirki::get_option('workscout', 'pp_retina_logo_width');
    $markercolor =  Kirki::get_option('workscout', 'pp_maps_marker_color', '#808080');

    $footer_color = Kirki::get_option('workscout', 'workscout_footer_bg_color');
    $footer_text_color = Kirki::get_option('workscout', 'workscout_footer_text_color');
    $footer_border_color = Kirki::get_option('workscout', 'workscout_footer_border_color');

    $map_provider = get_option('workscout_map_provider');
    if ($map_provider == "none") : $mapheight = '20px';
    endif; ?>

    <style type="text/css">
        .old-header .current-menu-item>a,
        a.button.gray.app-link.opened,
        ul.float-right li a:hover,
        .old-header .menu ul li.sfHover a.sf-with-ul,
        .old-header .menu ul li a:hover,
        a.menu-trigger:hover,
        .old-header .current-menu-parent a,
        #jPanelMenu-menu li a:hover,
        .search-container button,
        .upload-btn,
        button,
        span.button,
        button.button,
        input[type="button"],
        input[type="submit"],
        a.button,
        .upload-btn:hover,
        #titlebar.photo-bg a.button.white:hover,
        a.button.dark:hover,
        #backtotop a:hover,
        .mfp-close:hover,
        .woocommerce-MyAccount-navigation li.is-active a,
        .woocommerce-MyAccount-navigation li.current-menu-item a,
        .tabs-nav li.active a,
        .tabs-nav-o li.active a,
        .accordion h3.active-acc,
        .highlight.color,
        .plan.color-2 .plan-price,
        .plan.color-2 a.button,
        .tp-leftarrow:hover,
        .tp-rightarrow:hover,
        .pagination ul li a.current-page,
        .woocommerce-pagination .current,
        .pagination li.current,
        .pagination li.current a,
        .pagination .current,
        .pagination ul li a:hover,
        .pagination-next-prev ul li a:hover,
        .infobox,
        .load_more_resumes,
        .job-manager-pagination .current,
        .hover-icon,
        .comment-by a.reply:hover,
        .chosen-container .chosen-results li.highlighted,
        .chosen-container-multi .chosen-choices li.search-choice,
        .list-search button,
        .checkboxes input[type=checkbox]:checked+label:before,
        .double-bounce1,
        .double-bounce2,
        .widget_range_filter .ui-state-default,
        .tagcloud a:hover,
        .filter_by_tag_cloud a.active,
        .filter_by_tag_cloud a:hover,
        #wp-calendar tbody td#today,
        .footer-widget .tagcloud a:hover,
        .nav-links a:hover,
        .icon-box.rounded i:after,
        #mapnav-buttons a:hover,
        .dashboard-list-box .button.gray:hover,
        .dashboard-list-box-static .button,
        .select2-container--default .select2-selection--multiple .select2-selection__choice,
        #footer-new .footer-widget.widget_nav_menu li a:before,
        .message-reply button,
        .account-type input.account-type-radio:checked~label,
        .mm-menu em.mm-counter,
        .enable-filters-button i,
        .enable-filters-button span,
        .slg-button:hover,
        .comment-by a.comment-reply-link:hover,
        #jPanelMenu-menu .current-menu-item>a,
        .button.color,
        .freelancer-indicators .indicator-bar span,
        .compact-list.freelancers-list-layout .freelancer:before,
        .pika-button:hover,
        .highlighted-category:hover,
        .pika-row.pick-whole-week:hover .pika-button,
        .tasks-list-container.compact-list .task-listing:before,
        .intro-search-button .button {
            background-color: <?php echo esc_attr($maincolor); ?>;
        }

        /* .pagination li.current,
        .pagination li.current a,
        .pagination .current {
            background-color: <?php echo esc_attr($maincolor); ?> !important;
        } */

        #backtotop a,
        .header-notifications-trigger span,
        a.header-notifications-button,
        #navigation ul ul.dropdown-nav ul.dropdown-nav li:hover a:after,
        #navigation ul ul.dropdown-nav li:hover a:after,
        .mm-menu em.mm-counter,
        .language-switcher.bootstrap-select.btn-group.open button,
        .language-switcher.bootstrap-select.btn-group button:hover,
        .footer-links ul li a span:before,
        .newsletter button,
        .pagination ul li a.current-page,
        a.blog-post-info:hover,
        #posts-nav li a:hover span,
        .comment-by a.reply:hover,
        .contact-address-headline:after,
        .enable-filters-button span,
        .enable-filters-button i,
        .job-listing.with-apply-button:hover .list-apply-button,
        .letters-list a.current,
        span.button,
        button.button,
        input[type="button"],
        input[type="submit"],
        a.button,
        .list-1 li:before,
        mark.color,
        table.basic-table th,
        .copy-url .copy-url-button,
        .keyword-input-container .keyword-input-button,
        .tags-container input[type="checkbox"]:checked+label,
        input:checked+.switch-button,
        .radio input[type="radio"]+label .radio-label:after,
        .uploadButton .uploadButton-button:hover,
        .pricing-plan .button:hover,
        .pricing-plan.recommended .button,
        .pricing-plan .recommended-badge,
        .payment-tab-trigger>input:checked~label::after,
        .breathing-icon,
        .icon-box-check,
        .testimonial-author span,
        .qtyInc:hover,
        .qtyDec:hover,
        #sign-in-dialog .mfp-close:hover,
        #small-dialog-1 .mfp-close:hover,
        #small-dialog-2 .mfp-close:hover,
        #small-dialog-3 .mfp-close:hover,
        #small-dialog-4 .mfp-close:hover,
        #small-dialog .mfp-close:hover,
        .slider-selection,
        .cluster-visible,
        .marker-container,
        .custom-zoom-in:hover,
        .custom-zoom-out:hover,
        #geoLocation:hover,
        #streetView:hover,
        #scrollEnabling:hover,
        #scrollEnabling.enabled,
        a.apply-now-button,
        a.attachment-box:hover,
        .freelancer-indicators .indicator-bar span,
        .dashboard-nav ul li span.nav-tag,
        .dashboard-box .button.dark:not(.ico):hover,
        .messages-inbox ul li:before,
        .message-by h5 i,
        .loader-ajax-container,
        .message-bubble.me .message-text {
            background-color: <?php echo esc_attr($maincolor); ?>;
        }

        .account-type input.account-type-radio~label:hover {
            color: <?php echo esc_attr($maincolor); ?>;
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.1);
        }

        body .button.send-message-to-owner {
            background-color: <?php echo esc_attr($maincolor); ?>21;
            color: <?php echo esc_attr($maincolor); ?>;
        }

        body .button.send-message-to-owner:hover {
            background-color: <?php echo esc_attr($maincolor); ?>2e;
            color: <?php echo esc_attr($maincolor); ?>
        }

        a,
        table td.title a:hover,
        table.manage-table td.action a:hover,
        #breadcrumbs ul li a:hover,
        #titlebar span.icons a:hover,
        .counter-box i,
        .counter,
        #popular-categories li a i,
        .single-resume .resume_description.styled-list ul li:before,
        .list-1 li:before,
        .dropcap,
        .resume-titlebar span a:hover i,
        .resume-spotlight h4,
        .resumes-content h4,
        .job-overview ul li i,
        .company-info span a:hover,
        .infobox a:hover,
        .meta-tags span a:hover,
        .widget-text h5 a:hover,
        .app-content .info span,
        .app-content .info ul li a:hover,
        table td.job_title a:hover,
        table.manage-table td.action a:hover,
        .job-spotlight span a:hover,
        .widget_rss li:before,
        .widget_rss li a:hover,
        .widget_categories li:before,
        .widget-out-title_categories li:before,
        .widget_archive li:before,
        .widget-out-title_archive li:before,
        .widget_recent_entries li:before,
        .widget-out-title_recent_entries li:before,
        .categories li:before,
        .widget_meta li:before,
        .widget_recent_comments li:before,
        .widget_nav_menu li:before,
        .widget_pages li:before,
        .widget_categories li a:hover,
        .widget-out-title_categories li a:hover,
        .widget_archive li a:hover,
        .widget-out-title_archive li a:hover,
        .widget_recent_entries li a:hover,
        .widget-out-title_recent_entries li a:hover,
        .categories li a:hover,
        .widget_meta li a:hover,
        #wp-calendar tbody td a,
        .widget_nav_menu li a:hover,
        .widget_pages li a:hover,
        .resume-title a:hover,
        .company-letters a:hover,
        .companies-overview li li a:hover,
        .icon-box.rounded i,
        .icon-box i,
        #titlebar .company-titlebar span a:hover,
        .adv-search-btn a,
        .new-category-box .category-box-icon,
        body .new-header #navigation>ul>li:hover>a,
        body .new-header #navigation>ul>li>a:hover,
        body .new-header #navigation>ul>li>a.current,
        body .new-header #navigation>ul>li:hover>a,
        body .new-header #navigation>ul>li>a:hover,
        .dashboard-nav ul li.active-submenu a,
        .dashboard-nav ul li:hover a,
        .dashboard-nav ul li.active a,
        .new-header .transparent-header #navigation>ul li:hover ul li:hover a:after,
        .new-header .transparent-header #navigation>ul li:hover a:after,
        .new-header .transparent-header #navigation>ul li a.current:after,
        .account-type input.account-type-radio~label:hover i,
        .dashboard-nav ul li.current-menu-item a,
        .transparent-header .login-register-buttons a:hover,
        .login-register-buttons a:hover,
        body .new-header #navigation>ul>li>a.current,
        .new-header #navigation ul li:hover a:after,
        .popup-tabs-nav li.active a,
        .tab-slider--trigger.active,
        .dashboard-box .headline h3 i,
        .new-header #navigation ul li a.current:after {
            color: <?php echo esc_attr($maincolor); ?>;
        }

        body .icon-box-2 svg g,
        body .icon-box-2 svg circle,
        body .icon-box-2 svg rect,
        body .icon-box-2 svg path,
        body .listeo-svg-icon-box-grid svg g,
        body .listeo-svg-icon-box-grid svg circle,
        body .listeo-svg-icon-box-grid svg rect,
        body .listeo-svg-icon-box-grid svg path,
        .icon-box i,
        .icon-box svg g,
        .icon-box svg circle,
        .icon-boxsvg rect,
        .icon-box svg path {
            fill: <?php echo esc_attr($maincolor); ?>;
        }

        body .woocommerce .cart .button,
        body .woocommerce .cart input.button,
        body .woocommerce #respond input#submit,
        body .woocommerce a.button,
        body .woocommerce button.button,
        body .woocommerce input.button,
        .boxed-wide .boxed-search-footer:after,
        .boxed-wide .boxed-search-right-side:after {
            background: <?php echo esc_attr($maincolor); ?>;
        }


        .dashboard-nav ul li.current-menu-item,
        .dashboard-nav ul li.active-submenu,
        .dashboard-nav ul li.active,
        .dashboard-nav ul li:hover,
        .icon-box.rounded i {
            border-color: <?php echo esc_attr($maincolor); ?>;
        }

        .job-overview ul li i {
            background: <?php echo esc_attr($maincolor); ?>1f;
        }

        .job-overview ul li i {
            background: <?php echo esc_attr($maincolor); ?>1f;
        }

        .small-tag {
            background-color: <?php echo esc_attr($maincolor); ?>;
        }

        .resumes li a:before,
        .resumes-list li a:before,
        .job-list li a:before,
        table.manage-table tr:before {
            -webkit-box-shadow: 0px 1px 0px 0px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.7);
            -moz-box-shadow: 0px 1px 0px 0px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.7);
            box-shadow: 0px 1px 0px 0px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.7);
        }

        #popular-categories li a:before {
            -webkit-box-shadow: 0px 0px 0px 1px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.7);
            -moz-box-shadow: 0px 0px 0px 1px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.7);
            box-shadow: 0px 0px 0px 1px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.7);
        }

        table.manage-table tr:hover td,
        .resumes li:hover,
        .job-list li:hover {
            border-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.7);
        }


        .recommended .pricing-plan-label {
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.06);
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .dashboard-nav ul li.current-menu-item,
        .dashboard-nav ul li.active-submenu,
        .dashboard-nav ul li.active,
        .dashboard-nav ul li:hover,
        table.manage-table tr:hover td,
        .resumes li:hover,
        .job-list li:hover,
        #popular-categories li a:hover {
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.05);
        }

        .tab-slider--tabs:after {
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.20);
        }

        .new-category-box:hover {
            background: <?php echo esc_attr($maincolor); ?>;
            box-shadow: 0 4px 12px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.35);
        }

        a.load_more_jobs.button,
        .button.send-message-to-owner,
        .resume-template-default .button.send-message-to-owner,
        .browse-all-cat-btn a {
            box-shadow: 0 4px 12px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.25);
        }

        @keyframes markerAnimation {

            0%,
            100% {
                box-shadow: 0 0 0 6px rgba(<?php echo workscout_hex2rgb($markercolor, true) ?>, 0.15);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(<?php echo workscout_hex2rgb($markercolor, true) ?>, 0.15);
            }
        }



        @keyframes clusterAnimation {

            0%,
            100% {
                box-shadow: 0 0 0 6px rgba(<?php echo workscout_hex2rgb($markercolor, true) ?>, 0.15);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(<?php echo workscout_hex2rgb($markercolor, true) ?>, 0.15);
            }
        }

        .marker-cluster-small div,
        .marker-cluster-medium div,
        .marker-cluster-large div,
        .marker-container,
        .cluster-visible {
            background-color: <?php echo esc_attr($markercolor); ?>;
        }

        .marker-cluster div:before {
            border: 7px solid <?php echo esc_attr($markercolor); ?>;
            box-shadow: inset 0 0 0 4px <?php echo esc_attr($markercolor); ?>;
        }

        body #dashboard table.manage-table tr:hover td {
            border-bottom: 1px solid <?php echo esc_attr($maincolor); ?>;
        }

        /* .select2-container--default .select2-results__option--highlighted[aria-selected], */
        .dashboard-nav ul li span.nav-tag,

        body .wp-subscribe-wrap input.submit,
        .adv-search-btn a:after,
        .panel-dropdown.active>a,
        body #dashboard table.manage-table tr td:before {
            background: <?php echo esc_attr($maincolor); ?>;
        }

        .mm-counter {
            background-color: <?php echo esc_attr($maincolor); ?>;
        }

        #titlebar .ajax-job-view-links span:hover a,
        .ajax-job-view-links span:hover a,
        .company-info-boxed-links span:hover a,
        .company-data__content--list._company_tagline span:hover {
            background: <?php echo esc_attr($maincolor); ?>1c;
        }

        body #titlebar .ajax-job-view-links span:hover a,
        body .ajax-job-view-links span:hover a,
        body .company-info-boxed-links span:hover a,
        .company-data__content--list._company_tagline span:hover a {
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .mas-wpjmc-search .widget.job-widget-regions input {

            background: <?php echo esc_attr($maincolor); ?>26;
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .mas-wpjmc-search .widget.job-widget-regions input:hover {
            background: <?php echo esc_attr($maincolor); ?>;

        }

        .resumes.alternative li:before,
        .category-small-box:hover {
            background-color: <?php echo esc_attr($maincolor); ?>;
        }

        .panel-dropdown>a:after,
        .intro-banner.boxed .adv-search-btn span,
        .category-small-box i {
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .old-header .transparent #logo img,
        #logo_nh img,
        .new-header #logo_nh img,
        #logo img {
            height: <?php echo $logo_height ?>px;

        }

        #ws-map,
        #search_map {
            height: <?php echo $mapheight; ?>;
        }

        .freelancers-list-layout .freelancer-details a.button:hover,
        .freelancers-grid-layout .freelancer-details a.button:hover {
            background-color: <?php echo esc_attr($maincolor); ?>;
        }

        .freelancers-list-layout .freelancer-details a.button,
        .freelancers-grid-layout .freelancer-details a.button,
        body:has(.jm-form) .container .select2-dropdown .select2-results__option--highlighted,
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.15);
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .task-listing-bid-inner .button {
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.17);
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .single-page-section .task-tags span {
            background-color: rgb(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.14);
        }

        .single-page-section .task-tags span a:hover {
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .share-buttons-content span strong,
        .dashboard-nav ul li.active-submenu a i,
        .dashboard-nav ul li.active a i,
        .dashboard-nav ul li:hover a i,
        .dashboard-nav ul:before {
            color: <?php echo esc_attr($maincolor); ?>;
        }


        #footer-new.custom-footer-colors .footer-new-row,
        #footer-new.custom-footer-colors {
            background-color: <?php echo esc_attr($footer_color); ?>;
            color: <?php echo esc_attr($footer_text_color); ?>;
        }

        #footer-new.custom-footer-colors .intro-stats li span,
        #footer-new.custom-footer-colors h4,
        #footer-new h3,
        #footer-new.custom-footer-colors h2,
        #footer-new.custom-footer-colors .footer-new-links h3,
        #footer-new.custom-footer-colors .intro-stats li strong {
            color: <?php echo esc_attr($footer_text_color); ?>;
        }

        #footer-new.custom-footer-colors .new-footer-social-icons li a,
        #footer-new.custom-footer-colors .footer-widget.widget_nav_menu li a:hover,
        #footer-new.custom-footer-colors .footer-widget.widget_nav_menu li a {
            color: <?php echo esc_attr($footer_text_color); ?> !important;
        }

        #footer-new.custom-footer-colors .footer-new-bottom-section,
        #footer-new.custom-footer-colors .footer-new-top-section,
        #footer-new.custom-footer-colors .footer-new-rows-right .footer-new-row {
            border-color: <?php echo esc_attr($footer_border_color); ?> !important
        }


        .dashboard-nav ul li.active-submenu a:after,
        .dashboard-nav ul li.active a:after,
        .dashboard-nav ul li:hover a:after {
            color: <?php echo esc_attr($maincolor); ?>;
            background: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.13);
        }

        .slider-handle {
            border: 2px solid <?php echo esc_attr($maincolor); ?>;

        }

        .slider-handle:after {
            box-shadow: 0 0 0px 6px rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.12);
        }

        .task-tags span {
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.07);
            color: <?php echo esc_attr($maincolor); ?>;
        }

        .boxed-list-headline h3 i {
            color: <?php echo esc_attr($maincolor); ?>;
        }


        .task-listing-bid-inner .button:hover {
            background-color: <?php echo esc_attr($maincolor); ?>;
            color: #fff;
        }

        body .job_description ul li::before,
        body .job_description ol li::before,
        .single-page-section ul li::before,
        .single-page-section ol li::before {
            background-color: rgba(<?php echo workscout_hex2rgb($maincolor, true) ?>, 0.15);
            color: <?php echo esc_attr($maincolor); ?>;
        }

        @media (max-width: 1099px) {
            .new-header #logo_nh img {
                max-height: <?php echo $logo_mobile_height ?>px !important;
                width: 100%;
            }
        }

        <?php $ordering = Kirki::get_option('workscout', 'pp_shop_ordering');
        if ($ordering) { ?>.woocommerce-ordering {
            display: none;
        }

        .woocommerce-result-count {
            display: none;
        }

        <?php } ?><?php
                    $rss = Kirki::get_option('workscout', 'pp_disable_rss', false);
                    if ($rss) { ?>.job_filters a.rss_link {
            display: none;
        }

        <?php } ?><?php
                    $breakpoint = Kirki::get_option('workscout', 'pp_alt_menu_width', false);
                    if ($breakpoint) { ?>@media (max-width: <?php echo $breakpoint; ?>px) {
            .sticky-header.cloned {
                display: none;
            }

            #titlebar.photo-bg.with-transparent-header.single {
                padding-top: 200px !important;
            }
        }

        <?php } ?><?php
                    $woo_nav = Kirki::get_option('workscout', 'pp_hide_woo_nav', array());
                    if (is_array($woo_nav)) {
                        $woo_output = '';

                        if (in_array('dashboard', $woo_nav)) {
                            $woo_output .= '
            .woocommerce-MyAccount-navigation-link--dashboard { display: none; }
        ';
                        }
                        if (in_array('orders', $woo_nav)) {
                            $woo_output .= '
            .woocommerce-MyAccount-navigation-link--orders { display: none; }
        ';
                        }
                        if (in_array('downloads', $woo_nav)) {
                            $woo_output .= '
            .woocommerce-MyAccount-navigation-link--downloads { display: none; }
        ';
                        }
                        if (in_array('addresses', $woo_nav)) {
                            $woo_output .= '
            .woocommerce-MyAccount-navigation-link--edit-address { display: none; }
        ';
                        }
                        if (in_array('account_details', $woo_nav)) {
                            $woo_output .= '
            .woocommerce-MyAccount-navigation-link--edit-account { display: none; }
        ';
                        }
                        if (in_array('logout', $woo_nav)) {
                            $woo_output .= '
            .woocommerce-MyAccount-navigation-link--customer-logout { display: none; }
        ';
                        }
                        echo $woo_output;
                    }
                    ?>
    </style>

<?php }



/**
 * Convert a hexa decimal color code to its RGB equivalent
 *
 * @param string $hexStr (hexadecimal color value)
 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
 */
function workscout_hex2rgb($hexStr, $returnAsString = false, $seperator = ',')
{
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}
