<?php
namespace CustomWidget\ElementorWidgets\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

if ( ! class_exists( 'Elementor\Controls_Manager' ) ) {
    return; 
}

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



    public function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'custom-widget-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
    // Min Height Control
    $this->add_responsive_control(
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
    $this->add_responsive_control(
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
        $this->add_control(
            'custom_class',
            [
                'label' => __('Custom Class', 'custom-widget-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => esc_html__('Enter custom class', 'custom-widget-plugin'),
            ]
        );
    
        // Adding "Number of Products" Control
        $this->add_control(
            'show_price',
            [
                'label' => __('Show Price', 'custom-widget-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-widget-plugin'),
                'label_off' => __('No', 'custom-widget-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'number_of_products',
            [
                'label' => __('Number of Products', 'custom-widget-plugin'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 6,
                'description' => esc_html__('Enter the number of products to display', 'custom-widget-plugin'),
            ]
        );
        $this->add_control(
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
    
        $this->add_control(
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
                    '{{WRAPPER}} .row' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
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
    
        $this->end_controls_section();
    
        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'custom-widget-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
    
        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'custom-widget-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .digital-product-wrapper .card-title' => 'color: {{VALUE}}',
                ],
            ]
        );
    
        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'custom-widget-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .digital-product-wrapper .card' => 'background-color: {{VALUE}}',
                ],
            ]
        );
    
        $this->end_controls_section();
    
//////// Button Style Section /////////////////////////////////////////////////////////////////////////////////////////////////

        $this->start_controls_section(
            'button_style_section',
            [
                'label' => esc_html__('Button Style', 'custom-widget-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
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
                    '{{WRAPPER}} .card-footer' => 'text-align: {{VALUE}};', // Apply to the card footer where the button is contained
                ],
            ]
        );
        
        // Tabs for Normal, Hover, Active
        $this->start_controls_tabs('button_tabs');
        
        // Normal Tab
        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => esc_html__('Normal', 'custom-widget-plugin'),
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_normal_typography',
                'selector' => '{{WRAPPER}} .btn',
            ]
        );
        
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_normal_background',
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .btn',
            ]
        );
        

        $this->add_control(
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

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_normal_border',
                'selector' => '{{WRAPPER}} .btn',
            ]
        );
        
        $this->add_control(
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
        
        $this->add_control(
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
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_normal_box_shadow',
                'selector' => '{{WRAPPER}} .btn',
            ]
        );
        
        $this->add_control(
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
        
        $this->add_control(
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
        
        $this->end_controls_tab(); // End Normal Tab
        
        // Hover Tab
        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => esc_html__('Hover', 'custom-widget-plugin'),
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_hover_typography',
                'selector' => '{{WRAPPER}} .btn:hover',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_hover_background',
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .btn:hover',
            ]
        );
        

        $this->add_control(
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

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_hover_border',
                'selector' => '{{WRAPPER}} .btn:hover',
            ]
        );
        
        $this->add_control(
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
        
        $this->add_control(
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
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .btn:hover',
            ]
        );
        
        $this->add_control(
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
        
        $this->add_control(
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
        
        $this->end_controls_tab(); // End Hover Tab
        
        // Active Tab
        $this->start_controls_tab(
            'button_active_tab',
            [
                'label' => esc_html__('Active', 'custom-widget-plugin'),
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_active_typography',
                'selector' => '{{WRAPPER}} .btn:active',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_active_background',
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .btn:active',
            ]
        );
        
        $this->add_control(
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

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_active_border',
                'selector' => '{{WRAPPER}} .btn:active',
            ]
        );
        
        $this->add_control(
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
        
        $this->add_control(
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
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_active_box_shadow',
                'selector' => '{{WRAPPER}} .btn:active',
            ]
        );
        
        $this->add_control(
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
        
        $this->add_control(
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
        
        $this->end_controls_tab(); // End Active Tab
        
        $this->end_controls_tabs(); // End Tabs
        
        $this->end_controls_section();
        
        

        }            
    

        
        private $category_id = null;

        public function set_category_id($category_id) {
            $this->category_id = intval($category_id);
        }
    
        public function get_category_id() {
            return isset($this->category_id) ? $this->category_id : null;
        }
    
        public function render($query = null) {
            // Ensure you have a unique identifier for the wrapper
            $widget_id = 'digital-product-grid-' . $this->get_id();            
            $settings = $this->get_settings_for_display();
        
            // Default settings
            $show_price = isset($settings['show_price']) && $settings['show_price'] === 'yes';
            $number_of_products = isset($this->settings['number_of_products']) ? $this->settings['number_of_products'] : -1; // Default to all (-1)
            $cards_per_row = isset($settings['cards_per_row']) ? $settings['cards_per_row'] : 3;
            $row_gap = isset($settings['row_gap']['size']) ? $settings['row_gap']['size'] : 20;
            $auto_flow = isset($settings['auto_flow']) ? $settings['auto_flow'] : 'row';
            $button_alignment = isset($settings['button_alignment']) ? $settings['button_alignment'] : 'center';
        
            // Button styles
            $button_styles = $this->get_button_styles($settings);
        
            // Prepare the query if not provided
            if ($query === null) {
                $args = [
                    'post_type' => 'download',
                    'posts_per_page' => $number_of_products,
                    'tax_query' => [
                        'relation' => 'AND',
                    ],
                ];
        
                // Get selected categories and tags from settings
                $selected_categories = isset($settings['selected_categories']) ? array_map('intval', $settings['selected_categories']) : [];
                $selected_tags = isset($settings['selected_tags']) ? array_map('sanitize_text_field', $settings['selected_tags']) : [];
        
                // Apply category filters
                if (!empty($selected_categories)) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'download_category',
                        'field' => 'term_id',
                        'terms' => $selected_categories,
                    ];
                }
        
                // Apply tag filters
                if (!empty($selected_tags)) {
                    $tag_ids = [];
                    foreach ($selected_tags as $tag_name) {
                        $tag = get_term_by('name', trim($tag_name), 'download_tag');
                        if ($tag) {
                            $tag_ids[] = $tag->term_id;
                        }
                    }
        
                    if (!empty($tag_ids)) {
                        $args['tax_query'][] = [
                            'taxonomy' => 'download_tag',
                            'field' => 'term_id',
                            'terms' => $tag_ids,
                        ];
                    }
                }
        
                $query = new \WP_Query($args);
            }
        
            // Log the actual number of downloads returned
            error_log('Number of Downloads Found: ' . $query->found_posts);
        
            ob_start();
            ?>
            <style>
                .digital-product-button {
                    background-color: <?php echo esc_attr($button_styles['normal']['background_color']); ?>;
                    color: <?php echo esc_attr($button_styles['normal']['color']); ?>;
                    border: <?php echo esc_attr($button_styles['normal']['border']); ?>;
                    padding: <?php echo esc_attr($button_styles['padding']); ?>;
                    margin: <?php echo esc_attr($button_styles['margin']); ?>;
                }
                .digital-product-button:hover {
                    background-color: <?php echo esc_attr($button_styles['hover']['background_color']); ?>;
                    color: <?php echo esc_attr($button_styles['hover']['color']); ?>;
                    border: <?php echo esc_attr($button_styles['hover']['border']); ?>;
                }
                .digital-product-button:active {
                    background-color: <?php echo esc_attr($button_styles['active']['background_color']); ?>;
                    color: <?php echo esc_attr($button_styles['active']['color']); ?>;
                    border: <?php echo esc_attr($button_styles['active']['border']); ?>;
                }
                .digital-product-footer {
                    text-align: <?php echo esc_attr($button_alignment); ?>;
                }
            </style>
            <div id="<?php echo esc_attr($widget_id); ?>" class="digital-product-grid-wrapper" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr($cards_per_row); ?>, 1fr); gap: <?php echo esc_attr($row_gap); ?>px; grid-auto-flow: <?php echo esc_attr($auto_flow); ?>;">
                <?php
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
        
                        $product_id = get_the_ID();
                        $product_title = get_the_title();
                        $product_excerpt = get_the_excerpt();
                        $product_image = get_the_post_thumbnail_url($product_id, 'medium') ?: plugins_url('assets/images/placeholder.webp', plugin_dir_path(__FILE__));
                        $product_price = edd_get_download_price($product_id);
                        $product_link = get_permalink($product_id);

                        // Get the product categories (taxonomy: 'download_category')
                        $product_categories = get_the_terms($product_id, 'download_category');
                        $product_category_names = [];
                        if (!is_wp_error($product_categories) && !empty($product_categories)) {
                            foreach ($product_categories as $category) {
                                $product_category_names[] = $category->name;
                            }
                        }
                        $product_category = implode(', ', $product_category_names);

                        // Get the product tags (taxonomy: 'download_tag')
                        $product_tags = get_the_terms($product_id, 'download_tag');
                        $product_tag_names = [];
                        if (!is_wp_error($product_tags) && !empty($product_tags)) {
                            foreach ($product_tags as $tag) {
                                $product_tag_names[] = $tag->name;
                            }
                        }
                        $product_tag = implode(', ', $product_tag_names);

                        // Determine the number of columns based on the presence of category and tag
                        $columns = [];
                        if ($product_category) {
                            $columns[] = '1fr'; // Add a column for category if it exists
                        }
                        if ($product_tag) {
                            $columns[] = '1fr'; // Add a column for tag if it exists
                        }

                        // Create the grid template string
                        $gridTemplate = implode(' ', $columns);

        
                        $unique_id = 'product-widget-id-' . $product_id;
        
                        $this->add_render_attribute(
                            'wrapper_' . $product_id,
                            [
                                'id' => $unique_id,
                                'class' => ['digital-product-wrapper', $settings['custom_class']],
                                'role' => 'region',
                                'aria-label' => esc_attr($product_title),
                            ]
                        );
        
                        $this->add_render_attribute(
                            'inner_' . $product_id,
                            [
                                'class' => 'digital-product-inner',
                                'data-product-id' => $product_id,
                            ]
                        );
        
                        ?>
