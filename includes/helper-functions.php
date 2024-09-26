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

function find_widget_settings($elements_data, $widget_id) {
    foreach ($elements_data as $element) {
        // Check if the element has a widget ID and if it matches the one we are looking for
        if (isset($element['id']) && $element['id'] === $widget_id) {
            return isset($element['settings']) ? $element['settings'] : []; // Return settings if found
        }
        
        // If the element has children, recursively search in them
        if (isset($element['elements']) && is_array($element['elements'])) {
            $settings = find_widget_settings($element['elements'], $widget_id);
            if (!empty($settings)) {
                return $settings;
            }
        }
    }
    return []; // Return an empty array if no settings are found
}

function get_taxonomy_terms($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy); // Get the terms for the specified taxonomy
    if (!is_wp_error($terms) && !empty($terms)) {
        $term_names = [];
        foreach ($terms as $term) {
            $term_names[] = $term->name; // Collect term names
        }
        return implode(', ', $term_names); // Return as a comma-separated string
    }
    return ''; // Return an empty string if no terms found
}

function create_grid_template($product_category, $product_tag) {
    // Start with an empty array of columns
    $columns = [];

    // If the product category exists, add a column for it
    if ($product_category) {
        $columns[] = '1fr'; // Add a column for category if it exists
    }

    // If the product tag exists, add a column for it
    if ($product_tag) {
        $columns[] = '1fr'; // Add a column for tag if it exists
    }

    // Return the columns as a space-separated string for the grid-template
    return !empty($columns) ? implode(' ', $columns) : '1fr'; // Default to 1 column if no terms exist
}
