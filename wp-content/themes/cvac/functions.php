<?php
// Load style.css
add_action( 'wp_enqueue_scripts', 'cvac_enqueue_scripts' );
function cvac_enqueue_scripts() {
    wp_enqueue_style( 
        'cvac-style', 
        get_stylesheet_uri()
    );
}

// Add favicon to the site pages
add_action( 'wp_head', 'cvac_favicon_link' );
function cvac_favicon_link() {
    echo '<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />' . "\n";
}

// Change the sender name and address in outgoing Wordpress e-mail
add_filter( 'wp_mail_from', 'cvac_wp_mail_from' );
add_filter( 'wp_mail_from_name', 'cvac_wp_mail_from_name' );
function cvac_wp_mail_from( $original_email_address ) {
    return 'no-reply@cvac.fr';
}
function cvac_wp_mail_from_name( $original_email_from ) {
    return 'CVAC';
}

// Enabling full template support
add_action( 'after_setup_theme', 'cvac_theme_setup' );
function cvac_theme_setup() {
    // WP WooCommerce
    add_theme_support( 'woocommerce' );

    // WP Event Manager
    add_theme_support( 'event-manager-templates' );
}

// WP Event Manager hide events from WooCommerce product page
add_action( 'woocommerce_product_query', 'cvac_pre_get_posts_query' );
function cvac_pre_get_posts_query( $q ) {
    $tax_query = (array) $q->get( 'tax_query' );
    $tax_query[] = array(
        'taxonomy' => 'product_type',
        'field' => 'slug',
        'terms' => array( 'event_package','event_ticket'), // Don't display products with event ticket type on the shop page.
        'operator' => 'NOT IN'
    );
    $q->set( 'tax_query', $tax_query );
}

// WP WooCommerce store available only to logged-in users
add_action( 'template_redirect', 'cvac_redirect_non_logged_in_users' );
function cvac_redirect_non_logged_in_users() {
    if ( !is_user_logged_in() && ( is_woocommerce() /*|| is_cart() || is_checkout()*/ ) ) {
        wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
        exit;
    }
}

// WP WooCommerce add vacation notice at Shop Loop and Single Product pages
add_action( 'woocommerce_before_shop_loop', 'cvac_woo_store_vacation_notice', 5 );
add_action( 'woocommerce_before_single_product', 'cvac_woo_store_vacation_notice', 5 );
function cvac_woo_store_vacation_notice() {
    $vacation_mode = woo_store_vacation()->service( 'options' )->get( 'vacation_mode', 'no' );
    if ($vacation_mode == 'yes') {
        $notice = woo_store_vacation()->service( 'options' )->get( 'vacation_notice' );
        $end_date = woo_store_vacation()->service( 'options' )->get( 'end_date' );
        $end_date_datetime = date_create( $end_date );
        $end_date_format = date_format( $end_date_datetime, "d/m/Y" );
        
        // Use this vacation notice only if the site notice is empty
        if ( empty( $notice ) && function_exists( 'wc_print_notice' ) ) {
            $notice = "Le magasin est fermé jusqu'au $end_date_format. Nous vous remercions de votre patience et vous prions de nous excuser pour ce désagrément.";

            printf( '<div id="%s">', esc_attr( woo_store_vacation()->get_slug() ) );
            $message = wp_kses_post( nl2br( $notice ) );
            wc_print_notice( $message, apply_filters( 'woo_store_vacation_notice_type', 'notice' ) );
            echo '</div>';
        }

    }
}

// Disable new user and password change e-mail notification to admin
add_filter( 'wp_new_user_notification_email_admin', '__return_false' );
remove_action( 'after_password_reset', 'wp_password_change_notification' );
// add_filter( 'wp_password_change_notification_email', 'wpdocs_stop_email' );
// function wpdocs_stop_email( $email ) {
//     $email['to'] = ''; //empty the TO: part, will fail to send
//     return $email;
// }