<div <?php echo $this->get_render_attribute_string('wrapper_' . $product_id); ?> style="display: grid; grid-template-rows: 1fr; gap: 10px;">
    <div <?php echo $this->get_render_attribute_string('inner_' . $product_id); ?> style="display: grid; grid-template-rows: 1fr; gap: 10px;">
        <div class="card" style="display: grid; grid-template-rows: auto 1fr auto; height: 100%;">
            <!-- Image Container -->
            <div class="p-0 m-0 card-image-container" style="grid-row: auto;">
                <?php if (!empty($product_image)) { ?>
                    <img class="card-img-top" src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>">
                <?php } else { ?>
                    <img class="card-img-top" src="<?php echo esc_url(plugins_url('assets/images/placeholder.webp', plugin_dir_path(__FILE__))); ?>" alt="<?php echo esc_attr__('Placeholder Image', 'custom-widget-plugin'); ?>">
                <?php } ?>
            </div>
                
            <!-- Card Body with title and excerpt -->
            <div class="card-body text-center" style="display: grid; grid-template-rows: auto 1fr;">
                <div class="text-center py-1" style="grid-row: auto;">
                    <span class="download-title"><?php echo esc_html($product_title); ?></span>
                </div>
                <?php if($product_excerpt): ?>
                    <div style="grid-row: auto;">
                        <p class="card-text text-center"><?php echo esc_html($product_excerpt); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($columns)): ?> <!-- Check if either category or tag exists -->
                    <div class="terms-info px-3 py-2" style="display: grid; grid-template-columns: <?php echo esc_attr($gridTemplate); ?>; gap: 5px; align-items: start;">
                        <?php if($product_category): ?>
                            <div class="term-title">
                                Categories: <br>
                                <span class="term-style"><?php echo esc_html($product_category); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($product_tag): ?>
                            <div class="term-title">
                                Tags: <br>
                                <span class="term-style"><?php echo esc_html($product_tag); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                        
                <!-- Price (if enabled) -->
                <?php if ($show_price) { ?>
                    <div class="price-info py-2" style="grid-row: auto;">
                        <p class="card-text text-center"><strong><?php echo __('Price:', 'custom-widget-plugin') . ' ' . edd_currency_filter($product_price); ?></strong></p>
                    </div>
                <?php } ?>
                    
                <!-- Card Footer with button -->
                <div class="card-footer digital-product-footer" style="grid-row: auto;">
                    <a href="<?php echo esc_url($product_link); ?>" class="btn digital-product-button">
                        <?php echo __('View Product', 'custom-widget-plugin'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    echo __('No digital products found', 'custom-widget-plugin');
                }
                ?>
            </div>
            <?php
            echo ob_get_clean();
        }
        
        
