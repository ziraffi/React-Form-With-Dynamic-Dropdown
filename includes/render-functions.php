<?php
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__FILE__) . '../includes/helper-functions.php';
include_once plugin_dir_path(__FILE__) . '../includes/query-functions.php';


function display_product_grid($query, $settings, $button_styles, $widget_id) {
    // Log the widget ID for debugging
    error_log('Rendering Widget ID: ' . $widget_id);
    ob_start();

    // Ensure the button styles are passed correctly
    $normal_styles = $button_styles['normal'];
    $hover_styles = $button_styles['hover'];
    $active_styles = $button_styles['active'];

    // Ensure that $settings contains the required keys
    $show_price = isset($settings['show_price']) ? $settings['show_price'] : 'no'; 
    $cards_per_row = isset($settings['cards_per_row']) ? $settings['cards_per_row'] : 3;
    $row_gap = isset($settings['row_gap']) ? $settings['row_gap'] : 20;
    $card_gap = isset($settings['column_gap']) ? $settings['column_gap'] : 20;
    $auto_flow = isset($settings['auto_flow']) ? $settings['auto_flow'] : 'row'; 

    ?>
    <div id="<?php echo esc_attr($widget_id); ?>" class="digital-product-grid-wrapper" 
         style="display: grid; 
                grid-template-columns: repeat(<?php echo esc_attr($cards_per_row); ?>, 1fr); 
                grid-auto-flow: <?php echo esc_attr($auto_flow); ?>;">
    <?php
    if ($query && $query->have_posts()) {
        // Log the post objects
        error_log('Posts in query: ' . print_r($query->posts, true));

        while ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID(); 
            $product_title = get_the_title();
            $product_excerpt = get_the_excerpt();
            $product_image = get_the_post_thumbnail_url($product_id, 'medium') ?: plugins_url('assets/images/placeholder.webp', plugin_dir_path(__FILE__));
            $product_price = get_product_price($product_id);
            $product_link = get_permalink($product_id);

            // Get the product categories and tags
            $product_category = get_taxonomy_terms($product_id, 'download_category');
            $product_tag = get_taxonomy_terms($product_id, 'download_tag');
            $gridTemplate = create_grid_template($product_category, $product_tag);

            ?>
            <div class="product-card" style="display: grid; ">
                <div class="p-0 m-0 card-image-container" style="grid-row: auto;">
                    <img class="card-img-top" src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>">
                </div>
                <div class="text-center py-1" style="grid-row: auto;">
                    <span class="download-title"><?php echo esc_html($product_title); ?></span>
                </div>
                <div class="text-center py-1" style="grid-row: auto;">
                    <p><?php echo esc_html($product_excerpt); ?></p>
                </div>
                    
                <?php if($product_category || $product_tag): ?> <!-- Check if either category or tag exists -->
                    <div class="terms-info" style="display: grid; grid-template-columns: <?php echo esc_attr($gridTemplate); ?>; gap: 5px; justify-items:center; align-items: start; text-align:center;">
                        <?php if($product_category): ?>
                            <div class="term-title">Categories: <br>
                            <span class="term-style"><?php echo esc_html($product_category); ?></span></div>
                        <?php endif; ?>
                        
                        <?php if($product_tag): ?>
                            <div class="term-title">Tags: <br>
                            <span class="term-style"><?php echo esc_html($product_tag); ?></span></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ('yes' === $show_price) { ?>
                <div class="price-info">
                    <strong><?php echo __('Price:', 'custom-widget-plugin') . ' ' . $product_price; ?></strong>
                </div>
                <?php }  ?>

                <a href="<?php echo esc_url($product_link); ?>" class="btn" style="background-color: <?php echo esc_attr($normal_styles['background_color']); ?>; color: <?php echo esc_attr($normal_styles['color']); ?>; border: <?php echo esc_attr($normal_styles['border']); ?>;">
                    View Product
                </a>
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo __('No products found', 'custom-widget-plugin');
    }
    ?>
    </div>
    <?php
    echo ob_get_clean();
}
