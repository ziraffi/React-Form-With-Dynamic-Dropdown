<?php
if (!defined('ABSPATH')) {
    exit;
}

function create_download_query($settings) {
    $args = [
        'post_type' => 'download',
        'posts_per_page' => isset($settings['number_of_products']) ? $settings['number_of_products'] : -1,
        'tax_query' => ['relation' => 'AND'],
    ];

    if (!empty($settings['selected_categories'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'download_category',
            'field' => 'term_id',
            'terms' => array_map('intval', $settings['selected_categories']),
        ];
    }

    if (!empty($settings['selected_tags'])) {
        $tag_ids = [];
        foreach ($settings['selected_tags'] as $tag_name) {
            $tag = get_term_by('name', sanitize_text_field($tag_name), 'download_tag');
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

    return new WP_Query($args);
}
