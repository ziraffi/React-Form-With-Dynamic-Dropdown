<?php
namespace CustomWidget\ElementorWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined("ABSPATH") ) {
    exit;
}
class React_Form_Widget extends Widget_Base {
    public function get_name() {
        return 'react-form-widget';
    }

    public function get_title() {
        return __('React Form', 'custom-widget-plugin');
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    public function get_categories() {
        return ['customWidgets'];
    }

    public function get_script_depends() {
        return ['react', 'react-dom', 'react-form-script', 'react-form-init'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'custom-widget-plugin'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'widget_title',
            [
                'label' => __('Widget Title', 'custom-widget-plugin'),
                'type' => Controls_Manager::TEXT,
                'default' => __('React Form', 'custom-widget-plugin'),
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'custom-widget-plugin'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]);

            $this->add_control(
                'text_color',
                [
                    'label' => __('Text Color', 'custom-widget-plugin'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#000',
                    'selectors' => [
                        '{{WRAPPER}} h2' => 'color: {{VALUE}};',
                    ],
                ]
                
                );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="react-form-widget">
            <h2><?php echo $settings['widget_title']; ?></h2>
             <div id="react-form-root"></div>
        </div>
        <?php

    }
}