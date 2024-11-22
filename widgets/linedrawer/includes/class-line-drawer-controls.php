<?php
namespace CustomWidget\ElementorWidgets\Includes;

use Elementor\Controls_Manager;
use CustomWidget\ElementorWidgets\Includes\Line_Drawer_Dynamic_Options;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include Line Drawer Dynamic Options
$line_drawer_dynamic_options = MY_PLUGIN_ROOT . 'widgets/linedrawer/includes/class-line-drawer-dynamic-options.php';
if (file_exists($line_drawer_dynamic_options)) {
    require_once $line_drawer_dynamic_options;
} else {
    error_log('Line Drawer Dynamic Options class not found at: ' . $line_drawer_dynamic_options);
}

class Line_Drawer_Controls {

    public static function get_control_options() {
        return [
            'first_container_checkbox' => self::get_first_container_checkbox(),
            'first_container' => self::get_first_container(),
            'first_container_target_position' => self::get_first_container_target_position(), // Target Position for first container
            'second_container_checkbox' => self::get_second_container_checkbox(),
            'second_container' => self::get_second_container(),
            'second_container_target_position' => self::get_second_container_target_position(), // Target Position for second container
            'line_type' => self::get_line_type(),
        ];
    }

    private static function get_first_container_checkbox() {
        return [
            'label' => __('Select 1st container', 'custom-widget-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'custom-widget-plugin'),
            'label_off' => __('No', 'custom-widget-plugin'),
            'return_value' => 'yes',
            'default' => 'no',
        ];
    }

    private static function get_first_container() {
        return [
            'label' => __('Container 1', 'custom-widget-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => self::get_dynamic_container_options(), // Fetch dynamic options
            'condition' => [
                'first_container_checkbox' => 'yes',
            ],
        ];
    }

    private static function get_first_container_target_position() {
        return [
            'label' => __('Target Position for Container 1', 'custom-widget-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => self::get_target_position_options(),
            'default' => 'top-left', // Default value
            'condition' => [
                'first_container_checkbox' => 'yes',
                'first_container!' => '', // Ensure that first container is selected
            ],
            'label_block' => true,
        ];
    }

    private static function get_second_container_checkbox() {
        return [
            'label' => __('Select 2nd container', 'custom-widget-plugin'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'custom-widget-plugin'),
            'label_off' => __('No', 'custom-widget-plugin'),
            'return_value' => 'yes',
            'default' => 'no',
        ];
    }

    private static function get_second_container() {
        return [
            'label' => __('Container 2', 'custom-widget-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => self::get_dynamic_container_options(), // Fetch dynamic options
            'condition' => [
                'second_container_checkbox' => 'yes',
            ],
        ];
    }

    private static function get_second_container_target_position() {
        return [
            'label' => __('Target Position for Container 2', 'custom-widget-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => self::get_target_position_options(),
            'default' => 'top-left', // Default value
            'condition' => [
                'second_container_checkbox' => 'yes',
                'second_container!' => '', // Ensure that second container is selected
            ],
            'label_block' => true,
        ];
    }

    private static function get_line_type() {
        return [
            'label' => __('Line Type', 'custom-widget-plugin'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'straight' => __('Straight', 'custom-widget-plugin'),
                'curve' => __('Curve', 'custom-widget-plugin'),
            ],
            'default' => 'straight',
            'label_block' => true,
        ];
    }

    private static function get_target_position_options() {
        return [
            'top-left' => __('Top Left', 'custom-widget-plugin'),
            'top-center' => __('Top Center', 'custom-widget-plugin'),
            'top-right' => __('Top Right', 'custom-widget-plugin'),
            'right-center' => __('Right Center', 'custom-widget-plugin'),
            'bottom-right' => __('Bottom Right', 'custom-widget-plugin'),
            'bottom-center' => __('Bottom Center', 'custom-widget-plugin'),
            'bottom-left' => __('Bottom Left', 'custom-widget-plugin'),
            'left-center' => __('Left Center', 'custom-widget-plugin'),
        ];
    }

    private static function get_dynamic_container_options() {
        return Line_Drawer_Dynamic_Options::get_dynamic_container_options(); // Call to the dynamic options method
    }
}
