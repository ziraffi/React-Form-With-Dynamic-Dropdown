<?php
namespace CustomWidget\ElementorWidgets\Includes;

class Line_Render {

    private $first_container;
    private $second_container;
    private $line_type;
    private $first_target_position;
    private $second_target_position;

    public function __construct($first_container, $second_container, $line_type, $first_target_position, $second_target_position) {
        $this->first_container = $first_container;
        $this->second_container = $second_container;
        $this->line_type = $line_type;
        $this->first_target_position = $first_target_position;  
        $this->second_target_position = $second_target_position; 
    }

    /**
     * Render the line between the selected containers.
     */
    public function render_lines() {
        // Ensure Elementor is loaded before rendering
        if (!\Elementor\Plugin::$instance || !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return; 
        }
        if (empty($this->first_container) || empty($this->second_container)) {
            error_log("One or both containers are not set.");
            return; // Exit if containers are not set
        }

        $container_data = $this->get_container_data();
        if (empty($container_data)) {
            error_log("Failed to retrieve container data.");
            return; // Exit if no data was retrieved
        } else {
            error_log("Container Data for finding coordinates: " . print_r($container_data, true));
        }

        // Get adjusted coordinates based on target positions
        $start = $this->get_coordinates($this->first_container, $container_data, $this->first_target_position);
        $end = $this->get_coordinates($this->second_container, $container_data, $this->second_target_position);

        error_log("Offset first Container After get_coordinates(): " . print_r($start, true));
        error_log("Offset second Container After get_coordinates(): " . print_r($end, true)); 

        if (!$start || !$end) {
            error_log("Invalid coordinates for line rendering.");
            return;
        }

        return $this->line_type === 'straight' 
            ? $this->render_straight_line($start, $end) 
            : $this->render_curved_line($start, $end);
    }

    /**
     * Get container data for both containers.
     *
     * @return array
     */
    private function get_container_data() {
        return [
            $this->first_container => $this->get_container_info($this->first_container),
            $this->second_container => $this->get_container_info($this->second_container),
        ];
    }

    /**
     * Get the container information.
     *
     * @param string $container_id
     * @return array|null
     */
    private function get_container_info($container_id) {
        $post_id = get_the_ID();
        $elementor_document = \Elementor\Plugin::instance()->documents->get($post_id);
    
        if (!$elementor_document) {
            error_log("Elementor document not found for post ID: $post_id");
            return null;
        }

        $elementor_data = $elementor_document->get_elements_data();
        if (empty($elementor_data)) {
            error_log("Elementor data is empty for post ID: $post_id");
            return null;
        }

        $element = $this->find_element_by_id($container_id, $elementor_data);
        if ($element) {
            // error_log("Elementor found: " . print_r($element, true));
            return $this->calculate_position($element);
        }

        error_log("Container ID $container_id not found in the Elementor data.");
        return null;
    }

    /**
     * Calculate the position of the container based on viewport size and its CSS properties.
     *
     * @param array $element
     * @return array
     */
    private function calculate_position($element) {
        // Get viewport dimensions
        $viewport_width = isset($_POST['viewport_width']) ? (int)$_POST['viewport_width'] : 1920;
        $viewport_height = isset($_POST['viewport_height']) ? (int)$_POST['viewport_height'] : 1080;

        // Base position data
        $position_data = [
            'left' => (float)($element['settings']['_offset_x']['size'] ?? 0),
            'top' => (float)($element['settings']['_offset_y']['size'] ?? 0),
            'width' => (float)($element['settings']['width']['size'] ?? 100),
            'height' => (float)($element['settings']['height']['size'] ?? 100),
        ];

        // Adjust position based on viewport size
        $position_data['left'] = ($position_data['left'] / 100) * $viewport_width;
        $position_data['top'] = ($position_data['top'] / 100) * $viewport_height;

        // Get positioning style
        $position_type = $element['settings']['position'] ?? 'relative';

        // Adjust coordinates based on position type
        if ($position_type === 'absolute') {
            // For absolute positioning, calculate relative to nearest positioned ancestor
            $parent_data = $this->get_parent_position($element);
            if ($parent_data) {
                $position_data['left'] += $parent_data['left'];
                $position_data['top'] += $parent_data['top'];
            }
        } elseif ($position_type === 'relative') {
            // For relative positioning, adjust based on the parent's position
            $parent_data = $this->get_parent_position($element);
            if ($parent_data) {
                $position_data['left'] += $parent_data['left'];
                $position_data['top'] += $parent_data['top'];
            }
        }

        return $position_data;
    }

