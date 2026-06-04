<?php

function load_stylesheets() {
    wp_register_style('stylesheet', get_template_directory_uri() . '/style.css', '', 1, 'all');
    wp_enqueue_style('stylesheet');
}
add_action('wp_enqueue_scripts', 'load_stylesheets');


function mytheme_enqueue_scripts() {
    wp_enqueue_script(
        'filters-and-sorting',
        get_template_directory_uri() . '/js/filters-and-sorting.js',
        [],
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');


// Register Menus
function mt_register_menus() {
    register_nav_menus([
          'primary' =>    __('Primary Menu', 'my-theme')
        ]);
}

add_action('after_setup_theme', 'mt_register_menus');

// Add support for custom logo
add_theme_support( 'custom-logo', [
    'flex-width'  => true,
    'flex-height' => true
]);
add_theme_support( 'post-thumbnails' );

//Add image sizes

add_action('after_setup_theme', function() {
    add_image_size('collection_image', 1200, 1600, true);
    add_image_size('post_image', 1100, 750, true);
});




remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

// Add custom product title with h4
add_action( 'woocommerce_shop_loop_item_title', function() {
    echo '<h4 class="woocommerce-loop-product__title">' . get_the_title() . '</h4>';
}, 10 );

// Remove add to cart button from product loops
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// Remove stripe link
add_filter( 'wc_stripe_hide_payment_request_on_product_page', '__return_true' );
add_filter( 'wc_stripe_show_payment_request_on_cart', '__return_false' );




//Google Tag Manager
function mt_gtm_tag() {
    
        $gtm_id = apply_filters('mt_gtm_id', 'GTM-58MXQCKH');
    ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo($gtm_id) ?>');</script>
        <!-- End Google Tag Manager -->

    <?php
}

add_action('wp_head', 'mt_gtm_tag', 0);