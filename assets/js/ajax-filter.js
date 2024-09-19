jQuery(document).ready(function ($) {
    $('#filter-category').change(function () {
        var category_id = $(this).val();
        console.log('Dropdown changed, category_id:', category_id); // Debugging
        var widgetWrapper = $('.digital-product-grid-wrapper');
        
        if (!category_id) {
            category_id = 0; // Default to 0 if "Select a category" is selected
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
                    console.log('AJAX response received'); // Debugging
                    // Replace the loading spinner with the fetched content
                    widgetWrapper.html(response.data);
                } else {
                    console.error('AJAX Error:', response.data); // Debugging
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error); // Debugging
                // Optionally remove the spinner even on error
                widgetWrapper.find('.loading-spinner').remove();
            }
        });
    });
});
