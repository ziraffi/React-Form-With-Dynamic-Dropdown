<?php
namespace CustomWidget\ElementorWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use CustomWidget\ElementorWidgets\Includes\Line_Drawer_Controls;
use CustomWidget\ElementorWidgets\Includes\Line_Render;

if ( ! defined( 'ABSPATH' ) ) exit;

class Line_Drawer_Widget extends Widget_Base {

    protected $line_drawer_assets_path;
    protected $line_drawer_editor_js;
    protected $line_drawer_editor_css;

    public function get_name() {
        return 'line_drawer';
    }

    public function get_title() {
        return __('Line Drawer', 'custom-widget-plugin');
    }

    public function get_icon() {
        return 'eicon-line-height';
    }

    public function get_categories() {
        return ['customWidgets'];
    }
    
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        // Define assets paths correctly
        $this->line_drawer_assets_path = esc_url(plugins_url('widgets/linedrawer/assets/', dirname(__FILE__)));
        $this->line_drawer_editor_css = esc_url(plugins_url('widgets/linedrawer/assets/css/editor-linedrawer.css', dirname(__FILE__)));

        // Require necessary classes
        $this->require_classes();
    }

    public function get_script_depends() {
        return ['line-drawer-js', 'line-drawer-editor-js']; // Return the handles of the scripts to be loaded
    }

    public function get_style_depends() {
        return ['line-drawer-css', 'line-drawer-editor-css']; // Return the handles of the styles to be loaded
    }


    private function require_classes() {
        $line_drawer_controls_path = MY_PLUGIN_ROOT . 'widgets/linedrawer/includes/class-line-drawer-controls.php';
        $line_render_path = MY_PLUGIN_ROOT . 'widgets/linedrawer/includes/class-line-render.php';

        $this->require_class($line_drawer_controls_path, 'Line_Drawer_Controls');
        $this->require_class($line_render_path, 'Line_Render');
    }

    private function require_class($class_path, $class_name) {
        if (file_exists($class_path)) {
            require_once $class_path;
        } else {
            error_log("{$class_name} file not found: " . $class_path);
        }
    }

    protected function _register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Line Settings', 'custom-widget-plugin'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        // Get controls from the Line_Drawer_Controls class
        $controls = Line_Drawer_Controls::get_control_options();

        if (is_array($controls) && !empty($controls)) {
            foreach ($controls as $control_id => $control_options) {
                $this->add_control($control_id, $control_options);
            }
        } else {
            error_log('No controls available from Line_Drawer_Controls.');
        }

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (empty($settings['first_container']) || empty($settings['second_container'])) {
            error_log('Container selection is missing. First: ' . print_r($settings['first_container'], true) . ' Second: ' . print_r($settings['second_container'], true));
            return; // Exit if either container is not selected
        }

        $line_render = new Line_Render(
            $settings['first_container'],
            $settings['second_container'],
            $settings['line_type'],
            $settings['first_target_position'] ?? 'Top Left',
            $settings['second_target_position'] ?? 'Top Left'
        );

        echo $line_render->render_lines();
    }
}
