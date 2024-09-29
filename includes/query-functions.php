<?php
if (!defined('ABSPATH')) {
    exit;
}

function create_download_query($settings) {
    // Log the entire settings array for debugging
    error_log("Settings: " . print_r($settings, true));
    
    $args = [
        'post_type' => 'download',
        'posts_per_page' => !empty($settings['number_of_products']) ? intval($settings['number_of_products']) : -1, // Use -1 to show all products if not set
        'tax_query' => ['relation' => 'OR'],
    ];

    // Log the number of products
    error_log("Number Of Products: " . (isset($settings['number_of_products']) ? $settings['number_of_products'] : 'Not Set'));

    // Only apply category filter if selected categories are provided
    if (!empty($settings['selected_categories']) && is_array($settings['selected_categories'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'download_category',
            'field' => 'term_id',
            'terms' => array_map('intval', $settings['selected_categories']),
            'operator' => 'IN',
        ];
    }

    // Only apply tag filter if selected tags are provided
    if (!empty($settings['selected_tags']) && is_array($settings['selected_tags'])) {
        $tag_ids = [];
        foreach ($settings['selected_tags'] as $tag_name) {
            $tag = get_term_by('name', sanitize_text_field($tag_name), 'download_tag');
            if ($tag && !is_wp_error($tag)) {
                $tag_ids[] = $tag->term_id;
            }
        }
        if (!empty($tag_ids)) {
            $args['tax_query'][] = [
                'taxonomy' => 'download_tag',
                'field' => 'term_id',
                'terms' => $tag_ids,
                'operator' => 'IN',
            ];
        }
    }

    return new WP_Query($args);
}
