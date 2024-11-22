<?php
namespace CustomWidget\ElementorWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Darkmode_Widget extends Widget_Base {

    public function get_style_depends() {
        return ['dark-mode-style']; // Ensure this style handle is registered
    }

    public function get_script_depends() {
        return ['dark-mode-script']; // Ensure this script handle is registered
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
