<?php

namespace CustomWidget\ElementorWidgets\Widgets;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Elementor\Controls_Manager')) {
    return;
}

// Include necessary files
include_once plugin_dir_path(__FILE__) . '../includes/query-functions.php';
include_once plugin_dir_path(__FILE__) . '../includes/render-functions.php';
include_once plugin_dir_path(__FILE__) . '../includes/helper-functions.php';
include_once plugin_dir_path(__FILE__) . '../includes/multi-grid-controls.php';

class Multi_Grid extends Widget_Base {

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

    public function render($query = null, $widget_data_id = null, $category_ids = null, $tags = null) {
        // Check if ID exists, if not regenerate or assign a fallback
        $widget_id = 'digital-product-grid-' . ($this->get_id() ? $this->get_id() : $widget_data_id);    
        $settings = $this->get_settings_for_display();

        // Use the helper function to sanitize custom class
        $sanitized_class = isset($settings['custom_class']) ? sanitize_input($settings['custom_class']) : '';
        
        // Get button styles
        $button_styles = $this->get_button_styles($settings);
    
        // Prepare the query if not provided
        if ($query === null) {
            $query = create_download_query($settings);
        }
    
        // Ensure the query is a valid WP_Query object
        if (!$query instanceof \WP_Query) {
            error_log('Invalid query returned');
            echo __('No products found', 'custom-widget-plugin');
            return;
        }
        
        // Log the found posts for debugging
        error_log('Query found posts: ' . print_r($query->posts, true));
    
        // Check if there are posts in the query
        if ($query->have_posts()) {
            display_product_grid($query, $settings, $button_styles, $widget_id); // Correctly pass $button_styles and $widget_id
        } else {
            echo __('No products found', 'custom-widget-plugin');
        }
    }
    
    // Button style utility function
    public function get_button_styles($settings) {
        $default_styles = [
            'background_color' => '#000000',
            'color' => '#FFFFFF',
            'border' => '1px solid #000000',
            'hover' => [
                'background_color' => '#555555',
                'color' => '#FFFFFF',
                'border' => '1px solid #555555',
            ],
            'active' => [
                'background_color' => '#333333',
                'color' => '#FFFFFF',
                'border' => '1px solid #333333',
            ],
            'padding' => '10px 20px',
            'margin' => '5px',
        ];

        // Assign styles based on settings, falling back to defaults
        return [
            'normal' => [
                'background_color' => isset($settings['button_background_color']) ? $settings['button_background_color'] : $default_styles['background_color'],
                'color' => isset($settings['button_text_color']) ? $settings['button_text_color'] : $default_styles['color'],
                'border' => isset($settings['button_border']) ? $this->format_border($settings['button_border']) : $default_styles['border'],
            ],
            'hover' => [
                'background_color' => isset($settings['button_hover_background_color']) ? $settings['button_hover_background_color'] : $default_styles['hover']['background_color'],
                'color' => isset($settings['button_hover_text_color']) ? $settings['button_hover_text_color'] : $default_styles['hover']['color'],
                'border' => isset($settings['button_hover_border']) ? $this->format_border($settings['button_hover_border']) : $default_styles['hover']['border'],
            ],
            'active' => [
                'background_color' => isset($settings['button_active_background_color']) ? $settings['button_active_background_color'] : $default_styles['active']['background_color'],
                'color' => isset($settings['button_active_text_color']) ? $settings['button_active_text_color'] : $default_styles['active']['color'],
                'border' => isset($settings['button_active_border']) ? $this->format_border($settings['button_active_border']) : $default_styles['active']['border'],
            ],
            'padding' => isset($settings['button_padding']) ? $settings['button_padding'] : $default_styles['padding'],
            'margin' => isset($settings['button_margin']) ? $settings['button_margin'] : $default_styles['margin'],
        ];
    }

    // Helper function to format border
    public function format_border($border) {
        if (is_array($border)) {
            // Check if border array contains all necessary properties and format them
            $border_style = isset($border['unit']) ? $border['unit'] : 'px';
            return "{$border['top']}{$border_style} {$border['right']}{$border_style} {$border['bottom']}{$border_style} {$border['left']}{$border_style}";
        }
        return $border; // Return the border directly if it's not an array
    }
    
    // Optional: Live editing content template (can be empty if not needed)
    protected function content_template() {
        // Optional live template code
    }
}
