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
    
        var client2ServeId = $('.elementor-widget-digital-product-grid');
        var widgetId = client2ServeId.data('id'); // Make sure this is not empty
        var pageId = ajax_object.pageId;
    
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_downloads',
                category: categories,
                tags: selectedTags,
                widget_id: widgetId,
                page_id: pageId
            },
            beforeSend: function() {
                // Clear the widget content and add a loading spinner
                client2ServeId.empty().append('<div class="loading-spinner"></div>');
                console.log('Before AJAX call, Widget ID:', widgetId);
            },
            success: function (response) {
                if (response.success) {
                    console.log('AJAX response success, rendering content.');
                    
                    // Clear the spinner and insert the new HTML content
                    client2ServeId.empty();  // First, empty the widget container
                    client2ServeId.html(response.data.output);  // Inject the HTML response
                    // Reset the form fields
                    $('#download-filter-form')[0].reset();                  } else {
                    client2ServeId.html('<p>No downloads found.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                client2ServeId.html('<p>There was an error fetching the downloads. Please try again later.</p>');
            },
            complete: function() {
                // Remove the spinner
                $('.loading-spinner').remove();
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
