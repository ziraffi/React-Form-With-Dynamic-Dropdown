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

use CustomWidget\ElementorWidgets\Widgets\Line_Drawer_Controls;
use CustomWidget\ElementorWidgets\Widgets\Darkmode_Widget;
use Elementor\Plugin as ElementorPlugin;

if (!defined('ABSPATH')) {
    exit;
}

final class CustomWidgetPlugin
{
    const VERSION = '1.0.0';
    const ELEMENTOR_MINIMUM_VERSION = '3.0.0';
    const PHP_MINIMUM_VERSION = '7.0.0';

    private static $_instance = null;
    private function define_constants()
    {
        define('CUSTOM_WIDGETS_VERSION', self::VERSION);
        define('MY_PLUGIN_ROOT', plugin_dir_path(__FILE__));
        define('CUSTOM_WIDGETS_URL', plugin_dir_url(__FILE__));
    }

    private function init_hooks()
    {
        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
        add_action('elementor/elements/categories_registered', [$this, 'create_new_category'], 10, 1);
        add_action('elementor/widgets/register', [$this, 'init_widgets']);

    }

    public function __construct()
    {
        $this->init_hooks();
        $this->define_constants();

        add_action('wp_enqueue_scripts', [$this, 'register_widget_styles']);

        // Dark MOde Hooks Start
        add_action('wp_enqueue_scripts', [$this, 'darkmode_widget_scripts']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'darkmode_editor_scripts']);

        add_action('wp_ajax_handle_toggle_dark_mode', [$this, 'handle_toggle_dark_mode']);
        add_action('wp_ajax_nopriv_handle_toggle_dark_mode', [$this, 'handle_toggle_dark_mode']);
        // Dark MOde Hooks End