// Utility function to get button styles
private function get_button_styles($settings) {
    return [
        'normal' => [
            'background_color' => isset($settings['button_background_color']) ? $settings['button_background_color'] : '#000000',
            'color' => isset($settings['button_text_color']) ? $settings['button_text_color'] : '#FFFFFF',
            'border' => isset($settings['button_border']) ? $settings['button_border'] : '1px solid #000000',
        ],
        'hover' => [
            'background_color' => isset($settings['button_hover_background_color']) ? $settings['button_hover_background_color'] : '#555555',
            'color' => isset($settings['button_hover_text_color']) ? $settings['button_hover_text_color'] : '#FFFFFF',
            'border' => isset($settings['button_hover_border']) ? $settings['button_hover_border'] : '1px solid #555555',
        ],
        'active' => [
            'background_color' => isset($settings['button_active_background_color']) ? $settings['button_active_background_color'] : '#333333',
            'color' => isset($settings['button_active_text_color']) ? $settings['button_active_text_color'] : '#FFFFFF',
            'border' => isset($settings['button_active_border']) ? $settings['button_active_border'] : '1px solid #333333',
        ],
        'padding' => isset($settings['button_padding']) ? $settings['button_padding'] : '10px 20px',
        'margin' => isset($settings['button_margin']) ? $settings['button_margin'] : '5px',
    ];
}



    // Optional: Live editing content template (can be empty if not needed)
    protected function content_template() {
}
}