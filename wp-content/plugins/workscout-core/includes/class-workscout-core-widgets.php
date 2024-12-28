<?php

if (!defined('ABSPATH')) exit;

/**
 * Hireo Core Widget base
 */
class WorkScout_Core_Widgets extends WP_Widget
{
    /**
     * Widget CSS class
     *
     * @access public
     * @var string
     */
    public $widget_cssclass;

    /**
     * Widget description
     *
     * @access public
     * @var string
     */
    public $widget_description;

    /**
     * Widget id
     *
     * @access public
     * @var string
     */
    public $widget_id;

    /**
     * Widget name
     *
     * @access public
     * @var string
     */
    public $widget_name;

    /**
     * Widget settings
     *
     * @access public
     * @var array
     */
    public $settings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->register();
    }


    /**
     * Register Widget
     */
    public function register()
    {
        $widget_ops = array(
            'classname'   => $this->widget_cssclass,
            'description' => $this->widget_description
        );

        parent::__construct($this->widget_id, $this->widget_name, $widget_ops);

        add_action('save_post', array($this, 'flush_widget_cache'));
        add_action('deleted_post', array($this, 'flush_widget_cache'));
        add_action('switch_theme', array($this, 'flush_widget_cache'));
    }



    /**
     * get_cached_widget function.
     */
    public function get_cached_widget($args)
    {

        return false;

        $cache = wp_cache_get($this->widget_id, 'widget');

        if (!is_array($cache))
            $cache = array();

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return true;
        }

        return false;
    }

    /**
     * Cache the widget
     */
    public function cache_widget($args, $content)
    {
        $cache[$args['widget_id']] = $content;

        wp_cache_set($this->widget_id, $cache, 'widget');
    }

    /**
     * Flush the cache
     * @return [type]
     */
    public function flush_widget_cache()
    {
        wp_cache_delete($this->widget_id, 'widget');
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        if (!$this->settings)
            return $instance;

        foreach ($this->settings as $key => $setting) {
            $instance[$key] = sanitize_text_field($new_instance[$key]);
        }

        $this->flush_widget_cache();

        return $instance;
    }

    /**
     * form function.
     *
     * @see WP_Widget->form
     * @access public
     * @param array $instance
     * @return void
     */
    function form($instance)
    {

        if (!$this->settings)
            return;

        foreach ($this->settings as $key => $setting) {

            $value = isset($instance[$key]) ? $instance[$key] : $setting['std'];

            switch ($setting['type']) {
                case 'text':
?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo $this->get_field_name($key); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
                    </p>
                <?php
                    break;
                case 'checkbox':
                ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo $this->get_field_name($key); ?>" type="checkbox" <?php checked(esc_attr($value), 'on'); ?> />
                    </p>
                <?php
                    break;
                case 'number':
                ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo $this->get_field_name($key); ?>" type="number" step="<?php echo esc_attr($setting['step']); ?>" min="<?php echo esc_attr($setting['min']); ?>" max="<?php echo esc_attr($setting['max']); ?>" value="<?php echo esc_attr($value); ?>" />
                    </p>
                <?php
                    break;
                case 'dropdown':
                ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <select class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo $this->get_field_name($key); ?>">

                            <?php foreach ($setting['options'] as $key => $option_value) { ?>
                                <option <?php selected($value, $key); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($option_value); ?></option>
                            <?php } ?>
                        </select>

                    </p>
        <?php
                    break;
            }
        }
    }

    /**
     * widget function.
     *
     * @see    WP_Widget
     * @access public
     *
     * @param array $args
     * @param array $instance
     *
     * @return void
     */
    public function widget($args, $instance)
    {
    }
}

/**
 * Save & Print listings Widget
 */
class WorkScout_Core_Bookmarks_Share_Widget extends WorkScout_Core_Widgets
{

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wp_post_types;

        $this->widget_cssclass    = 'workscout_core sidebar-widget widget_buttons';
        $this->widget_description = __('Display a Bookmarks and share buttons.', 'hireo_core');
        $this->widget_id          = 'widget_bookmarks_share';
        $this->widget_name        =  __('WorkScout Bookmarks & Share', 'hireo_core');
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => __('Bookmarks & Share ', 'workscout_core'),
                'label' => __('Title', 'workscout_core')
            ),
            'bookmarks' => array(
                'type'  => 'checkbox',
                'std'    => 'on',
                'label' => __('Bookmark button', 'hireo_core')
            ),
            'share' => array(
                'type'  => 'checkbox',
                'std'    => 'on',
                'label' => __('Share buttons', 'hireo_core')
            ),

        );
        $this->register();
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget($args, $instance)
    {
        // if ($this->get_cached_widget($args)) {
        //     return;
        // }

        ob_start();

        extract($args);
        $title  = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
        global $post;
        $share = (isset($instance['share'])) ? $instance['share'] : '';
        $bookmarks = (isset($instance['bookmarks'])) ? $instance['bookmarks'] : '';

        echo $before_widget;

        if ($title) echo $before_title . $title . $after_title;
        if($bookmarks == 'on'){

        
        do_action('workscout_bookmark_hook');
        } ?>
        <!-- Bookmark Button -->


                    <!-- Copy URL -->
                    <div class="copy-url">
                        <input id="copy-url" type="text" value="" class="with-border">
                        <button class="copy-url-button ripple-effect" data-clipboard-target="#copy-url" title="<?php esc_html_e('Copy to Clipboard', 'hireo_core') ?>" data-tippy-placement="top"><i class="icon-material-outline-file-copy"></i></button>
                    </div>
                    <?php if (!empty($share)) :
                        $id = $post->ID;
                        $title = urlencode($post->post_title);
                        $url =  urlencode(get_permalink($id));
                        $summary = urlencode(workscout_string_limit_words($post->post_excerpt, 20));
                        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'medium');
                        if ($thumb) {
                            $imageurl = urlencode($thumb[0]);
                        } else {
                            $imageurl = false;
                        }
                    ?>
                        <!-- Share Buttons -->
                        <div class="share-buttons margin-top-25">
                            <div class="share-buttons-trigger"><i class="icon-feather-share-2"></i></div>
                            <div class="share-buttons-content">
                                <span><?php esc_html_e('Interesting?', 'workscout_core') ?> <strong><?php esc_html_e('Share It!', 'workscout_core') ?></strong></span>
                                <ul class="share-buttons-icons">
                                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo ($url); ?>" data-button-color="#3b5998" title="<?php esc_html_e('Share on Facebook', 'hireo_core') ?>" data-tippy-placement="top"><i class="icon-brand-facebook-f"></i></a></li>
                                    <li><a <?php echo ' href="https://twitter.com/share?url=' . $url . '&amp;text=' . esc_attr($summary) . '"'; ?> data-button-color="#1da1f2" title="<?php esc_html_e('Share on Twitter', 'hireo_core') ?>" data-tippy-placement="top"><i class="icon-brand-twitter"></i></a></li>
                                    <li><a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo ($url); ?>" data-button-color="#0077b5" title="<?php esc_html_e('Share on LinkedIn', 'hireo_core') ?>" data-tippy-placement="top"><i class="icon-brand-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>







            <?php
            echo $after_widget;

            $content = ob_get_clean();

            echo $content;

            //   $this->cache_widget($args, $content);
        }
    }

    register_widget('WorkScout_Core_Bookmarks_Share_Widget');