    /**
     * Get parent position data.
     *
     * @param array $element
     * @return array|null
     */
    private function get_parent_position($element) {
        if (isset($element['parent_id'])) {
            $parent_data = $this->get_container_info($element['parent_id']);
            if ($parent_data) {
                return $this->calculate_position($parent_data);
            }
        }
        return null; // Return null if no parent data found
    }

    /**
     * Recursively search for an element by ID or data-id within the Elementor layout data.
     *
     * @param string $container_id
     * @param array $elements
     * @return array|null
     */
    private function find_element_by_id($container_id, $elements) {
        foreach ($elements as $element) {
            // Check if the current element matches the given container ID
            if (isset($element['id']) && $element['id'] === $container_id) {
                return $element;
            }

            // Recursively search in child elements if available
            if (isset($element['elements']) && is_array($element['elements'])) {
                $result = $this->find_element_by_id($container_id, $element['elements']);
                if ($result) {
                    return $result;
                }
            }
        }
        return null; // Return null if no matching element is found
    }

    /**
     * Get coordinates for the specified container based on the target position.
     *
     * @param string $container_id
     * @param array $container_data
     * @param string $target_position
     * @return array|false Returns coordinates or false if not found
     */
    private function get_coordinates($container_id, $container_data, $target_position) {
        // Check if the container data exists for the provided ID
        if (!isset($container_data[$container_id])) {
            error_log("Container data not found for ID: $container_id.");
            return false;
        }
    
        // Retrieve container data
        $data = $container_data[$container_id];
    
        // Initialize coordinates based on container's left and top properties
        $x = (float) $data['left'];
        $y = (float) $data['top'];
    
        // Adjust coordinates based on the target position
        switch (strtolower($target_position)) {
            case 'top left':
                // No adjustment needed
                break;
            case 'top center':
                $x += $data['width'] / 2;
                break;
            case 'top right':
                $x += $data['width'];
                break;
            case 'right center':
                $x += $data['width'];
                $y += $data['height'] / 2;
                break;
            case 'bottom right':
                $x += $data['width'];
                $y += $data['height'];
                break;
            case 'bottom center':
                $x += $data['width'] / 2;
                $y += $data['height'];
                break;
            case 'bottom left':
                $y += $data['height'];
                break;
            case 'left center':
                $y += $data['height'] / 2;
                break;
            default:
                error_log("Invalid target position: $target_position.");
                return false;
        }
    
        // Return calculated coordinates
        return [
            'x' => $x,
            'y' => $y,
        ];
    }

    /**
     * Render a straight line.
     *
     * @param array $start
     * @param array $end
     * @return string SVG markup for the straight line
     */
    private function render_straight_line($start, $end) {
        return sprintf(
            '<svg width="%s" height="%s" style="position: absolute; pointer-events: none;">
                <line x1="%s" y1="%s" x2="%s" y2="%s" stroke="black" stroke-width="2" />
            </svg>',
            max($start['x'], $end['x']) + 20, // Adding extra space for line rendering
            max($start['y'], $end['y']) + 20,
            $start['x'], 
            $start['y'], 
            $end['x'], 
            $end['y']
        );
    }

    /**
     * Render a curved line.
     *
     * @param array $start
     * @param array $end
     * @return string SVG markup for the curved line
     */
    private function render_curved_line($start, $end) {
        // Here you could use cubic bezier curve equations or similar logic.
        // For simplicity, using a quadratic curve as an example.
        $control_x = ($start['x'] + $end['x']) / 2;
        $control_y = min($start['y'], $end['y']) - 100; // Arbitrary control point

        return sprintf(
            '<svg width="%s" height="%s" style="position: absolute; pointer-events: none;">
                <path d="M %s %s Q %s %s %s %s" stroke="black" stroke-width="2" fill="transparent" />
            </svg>',
            max($start['x'], $end['x']) + 20,
            max($start['y'], $end['y']) + 20,
            $start['x'], 
            $start['y'], 
            $control_x, 
            $control_y, 
            $end['x'], 
            $end['y']
        );
    }
}
