<?php
/**
 * Plugin Name: Custom Widgets
 * Version: 0.0.1
 * License: GPLv2 or later
 * Author: Mr. Rajkiran
 * Author URI: #
 * Description: Hand-made Custom Plugin for Multipurpose Like Adding widgets, Functionalities
 * Text Domain: custom-widget-plugin
 */

namespace CustomWidget\ElementorWidgets;
use CustomWidget\ElementorWidgets\Widgets\Multi_Grid; 
use Elementor\Plugin as ElementorPlugin;
use Elementor\Widgets_Manager as ElementorWidgetsManager;
use WP_Query;

require_once plugin_dir_path(__FILE__) . 'includes/helper-functions.php';


if (!defined('ABSPATH')) {
    exit;
}
error_log('Custom Widget Path: ' . plugin_dir_path(__FILE__) . 'widgets/custom-widget.php');

final class CustomWidgetPlugin
{
    const VERSION = '0.0.1';
    const ELEMENTOR_MINIMUM_VERSION = '3.0.0';
    const PHP_MINIMUM_VERSION = '7.0.0'; 

    private static $_instance = null;

    public function __construct()
    {
        define('MY_PLUGIN_ROOT', plugin_dir_path(__FILE__));

        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init_plugin']); 
        add_action('wp_enqueue_scripts', [$this, 'register_widget_styles']); 
        add_action('elementor/elements/categories_registered', [$this, 'create_new_category'], 5,1);
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
        add_shortcode('download_filter_form', [$this, 'filter_form_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_ajax_filter_script']);

        // add_action('wp_ajax_filter_products', [$this, 'filter_products_callback']);
        // add_action('wp_ajax_nopriv_filter_products', [$this, 'filter_products_callback']);

        add_action('wp_ajax_filter_downloads', [$this, 'filter_downloads']);
        add_action('wp_ajax_nopriv_filter_downloads', [$this, 'filter_downloads']);

        add_action('wp_ajax_fetch_tag_suggestions', [$this, 'fetch_tag_suggestions']);
        add_action('wp_ajax_nopriv_fetch_tag_suggestions', [$this, 'fetch_tag_suggestions']);
    }

