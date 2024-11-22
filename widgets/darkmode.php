<?php
namespace CustomWidget\ElementorWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Darkmode_Widget extends Widget_Base {
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
        // No need to call parent::__construct() since Widget_Base does not require it.
        // Enqueue scripts and styles for the frontend
        add_action('wp_enqueue_scripts', [$this, 'darkmode_widget_scripts']);

        // Enqueue scripts and styles for the Elementor editor
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'darkmode_editor_scripts']);

        // Handle AJAX actions for toggling dark mode
        add_action('wp_ajax_handle_toggle_dark_mode', [$this, 'handle_toggle_dark_mode']);
        add_action('wp_ajax_nopriv_handle_toggle_dark_mode', [$this, 'handle_toggle_dark_mode']);
    }

    public function darkmode_widget_scripts() {
        error_log("TOuched here");
        // Enqueue the dark mode CSS for the frontend
        wp_enqueue_style('dark-mode-style', CUSTOM_WIDGETS_URL . 'assets/css/darkmode.css');

        // Enqueue the dark mode JavaScript for the frontend
        wp_enqueue_script('dark-mode-script', CUSTOM_WIDGETS_URL . 'assets/js/darkmode.js', ['jquery'], null, true);

        // Localize script to provide data to the JavaScript file
        wp_localize_script('dark-mode-script', 'darkModeData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('toggle_dark_mode_nonce'),
        ]);
    }

    public function darkmode_editor_scripts() {
        // Enqueue additional styles or scripts for the Elementor editor if needed
        wp_enqueue_script(
            'dark-mode-editor-script',
            CUSTOM_WIDGETS_URL . 'assets/js/elementor-editor.js',
            ['jquery'],
            CUSTOM_WIDGETS_VERSION,
            true
        );
    }

    public function handle_toggle_dark_mode() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'toggle_dark_mode_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce'], 403);
            return;
        }

        $is_dark_mode = isset($_POST['dark_mode']) && filter_var(sanitize_text_field($_POST['dark_mode']), FILTER_VALIDATE_BOOLEAN);
        wp_send_json_success(['dark_mode' => $is_dark_mode]);
    }

    public function get_name() {
        return 'dark_mode_switch';
    }

    public function get_title() {
        return __('Dark Mode Switch', 'custom-widget-plugin');
    }

    public function get_icon() {
        return 'eicon-toggle';
    }

    public function get_categories() {
        return ['customWidgets'];
    }

    protected function register_controls() { // Updated method name
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Settings', 'custom-widget-plugin'),
            ]
        );

        $this->add_control(
            'dark_mode',
            [
                'label' => __('Enable Dark Mode', 'custom-widget-plugin'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'custom-widget-plugin'),
                'label_off' => __('Off', 'custom-widget-plugin'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        try {
            $settings = $this->get_settings_for_display();
    
            // Ensure settings are an array
            if (!is_array($settings)) {
                error_log('Settings are not an array: ' . print_r($settings, true));
                $settings = [];
            }
    
            // Check if dark mode is enabled
            $dark_mode_enabled = !empty($settings['dark_mode']) && $settings['dark_mode'] === 'yes';
    
            // Output your dark mode switch here
            ?>
            <div class="parent-div">
                <section class="toggle-dark-mode-button" id="dark-mode-toggle" data-dark-mode="<?php echo esc_attr($dark_mode_enabled ? '1' : '0'); ?>">
                    <div class="round"></div>
                </section>
            </div>
            <?php
        } catch (Exception $e) {
            error_log('Error rendering dark mode widget: ' . $e->getMessage());
            // Handle the error gracefully, e.g., display a fallback message or log the error
        }
    }
}