        // add_action('wp_enqueue_scripts', [$this, 'enqueue_Line_draw_script']); 
        // React add action hook for Cripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_react_scripts']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'enqueue_react_scripts']);


    }
    
    function enqueue_react_scripts() {
        // Enqueue React and ReactDOM from a CDN
        wp_enqueue_script('react', 'https://unpkg.com/react@17/umd/react.production.min.js', [], '17.0.0', true);
        wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@17/umd/react-dom.production.min.js', ['react'], '17.0.0', true);
    
        // Enqueue your bundled React application
        $bundle_path = CUSTOM_WIDGETS_URL . 'dist/bundle.js';
        $bundle_version = filemtime(plugin_dir_path(__FILE__) . 'dist/bundle.js');
        wp_enqueue_script('react-form-script', $bundle_path, ['wp-element'], $bundle_version, true);
    
        // Enqueue your CSS
        wp_enqueue_style('react-form-style', CUSTOM_WIDGETS_URL . 'assets/css/App.css', [], filemtime(plugin_dir_path(__FILE__) . 'assets/css/App.css'));
    
        // Localize the script with necessary data
        wp_localize_script('react-form-script', 'reactFormData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('react_form_nonce'),
        ]);
    
        // Enqueue initialization script if it exists
        $init_script_path = CUSTOM_WIDGETS_URL . 'assets/js/react-form-init.js';
        $init_script_full_path = plugin_dir_path(__FILE__) . 'assets/js/react-form-init.js';
    
        if (file_exists($init_script_full_path)) {
            wp_enqueue_script('react-form-init', $init_script_path, ['react-form-script'], filemtime($init_script_full_path), true);
        } else {
            error_log("React Form Init script not found at: " . $init_script_full_path);
        }
        // enque JSON data to the frontend
        wp_enqueue_script('indian-pincodes-data', CUSTOM_WIDGETS_URL . 'widgets/formReact/src/assets/JSON/IndianPincodesData.json', [], '1.0', true);

        // Localize the script with necessary data
        wp_localize_script('react-form-script', 'reactFormData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('react_form_nonce'),
            'jsonUrl' => CUSTOM_WIDGETS_URL . 'widgets/formReact/src/assets/JSON/IndianPincodesData.json'
        ]);
    }

    // checking react-form-script real path 

    public function register_widget_styles()
    {
        wp_enqueue_style('bootstrap-css', CUSTOM_WIDGETS_URL . 'assets/css/bootstrap.css');
        wp_enqueue_style('custom-style', CUSTOM_WIDGETS_URL . 'assets/css/style.css');
    }

    // public function darkmode_widget_scripts() {
    //     wp_enqueue_style('dark-mode-style', CUSTOM_WIDGETS_URL . 'assets/css/darkmode.css'); 
    //     wp_enqueue_script('dark-mode-script', CUSTOM_WIDGETS_URL . 'assets/js/darkmode.js', ['jquery'], null, true);

    //     // Localize the script to pass the nonce and ajaxurl
    //     // wp_localize_script('dark-mode-script', 'darkModeData', [
    //     //     'ajaxurl' => admin_url('admin-ajax.php'),
    //     //     'nonce' => wp_create_nonce('toggle_dark_mode_nonce'), 
    //     // ]);
    // }

    // function handle_toggle_dark_mode() {
    //     // Log the incoming data for debugging
    //     error_log(print_r($_POST, true));

    //     // Check nonce
    //     if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'toggle_dark_mode_nonce')) {
    //         error_log('Nonce verification failed');
    //         wp_send_json_error(array('message' => 'Invalid nonce'), 403);
    //         return;
    //     }

    //     // Get dark mode state
    //     $is_dark_mode = isset($_POST['dark_mode']) && $_POST['dark_mode'] === 'true';

    //     // Save the preference in user meta or session (if needed)

    //     // Send a successful response
    //     wp_send_json_success(array('dark_mode' => $is_dark_mode));
    // }
    public function handle_toggle_dark_mode()
    {
        error_log(print_r($_POST['nonce'], true));
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'toggle_dark_mode_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce'], 403);
            return;
        }

        $is_dark_mode = isset($_POST['dark_mode']) && filter_var(sanitize_text_field($_POST['dark_mode']), FILTER_VALIDATE_BOOLEAN);
        wp_send_json_success(['dark_mode' => $is_dark_mode]);
    }
    public function darkmode_widget_scripts()
    {
        // Register the dark mode CSS for the frontend
        wp_register_style('dark-mode-style', CUSTOM_WIDGETS_URL . 'assets/css/darkmode.css');

        // Register the dark mode JavaScript for the frontend
        wp_register_script('dark-mode-script', CUSTOM_WIDGETS_URL . 'assets/js/darkmode.js', ['jquery'], null, true);

        // Localize script to provide data to the JavaScript file
        wp_localize_script('dark-mode-script', 'darkModeData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('toggle_dark_mode_nonce'),
        ]);
    }

    public function darkmode_editor_scripts()
    {
        // Enqueue additional styles or scripts for the Elementor editor if needed
        wp_enqueue_script(
            'dark-mode-editor-script',
            CUSTOM_WIDGETS_URL . 'assets/js/elementor-editor.js',
            ['jquery'],
            CUSTOM_WIDGETS_VERSION,
            true
        );
    }
    public function i18n()
    {
        load_plugin_textdomain('custom-widget-plugin');
    }

    public function init_plugin()
    {
        // Plugin initialization logic can be added here if needed
    }

    public function init_widgets()
    {
        $darkmode_widget_path = MY_PLUGIN_ROOT . 'widgets/darkmode/darkmode.php';
        if (file_exists($darkmode_widget_path)) {
            require_once $darkmode_widget_path;
            if (class_exists('\CustomWidget\ElementorWidgets\Widgets\Darkmode_Widget')) {
                ElementorPlugin::instance()->widgets_manager->register_widget_type(new Darkmode_Widget());
            } else {
                error_log('Darkmode_Widget class not found after including darkmode.php');
            }
        } else {
            error_log(message: 'darkmode.php not found at: ' . $darkmode_widget_path);
        }

        $react_form_widget_path = MY_PLUGIN_ROOT . 'widgets/formReact/react-form-widget.php';
        if (file_exists($react_form_widget_path)) {
            require_once $react_form_widget_path;
            if (class_exists('\CustomWidget\ElementorWidgets\Widgets\React_Form_Widget')) {
                ElementorPlugin::instance()->widgets_manager->register(new \CustomWidget\ElementorWidgets\Widgets\React_Form_Widget());
            } else {
                error_log('React_Form_Widget class not found after including react-form-widget.php');
            }
        } else {
            error_log('react-form-widget.php not found at: ' . $react_form_widget_path);
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
