jQuery(document).ready(function ($) {
    var selectedTags = [];
    var categories = [];
    
    // Handle filter form submission
    $('#download-filter-form').on('submit', function (e) {
        e.preventDefault();
        console.log("AJAX filter form submitted");

        categories = $('input[name="category[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        
        var elementorWidgetDiv = $('.elementor-widget-digital-product-grid>.elementor-widget-container');
        var client2ServeId = $('.elementor-widget-digital-product-grid');
        var widgetDataId = client2ServeId.data('id'); // Make sure this is not empty
        var pageId = ajax_object.pageId;
        var numberOfDownloads = $('input[name="number_of_products"]').val(); // Assuming this is part of the form
        
        // Send categories, tags, and number of downloads to the server
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_downloads',
                category: categories,
                tags: selectedTags,
                widget_data_id: widgetDataId,
                page_id: pageId,
                number_of_products: numberOfDownloads // Send the number of downloads as well
            },
            beforeSend: function() {
                // Clear the widget content and add a loading spinner
                elementorWidgetDiv.empty().append('<div class="loading-spinner"></div>');
                console.log('Before AJAX call, Widget ID:', widgetDataId);
            },
            success: function (response) {
                if (response.success) {
                    console.log('AJAX response success, rendering content.');
                    
                    // Clear the spinner and insert the new HTML content
                    elementorWidgetDiv.empty();
                    elementorWidgetDiv.html(response.data.output);  // Inject the HTML response
                } else {
                    elementorWidgetDiv.html('<p>No downloads found.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                elementorWidgetDiv.html('<p>There was an error fetching the downloads. Please try again later.</p>');
            },
            complete: function() {
                // Reset the form and clear suggestions after submission
                resetFilterForm();
                $('.loading-spinner').remove(); // Remove the spinner
            }
        });
    });
    
    // Tag suggestion (dynamic tag fetching)
    $('#filter-tags').on('input', function () {
        var searchTerm = $(this).val();

        if (searchTerm.length > 2) {
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'fetch_tag_suggestions',
                    term: searchTerm,
                    selected_tags: selectedTags // Send selected tags to exclude them from the results
                },
                success: function (response) {
                    $('#tags-suggestions').html(response.data);
                }
            });
        }
    });

    // Handle tag selection
    $(document).on('click', '.tag-suggestion', function () {
        var selectedTag = $(this).text();

        if (!selectedTags.includes(selectedTag)) {
            selectedTags.push(selectedTag);

            // Append selected tag to the list
            $('#selected-tags').append('<li class="tag-item">' + selectedTag + '<span class="remove-tag">x</span></li>');

            // Clear the input field and suggestion list
            $('#filter-tags').val('');
            $('#tags-suggestions').empty();
        }
    });

    // Remove a selected tag
    $(document).on('click', '.remove-tag', function () {
        var tagToRemove = $(this).parent().text().slice(0, -1); // Remove 'x'

        selectedTags = selectedTags.filter(function(tag) {
            return tag !== tagToRemove;
        });
        $(this).parent().remove();
    });

    // Function to reset the filter form
    function resetFilterForm() {
        $('input[name="category[]"]').prop('checked', false);
        selectedTags = [];
        $('#selected-tags').empty();
        $('#filter-tags').val('');
        $('#tags-suggestions').empty();
    }
});
