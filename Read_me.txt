Need to update code in EDD template-function.php if you get error in debug
PHP Warning:  Attempt to read property "post_type" on int in ' Your-path\wp-content\plugins\easy-digital-downloads\includes\template-functions.php ' on line 462

Path: wp-content\plugins\easy-digital-downloads\includes\template-functions.php
function edd_after_download_content( $content ) {
    global $post;

    // Check if $post is an object
    if ( !is_object( $post ) ) {
        // Attempt to get the post object by ID
        $post = get_post( $post ); // Retrieves the post object using its ID
    }

    // Now, check if it's a valid post object
    if ( is_object( $post ) && $post->post_type == 'download' && is_singular( 'download' ) && is_main_query() && !post_password_required() ) {
        ob_start();
        do_action( 'edd_after_download_content', $post->ID );
        $content .= ob_get_clean();
    }

    return $content;
}
add_filter( 'the_content', 'edd_after_download_content' );
