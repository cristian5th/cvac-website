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

// WP WooCommerce Store Available Only To Logged-in Users
function my_redirect_non_logged_in_users() {
    if ( !is_user_logged_in() && ( is_woocommerce() /*|| is_cart() || is_checkout()*/ ) ) {
        wp_redirect( get_permalink( get_option('woocommerce_myaccount_page_id') ) );
        exit;
    }
}
add_action( 'template_redirect', 'my_redirect_non_logged_in_users' );

// WP WooCommerce add vacation notice at Shop Loop and Single Product pages
function my_woo_store_vacation_notice() {
    $vacation_mode = woo_store_vacation()->service( 'options' )->get( 'vacation_mode', 'no' );
    if ($vacation_mode == 'yes') {
        $notice = woo_store_vacation()->service( 'options' )->get( 'vacation_notice' );
        $end_date = woo_store_vacation()->service( 'options' )->get( 'end_date' );
        $end_date_datetime = date_create( $end_date );
        $end_date_format = date_format( $end_date_datetime, "d/m/Y" );
        
        // Bail early, if the notice is empty.
        if ( empty( $notice ) && function_exists( 'wc_print_notice' ) ) {
            $notice = "Le magasin est fermé jusqu'au $end_date_format. Nous vous remercions de votre patience et vous prions de nous excuser pour ce désagrément.";

            printf( '<div id="%s">', esc_attr( woo_store_vacation()->get_slug() ) );
            $message = wp_kses_post( nl2br( $notice ) );
            wc_print_notice( $message, apply_filters( 'woo_store_vacation_notice_type', 'notice' ) );
            echo '</div>';
        }

    }
}
add_action( 'woocommerce_before_shop_loop', 'my_woo_store_vacation_notice', 5 );
add_action( 'woocommerce_before_single_product', 'my_woo_store_vacation_notice', 5 );

// Disable new user and password change e-mail notification to admin
add_filter( 'wp_new_user_notification_email_admin', '__return_false' );
add_filter( 'send_password_change_email', '__return_false' );