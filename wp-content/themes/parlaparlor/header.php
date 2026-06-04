<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <?php
        do_action('mt_gtm_tag');
    ?>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <title>Pärlapärlor</title>
</head>
<body <?php body_class('test'); ?>>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58MXQCKH"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

<header>
    <nav class="primary-nav">
        <section class="menu-top">
            <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo()) : ?>
                <?php the_custom_logo() ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="<?php echo get_bloginfo('name'); ?> logo" class="custom-logo">
                </a>
            <?php endif; ?>
            <div class="nav-icons">

                <div id="site-search" class="site-search">
                    <input type="text" id="global-search" placeholder="<?php esc_attr_e('Sök produkter eller kollektioner', 'collections-plugin'); ?>">
                    <button class="search-button input-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/search-icon-brown.svg" alt="Search Icon">
                    </button>
                    <div id="global-search-results" class="search-results"></div>
                </div>

                <a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/cart-icon.svg" alt="Cart Icon">
                    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </a>
            </div>
        </section>

        <section class="navigation">
            <?php
                $menu = wp_nav_menu([
                    'theme_location'    => 'primary',
                    'menu_class'     => 'menu',
                    'container'      => false,
                ]);
            ?>
        </section>
    </nav>
</header>
    