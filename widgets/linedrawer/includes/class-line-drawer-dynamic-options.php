<?php
namespace CustomWidget\ElementorWidgets\Includes;

class Line_Drawer_Dynamic_Options {
    
    /**
     * Retrieves the containers on the current page.
     *
     * @return array An associative array of container IDs and their labels.
     */
    public static function get_containers() {
        $containers = [];
        
        // Get the current post ID
        $post_id = get_the_ID();

        // Get the Elementor document for the current post
        $document = \Elementor\Plugin::instance()->documents->get($post_id);

        // Check if the document is valid and of a page type
        if ($document && method_exists($document, 'get_elements_data')) {
            // Get the elements from the document
            $elementor_data = $document->get_elements_data();
            
            // If elements are available, parse them
            if (!empty($elementor_data)) {
                $containers = self::parse_elements($elementor_data);
            }
        }

        return $containers;
    }

    /**
     * Parses the Elementor elements to build a tree structure.
     *
     * @param array $elements The array of Elementor elements.
     * @param string $parent_id Optional. The parent ID to build a nested structure.
     * @return array An associative array of container IDs and their labels.
     */
    private static function parse_elements($elements, $depth = 0) {
        $result = [];
    
        foreach ($elements as $element) {
            // Get element settings
            $id = $element['id'];
            
            // Check if title exists, otherwise set a default title
            $title = !empty($element['settings']['title']) ? $element['settings']['title'] : __('Untitled Container', 'custom-widget-plugin');
    
            // Add indentation to reflect hierarchy in the dropdown
            $indentation = str_repeat('— ', $depth);
    
            // Store the container with its hierarchy level
            $result[$id] = $indentation . $title;
    
            // Recursively parse child elements if they exist
            if (!empty($element['elements'])) {
                $child_elements = self::parse_elements($element['elements'], $depth + 1);
                $result = array_merge($result, $child_elements);
            }
        }
    
        return $result;
    }
    

    /**
     * Retrieve and return the options in a tree structure for dropdowns.
     *
     * @return array An array of container options.
     */
    public static function get_dynamic_container_options() {
        $containers = self::get_containers();
        // Structure for dropdown (label => value)
        return $containers;
    }
}
