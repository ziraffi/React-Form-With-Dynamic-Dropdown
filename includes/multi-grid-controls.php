<?php
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

function download_grid_register_controls($widget)  {

    if (!$widget instanceof \Elementor\Widget_Base) {
        error_log('Widget is not an instance of \Elementor\Widget_Base'); // For debugging
        return;
    } 


// Content Section
$widget->start_controls_section(
    'content_section',
    [
        'label' => esc_html__('Content', 'custom-widget-plugin'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]
);
// Min Height Control
$widget->add_responsive_control(
    'min_height',
    [
        'label' => __('Min Height', 'custom-widget-plugin'),
        'type' => Controls_Manager::SLIDER,
        'placeholder' => __('Enter min height', 'plugin-name'),
        'size_units' => ['px', 'em', 'rem', '%', 'vh'],
        'selectors' => [
            '{{WRAPPER}}' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
    ]
);
// Min Width Control
$widget->add_responsive_control(
    'min_width',
    [
        'label' => __('Min Width', 'custom-widget-plugin'),
        'type' => Controls_Manager::SLIDER,
        'placeholder' => __('Enter min width', 'plugin-name'),
        'size_units' => ['px', 'em', 'rem', '%', 'vw'],
        'selectors' => [
            '{{WRAPPER}}' => 'min-width: {{SIZE}}{{UNIT}};',
        ],
    ]
);    
    $widget->add_control(
        'custom_class',
        [
            'label' => esc_html__('Custom Class', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'placeholder' => esc_html__('Enter custom class', 'custom-widget-plugin'),
        ]
    );

    $widget->add_control(
        'show_price',
        [
            'label' => esc_html__('Show Price', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'custom-widget-plugin'),
            'label_off' => esc_html__('No', 'custom-widget-plugin'),
            'return_value' => 'yes', 
            'default' => 'no', 
        ]
    );
    // Adding "Number of Products" Control        
    $widget->add_control(
        'number_of_products',
        [
            'label' => esc_html__('Number of Products', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 20,
            'step' => 1,
            'default' => 6,
            'description' => esc_html__('Enter the number of products to display', 'custom-widget-plugin'),
        ]
    );
    $widget->add_control(
        'cards_per_row',
        [
            'label' => __('Cards per Row', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 6,
            'step' => 1,
            'default' => 3,
            'description' => esc_html__('Enter the number of cards to display per row', 'custom-widget-plugin'),
        ]
    );

    $widget->add_control(
        'row_gap',
        [
            'label' => __('Gap Between Rows', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 10,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}} .digital-product-grid-wrapper' => 'row-gap: {{SIZE}}{{UNIT}};', // Correct target
            ],
        ]
    );
    
    $widget->add_control(
        'card_gap',
        [
            'label' => __('Gap Between Cards', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 10,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}} .digital-product-grid-wrapper' => 'column-gap: {{SIZE}}{{UNIT}};', // Correct target
            ],
        ]
    );
    
    $widget->add_control(
        'auto_flow',
        [
            'label' => __('Auto Flow', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'row' => __('Row', 'custom-widget-plugin'),
                'column' => __('Column', 'custom-widget-plugin'),
            ],
            'default' => 'row',
            'description' => esc_html__('Choose how the grid items should auto flow when they overflow.', 'custom-widget-plugin'),
        ]
    );

    $widget->end_controls_section();

    // Style Section
    $widget->start_controls_section(
        'style_section',
        [
            'label' => esc_html__('Style', 'custom-widget-plugin'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );

    $widget->add_control(
        'text_color',
        [
            'label' => __('Text Color', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .digital-product-wrapper .card-title' => 'color: {{VALUE}}',
            ],
        ]
    );

    $widget->add_control(
        'background_color',
        [
            'label' => __('Background Color', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .digital-product-wrapper .card' => 'background-color: {{VALUE}}',
            ],
        ]
    );

    $widget->end_controls_section();

/////// Button Style Section /////////////////////////////////////////////////////////////////////////////////////////////////
    $widget->start_controls_section(
        'button_style_section',
        [
            'label' => esc_html__('Button Style', 'custom-widget-plugin'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );
    $widget->add_control(
        'button_alignment',
        [
            'label' => __('Button Alignment', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => __('Left', 'custom-widget-plugin'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'custom-widget-plugin'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => __('Right', 'custom-widget-plugin'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .card-footer' => 'text-align: {{VALUE}};', 
            ],
        ]
    );
    
    // Tabs for Normal, Hover, Active
    $widget->start_controls_tabs('button_tabs');
    
    // Normal Tab
    $widget->start_controls_tab(
        'button_normal_tab',
        [
            'label' => esc_html__('Normal', 'custom-widget-plugin'),
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'button_normal_typography',
            'selector' => '{{WRAPPER}} .btn',
        ]
    );
    
    
    $widget->add_group_control(
        \Elementor\Group_Control_Background::get_type(),
        [
            'name' => 'button_normal_background',
            'types' => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .btn',
        ]
    );
    
    $widget->add_control(
        'button_color_normal',
        [
            'label' => __( 'Button Text Color (Normal)', 'custom-widget-plugin' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#FFFFFF',
            'selectors' => [
                '{{WRAPPER}} .btn' => 'color: {{VALUE}};',
            ],
        ]
    );
    $widget->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'button_normal_border',
            'selector' => '{{WRAPPER}} .btn',
        ]
    );
    
    $widget->add_control(
        'button_normal_border_weight',
        [
            'label' => __('Border Width', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'default' => [
                'top' => 2,
                'right' => 2,
                'bottom' => 2,
                'left' => 2,
                'unit' => 'px',
                'isLinked' => true,
            ],
            'selectors' => [
                '{{WRAPPER}} .btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_control(
        'button_normal_border_radius',
        [
            'label' => __('Border Radius', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 4,
                'right' => 4,
                'bottom' => 4,
                'left' => 4,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Box_Shadow::get_type(),
        [
            'name' => 'button_normal_box_shadow',
            'selector' => '{{WRAPPER}} .btn',
        ]
    );
    
    $widget->add_control(
        'button_normal_padding',
        [
            'label' => __('Padding', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 10,
                'right' => 20,
                'bottom' => 10,
                'left' => 20,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_control(
        'button_normal_margin',
        [
            'label' => __('Margin', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 5,
                'right' => 5,
                'bottom' => 5,
                'left' => 5,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->end_controls_tab();
    
    // Hover Tab
    $widget->start_controls_tab(
        'button_hover_tab',
        [
            'label' => esc_html__('Hover', 'custom-widget-plugin'),
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'button_hover_typography',
            'selector' => '{{WRAPPER}} .btn:hover',
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Background::get_type(),
        [
            'name' => 'button_hover_background',
            'types' => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .btn:hover',
        ]
    );
    
    $widget->add_control(
        'button_color_hover',
        [
            'label' => __( 'Button Text Color (Hover)', 'custom-widget-plugin' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#FFFFFF',
            'selectors' => [
                '{{WRAPPER}} .btn:hover' => 'color: {{VALUE}};',
            ],
        ]
    );
    $widget->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'button_hover_border',
            'selector' => '{{WRAPPER}} .btn:hover',
        ]
    );
    
    $widget->add_control(
        'hover_border_weight',
        [
            'label' => __('Border Width', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'default' => [
                'top' => 2,
                'right' => 2,
                'bottom' => 2,
                'left' => 2,
                'unit' => 'px',
                'isLinked' => true,
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_control(
        'button_hover_border_radius',
        [
            'label' => __('Border Radius', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 4,
                'right' => 4,
                'bottom' => 4,
                'left' => 4,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Box_Shadow::get_type(),
        [
            'name' => 'button_hover_box_shadow',
            'selector' => '{{WRAPPER}} .btn:hover',
        ]
    );
    
    $widget->add_control(
        'button_hover_padding',
        [
            'label' => __('Padding', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 10,
                'right' => 20,
                'bottom' => 10,
                'left' => 20,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_control(
        'button_hover_margin',
        [
            'label' => __('Margin', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 5,
                'right' => 5,
                'bottom' => 5,
                'left' => 5,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->end_controls_tab(); // End Hover Tab
    
    // Active Tab
    $widget->start_controls_tab(
        'button_active_tab',
        [
            'label' => esc_html__('Active', 'custom-widget-plugin'),
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'button_active_typography',
            'selector' => '{{WRAPPER}} .btn:active',
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Background::get_type(),
        [
            'name' => 'button_active_background',
            'types' => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .btn:active',
        ]
    );
    
    $widget->add_control(
        'button_color_active',
        [
            'label' => __( 'Button Text Color (Active)', 'custom-widget-plugin' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#FFFFFF',
            'selectors' => [
                '{{WRAPPER}} .btn:active' => 'color: {{VALUE}};',
            ],
        ]
    );
    $widget->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'button_active_border',
            'selector' => '{{WRAPPER}} .btn:active',
        ]
    );
    
    $widget->add_control(
        'active_border_width',
        [
            'label' => __('Border Width', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'default' => [
                'top' => 2,
                'right' => 2,
                'bottom' => 2,
                'left' => 2,
                'unit' => 'px',
                'isLinked' => true,
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:active' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_control(
        'button_active_border_radius',
        [
            'label' => __('Border Radius', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 4,
                'right' => 4,
                'bottom' => 4,
                'left' => 4,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_group_control(
        \Elementor\Group_Control_Box_Shadow::get_type(),
        [
            'name' => 'button_active_box_shadow',
            'selector' => '{{WRAPPER}} .btn:active',
        ]
    );
    
    $widget->add_control(
        'button_active_padding',
        [
            'label' => __('Padding', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 10,
                'right' => 20,
                'bottom' => 10,
                'left' => 20,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:active' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_control(
        'button_active_margin',
        [
            'label' => __('Margin', 'custom-widget-plugin'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem' ],
            'default' => [
                'top' => 5,
                'right' => 5,
                'bottom' => 5,
                'left' => 5,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .btn:active' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->end_controls_tab(); 
    
    $widget->end_controls_tabs();
    
    $widget->end_controls_section();    
}