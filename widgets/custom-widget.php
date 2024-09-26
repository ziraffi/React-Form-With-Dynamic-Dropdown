<?php
namespace CustomWidget\ElementorWidgets\Widgets;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

if ( ! class_exists( 'Elementor\Controls_Manager' ) ) {
    return;
}

include_once plugin_dir_path(__FILE__) . '../includes/query-functions.php';
include_once plugin_dir_path(__FILE__) . '../includes/render-functions.php';
include_once plugin_dir_path(__FILE__) . '../includes/helper-functions.php';
include_once plugin_dir_path(__FILE__) . '../includes/multi-grid-controls.php';


class Multi_Grid extends \Elementor\Widget_Base {

    public function get_name() {
        return 'digital-product-grid';
    }

    public function get_title() {
        return __('Digital Product Grid', 'custom-widget-plugin');
    }

    public function get_icon() {
        return 'eicon-container-grid';
    }

    public function get_categories() {
        return ['customWidgets'];
    }

    protected function register_controls() {
        download_grid_register_controls($this);
    }            
    
    public function render($query = null) {
        $settings = $this->get_settings_for_display();
        $widget_id = 'digital-product-grid-' . $this->get_id(); // Generate a unique widget ID
    
        // Use the helper function to sanitize custom class
        $sanitized_class = isset($settings['custom_class']) ? sanitize_input($settings['custom_class']) : '';
        
        // Get button styles
        $button_styles = $this->get_button_styles($settings);
    
        // Prepare the query if not provided
        if ($query === null) {
            $query = create_download_query($settings); 
        }
    
        // Ensure the query is a valid WP_Query object
        if ($query instanceof \WP_Query) {
            // Rendering logic
            display_product_grid($query, $settings, $button_styles, $widget_id); // Correct the passing of $button_styles and $widget_id
        } else {
            echo __('No products found', 'custom-widget-plugin');
        }
    }
    
    
    
    // Button style utility function
    public function get_button_styles($settings) {
        return [
            'normal' => [
                'background_color' => isset($settings['button_background_color']) && !empty($settings['button_background_color']) 
                    ? $settings['button_background_color'] 
                    : '#000000',
                'color' => isset($settings['button_text_color']) && !empty($settings['button_text_color']) 
                    ? $settings['button_text_color'] 
                    : '#FFFFFF',
                'border' => isset($settings['button_border']) && !empty($settings['button_border']) 
                    ? $settings['button_border'] 
                    : '1px solid #000000',
            ],
            'hover' => [
                'background_color' => isset($settings['button_hover_background_color']) && !empty($settings['button_hover_background_color']) 
                    ? $settings['button_hover_background_color'] 
                    : '#555555',
                'color' => isset($settings['button_hover_text_color']) && !empty($settings['button_hover_text_color']) 
                    ? $settings['button_hover_text_color'] 
                    : '#FFFFFF',
                'border' => isset($settings['button_hover_border']) && !empty($settings['button_hover_border']) 
                    ? $settings['button_hover_border'] 
                    : '1px solid #555555',
            ],
            'active' => [
                'background_color' => isset($settings['button_active_background_color']) && !empty($settings['button_active_background_color']) 
                    ? $settings['button_active_background_color'] 
                    : '#333333',
                'color' => isset($settings['button_active_text_color']) && !empty($settings['button_active_text_color']) 
                    ? $settings['button_active_text_color'] 
                    : '#FFFFFF',
                'border' => isset($settings['button_active_border']) && !empty($settings['button_active_border']) 
                    ? $settings['button_active_border'] 
                    : '1px solid #333333',
            ],
            'padding' => isset($settings['button_padding']) && !empty($settings['button_padding']) 
                ? $settings['button_padding'] 
                : '10px 20px',
            'margin' => isset($settings['button_margin']) && !empty($settings['button_margin']) 
                ? $settings['button_margin'] 
                : '5px',
        ];
    }
    
    // Optional: Live editing content template (can be empty if not needed)
    protected function content_template() {
}
}