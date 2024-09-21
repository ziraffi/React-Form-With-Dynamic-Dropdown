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
use CustomWidget\ElementorWidgets\Widgets\Multi_Grid; // Correct namespace import
use Elementor\Plugin as ElementorPlugin;
use Elementor\Widgets_Manager as ElementorWidgetsManager;

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
        add_shortcode('filter_category', [$this, 'filter_category_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_ajax_filter_script']);
        add_action('wp_ajax_filter_products', [$this, 'filter_products_callback']);
        add_action('wp_ajax_nopriv_filter_products', [$this, 'filter_products_callback']);
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
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
    

    function filter_products_callback() {
        if (isset($_POST['category_id'])) {
            $category_id = intval($_POST['category_id']);
            error_log('Selected category ID: ' . $category_id);
        } else {
            error_log('Category ID not set.');
            wp_send_json_error('Category ID not set');
        }
    
        // Ensure widget class exists and include necessary files
        include_once plugin_dir_path(__FILE__) . 'widgets/custom-widget.php';
    
        if (!class_exists('\CustomWidget\ElementorWidgets\Widgets\Multi_Grid')) {
            error_log('Multi_Grid class not found');
            wp_send_json_error('Multi_Grid class not found');
        }
    
        // Initialize the widget instance and set the category ID
        $widget_instance = new \CustomWidget\ElementorWidgets\Widgets\Multi_Grid();

        // If category_id is 0, retrieve all downloads
        if ($category_id == 0) {
            $widget_instance->set_category_id(null); // Fetch all downloads if no specific category
        } else {
            $widget_instance->set_category_id($category_id);
        }    

        ob_start();

        $widget_instance->render(); 

        $output = ob_get_clean();
    
        if (empty($output)) {
            error_log('No output generated by widget render');
            wp_send_json_error('No output generated');
        }
    
        wp_send_json_success($output);
    }
    
      

    function filter_category_shortcode() {
        $terms = get_terms([
            'taxonomy' => 'download_category',
            'hide_empty' => true,
        ]);
    
        ob_start();
        ?>
        <div>Choose The Category</div><br>
        <select id="filter-category">
            <option value="">Select a category</option>
            <?php foreach ($terms as $term) : ?>
                <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
        return ob_get_clean();
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

