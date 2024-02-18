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

// WP Event Manager Enabling Full Template Support
add_theme_support( 'event-manager-templates' );