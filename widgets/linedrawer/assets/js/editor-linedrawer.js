jQuery(document).ready(function($) {    
    // Use a selector for the dropdown that matches the data-setting attribute
    var $dropdown = $('[data-setting="first_container"], [data-setting="second_container"]');
    console.log("Container Dropdown Info: ",$dropdown);

    // When the user hovers over a dropdown item
    $dropdown.on('mouseenter', 'option', function() {
        var containerId = $(this).val(); // Get the container ID
        console.log("Current Container Data-ID",containerId);
        
        // Find the element in the Elementor editor by ID
        var $element = $('[data-id="' + containerId + '"]');
        
        // If the element exists, highlight it
        if ($element.length) {
            $element.css({
                'border': '2px solid red',
                'transition': 'border 0.2s ease'
            });
        }
    });

    // Remove highlight on mouse leave
    $dropdown.on('mouseleave', 'option', function() {
        var containerId = $(this).val();
        var $element = $('[data-id="' + containerId + '"]');
        
        // Remove the highlight
        if ($element.length) {
            $element.css('border', '');
        }
    });

    // Scroll to the container on click
    $dropdown.on('change', function() {
        var containerId = $(this).val();
        var $element = $('[data-id="' + containerId + '"]');
        
        if ($element.length) {
            $('html, body').animate({
                scrollTop: $element.offset().top - 100 // Adjust this value if necessary
            }, 500);
        }
    });
});
