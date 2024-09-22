jQuery(document).ready(function ($) {
    var selectedTags = [];
    var categories = [];

    // Handle filter form submission
    $('#download-filter-form').on('submit', function (e) {
        e.preventDefault();

        categories = $('input[name="category[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        var widgetWrapper = $('.elementor-widget-digital-product-grid>.elementor-widget-container');

        // Optionally, you can also reset the layout styles here if needed
        // widgetWrapper.attr('style', '');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_downloads',
                category: categories,
                tags: selectedTags
            },
            beforeSend: function() {
            // Create the spinner element
            var spinner = $('<div class="loading-spinner"></div>');
            widgetWrapper.empty().append(spinner); // Clear previous content and add spinner
            },
            success: function (response) {
                widgetWrapper.html(response.data);
                
                // Reinitialize Elementor styles and controls
                elementorFrontend.elementsHandler.runReadyTrigger(widgetWrapper);

                // Reset the form after successful filtering
                resetFilterForm();
            },
            complete: function() {
            // Remove the spinner after the request is complete
            widgetWrapper.find('.loading-spinner').remove();
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
                    console.log(response); // Log the response to check if it's correct
                    $('#tags-suggestions').html(response.data);
                }
            });
        }
    });

    // Handle tag selection
    $(document).on('click', '.tag-suggestion', function () {
        var selectedTag = $(this).text();

        // Add the selected tag to the array if not already present
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

        // Remove tag from the array and list
        selectedTags = selectedTags.filter(function(tag) {
            return tag !== tagToRemove;
        });
        $(this).parent().remove();
    });

    // Function to reset the filter form
    function resetFilterForm() {
        // Uncheck all category checkboxes
        $('input[name="category[]"]').prop('checked', false);

        // Clear selected tags
        selectedTags = [];
        $('#selected-tags').empty();
        
        // Clear tag input field
        $('#filter-tags').val('');
        $('#tags-suggestions').empty();
    }
});
