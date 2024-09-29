<?php
if (!defined('ABSPATH')) {
    exit;
}

function sanitize_input($input) {
    return sanitize_text_field($input);
}

function get_product_price($product_id) {
    $edd_price= edd_get_download_price($product_id);
    return edd_currency_filter($edd_price);
}

function find_widget_settings(array $elements_data, string $widget_data_id): array {
    foreach ($elements_data as $element) {
        if (isset($element['id']) && $element['id'] === $widget_data_id) {
            return $element['settings'] ?? []; // Use null coalescing operator
        }
        
        if (!empty($element['elements']) && is_array($element['elements'])) {
            $settings = find_widget_settings($element['elements'], $widget_data_id);
            if (!empty($settings)) {
                return $settings;
            }
        }
    }
    return [];
}

function get_taxonomy_terms(int $post_id, string $taxonomy): string {
    $terms = get_the_terms($post_id, $taxonomy);
    if (!is_wp_error($terms) && !empty($terms)) {
        return implode(', ', wp_list_pluck($terms, 'name')); // wp_list_pluck for cleaner code
    }
    return ''; 
}

function create_grid_template(?string $product_category, ?string $product_tag): string {
    $columns = [];

    if ($product_category) {
        $columns[] = '1fr'; // Category column
    }

    if ($product_tag) {
        $columns[] = '1fr'; // Tag column
    }

    return !empty($columns) ? implode(' ', $columns) : '1fr'; 
}

