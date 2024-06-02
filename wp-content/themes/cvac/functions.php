<?php
// Load style.css
add_action( 'wp_enqueue_scripts', 'cvac_enqueue_styles' );

function cvac_enqueue_styles() {
    wp_enqueue_style( 
        'cvac-style', 
        get_stylesheet_uri()
    );
}

// Add favicon to the site pages
function my_favicon_link() {
    echo '<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />' . "\n";
}
add_action( 'wp_head', 'my_favicon_link' );

// WP WooCommerce Enabling Full Template Support
function cvac_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'cvac_add_woocommerce_support' );

// WP Event Manager Enabling Full Template Support
add_theme_support( 'event-manager-templates' );

// WP Event Manager Hide Events From WooCommerce Product Page
add_action( 'woocommerce_product_query', 'ts_custom_pre_get_posts_query' );

function ts_custom_pre_get_posts_query( $q ) {
    $tax_query = (array) $q->get( 'tax_query' );
    $tax_query[] = array(
        'taxonomy' => 'product_type',
        'field' => 'slug',
        'terms' => array( 'event_package','event_ticket'), // Don't display products with event ticket type on the shop page.
        'operator' => 'NOT IN'
    );
    $q->set( 'tax_query', $tax_query );
}