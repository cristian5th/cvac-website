<?php
// Custom Function to Include
function my_favicon_link() {
    echo '<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />' . "\n";
}
add_action( 'wp_head', 'my_favicon_link' );


/**
 * override registrations form fields
 * @parma $fields
 * @return $fields
 **/
function custom_function($fields) {
    //customize $fields
    $fields['food']['value'] = 'vegetables';

return $fields;
}
add_filter( 'event_registration_form_fields', 'custom_function', 10, 1 );