jQuery(document).ready(function ($) {
    $('#filter-category').change(function () {
        var category_id = $(this).val();
        console.log('Dropdown changed, category_id:', category_id); 
        var widgetWrapper = $('.digital-product-grid-wrapper');
        
        if (!category_id) {
            category_id = 0; 
        }
        
        // Clear existing content and add loading spinner
        widgetWrapper.empty().append('<div class="loading-spinner"></div>');
        
        // Optionally reset layout styles here if needed
        widgetWrapper.attr('style', '');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_products',
                category_id: category_id
            },
            success: function (response) {
                if (response.success) {
                    console.log('AJAX response received'); 
                    // Replace the loading spinner with the fetched content
                    widgetWrapper.html(response.data);
                } else {
                    console.error('AJAX Error:', response.data); 
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error); 
                // Optionally remove the spinner even on error
                widgetWrapper.find('.loading-spinner').remove();
            }
        });
    });
});