    public function enqueue_ajax_filter_script() {
        wp_enqueue_script(
            'ajax-filter',
            plugin_dir_url( __FILE__ ) . 'assets/js/ajax-filter.js',
            array('jquery'),
            null,
            true
        );
    
        wp_localize_script('ajax-filter', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'pageId' => get_the_ID(),
        ));
        
    }


    function filter_form_shortcode() {
        $categories = get_terms([
            'taxonomy' => 'download_category', 
            'hide_empty' => true,
        ]);
    
        ob_start();
        ?>
        <form id="download-filter-form" data-widget-id="">
            <fieldset>
                <legend>Filter by Category</legend>
                <div>
                    <?php foreach ($categories as $category) : ?>
                        <label>
                            <input type="checkbox" name="category[]" value="<?php echo esc_attr($category->term_id); ?>">
                            <?php echo esc_html($category->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
    
            <fieldset>
                <legend>Filter by Tags</legend>
                <input type="text" id="filter-tags" name="tags" placeholder="Enter tags" autocomplete="off">
                <div id="tags-suggestions"></div>
                <ul id="selected-tags"></ul>
            </fieldset>
    
            <button type="submit">Filter</button>
        </form>
        <?php
        return ob_get_clean();
    }    


    function fetch_tag_suggestions() {
        $term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
        $exclude_tags = isset($_POST['selected_tags']) ? array_map('sanitize_text_field', $_POST['selected_tags']) : [];
    
        if (!empty($term)) {
            $tags = get_terms([
                'taxonomy' => 'download_tag', // Replace with your actual tag taxonomy
                'hide_empty' => false,
                'name__like' => $term,
                // Since `exclude` is not available directly, we will filter after fetching
            ]);
    
            if (!is_wp_error($tags) && !empty($tags)) {
                ob_start();
                foreach ($tags as $tag) {
                    if (!in_array($tag->name, $exclude_tags)) { // Exclude selected tags
                        echo '<div class="tag-suggestion">' . esc_html($tag->name) . '</div>';
                    }
                }
                $suggestions_html = ob_get_clean();
    
                if (empty($suggestions_html)) {
                    $suggestions_html = '<div class="no-suggestions">No tags found</div>';
                }
    
                wp_send_json_success($suggestions_html);
            } else {
                wp_send_json_success('<p>No tags found</p>');
            }
        }
        wp_die();
    }
    
    public function get_widget_settings($widget_id, $page_Id) {
        if (empty($widget_id)) {
            error_log('Widget ID is missing.');
            return [];
        }
    
        // Force Elementor to load if it hasn't been
        \Elementor\Plugin::$instance->frontend->init();
        
        // Get the Elementor document for the current page
        $document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend($page_Id);
        error_log('Value of $page_Id at get_widget_settings: ' . $page_Id);
        if (!$document) {
            error_log('Document not found for this page.');
            return [];
        }
    
        // Get all widgets data for this document
        $elements_data = $document->get_elements_data();
    
        // Search for widget settings by widget ID
        $settings = find_widget_settings($elements_data, $widget_id); // Or use the namespace if required
    
        // Log the retrieved settings for debugging
        error_log('Settings retrieved: ' . print_r($settings, true));
    
        return $settings;
    }
    
    
    
    
    public function filter_downloads() {
        
        // Get categories and tags from the POST payload
        $category_ids = isset($_POST['category']) ? array_map('intval', $_POST['category']) : [];
        $tags = isset($_POST['tags']) ? array_map('sanitize_text_field', $_POST['tags']) : [];
        $widget_id = isset($_POST['widget_id']) ? sanitize_text_field($_POST['widget_id']) : '';
        $page_Id = isset($_POST['page_id']) ? sanitize_text_field($_POST['page_id']) : '';
        error_log('Widget ID in server: ' . $widget_id);
    
        // Log the request payload
        error_log('Categories: ' . print_r($category_ids, true));
        error_log('Tags: ' . print_r($tags, true));
    
        // Retrieve the widget settings
        $settings = $this->get_widget_settings($widget_id, $page_Id);
        
        error_log('widget ID at filter_downloads: ' . $widget_id);

    
        // Prepare the query
        $args = [
            'post_type' => 'download',
            'posts_per_page' => -1,
            'tax_query' => [
                'relation' => 'AND',
            ],
        ];
    
        // Filter by category
        if (!empty($category_ids)) {
            $args['tax_query'][] = [
                'taxonomy' => 'download_category',
                'field' => 'term_id',
                'terms' => $category_ids,
            ];
        }
    
        // Filter by tags
        if (!empty($tags)) {
            $tag_ids = [];
            foreach ($tags as $tag_name) {
                $tag = get_term_by('name', trim($tag_name), 'download_tag');
                if ($tag) {
                    $tag_ids[] = $tag->term_id;
                }
            }
    
            if (!empty($tag_ids)) {
                $args['tax_query'][] = [
                    'taxonomy' => 'download_tag',
                    'field' => 'term_id',
                    'terms' => $tag_ids,
                ];
            }
        }
    
        // Execute the query
        $query = new WP_Query($args);
        error_log('Number of Downloads Found: ' . $query->found_posts);
    
        if ($query->have_posts()) {
            ob_start();
            include_once plugin_dir_path(__FILE__) . 'widgets/custom-widget.php';
    
            if (!class_exists('\CustomWidget\ElementorWidgets\Widgets\Multi_Grid')) {
                error_log('Multi_Grid class not found');
                wp_send_json_error('Multi_Grid class not found');
            }
    
            // Initialize the Multi_Grid widget instance
            $widget_instance = new \CustomWidget\ElementorWidgets\Widgets\Multi_Grid();
            $widget_instance->set_settings($settings); 
            $widget_instance->render($query);
            $output = ob_get_clean();
            wp_send_json_success(['output' => $output, 'widget_id' => $widget_id]);
        } else {
            wp_send_json_error(__('No downloads found', 'custom-widget-plugin'));
        }
    
        wp_die();
    }
    
    
    public function register_widget_styles() {
        // Correct path using plugin_dir_url or plugins_url
        wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap.css' ); 
        wp_enqueue_style( 'custom-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' ); 
    }

    public function i18n()
    {
        load_plugin_textdomain('custom-widget-plugin');
    }

    public function init_plugin()
    {
        // Plugin initialization logic
    }

    public function init_widgets() {
        $custom_widget_path = plugin_dir_path(__FILE__) . 'widgets/custom-widget.php';
        error_log('Attempting to include: ' . $custom_widget_path);
    
        if (file_exists($custom_widget_path)) {
            include_once $custom_widget_path;
            
            if (class_exists('\CustomWidget\ElementorWidgets\Widgets\Multi_Grid')) {
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \CustomWidget\ElementorWidgets\Widgets\Multi_Grid());
                error_log('Multi_Grid class successfully registered.');
            } else {
                error_log('Multi_Grid class not found after including custom-widget.php');
            }
        } else {
            error_log('custom-widget.php not found at: ' . $custom_widget_path);
        }
    }
    
    

    public static function get_instance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function create_new_category($elements_manager)
    {
        $elements_manager->add_category(
            'customWidgets',
            [
                'title' => __('AWidgets', 'custom-widget-plugin'),
                'icon' => 'eicon-container-grid',
            ]
        );
    }
}

CustomWidgetPlugin::get_instance();

