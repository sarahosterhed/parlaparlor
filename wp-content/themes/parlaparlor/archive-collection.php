<?php get_header(); ?>

<main>
    <section class="intro-section">
            <h1><?php post_type_archive_title(); ?></h1>
            <p class="ingress"><?php _e('Här hittar du utvalda kollektioner skapade av våra användare med inspirerande kombinationer av pärlor, hängen och smyckesdelar som enkelt kan beställas hem. Varje kollektion är unik och framtagen för att göra ditt smyckesskapande både enklare och mer personligt.'); ?></p>
            <hr />
    </section>

    <section class="buttons-menu">
        <a href="<?php echo esc_url( home_url( '/skapa-kollektion/' ) ); ?>" class="button create-collection-button">
            <?php _e( 'Skapa kollektion', 'collections-plugin' ); ?>
            <span>+</span>
        </a>
        <button class="button filter-button">
            <?php _e( 'Filtrera', 'collections-plugin' ); ?>
        </button>
        <button class="button sort-button">
            <?php _e( 'Sortera', 'collections-plugin' ); ?>
        </button>
    </section>

    <section id="main-content" class="main-content-layout">
        <div class="main-grid">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
            <article class="collection-card">
                    <a href="<?php the_permalink(); ?>" class="collection-card-image-wrapper">
                        <div class="collection-card-overlay">
                            <h4><?php the_title(); ?></h4>
                            <?php the_excerpt(); ?>
                        </div>
                        <?php if(has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('collection_image') ?>
                        <?php endif; ?>
                    </a>

                    <?php
                        $collection_id = get_the_ID();
                        $product_ids = get_post_meta( $collection_id, 'mp_collection_products', true );

                        if ( !empty ($product_ids) ) : ?>
                                <div class="collection-product-grid">
                                    <?php
                                        $index = 0;

                                        foreach( $product_ids as $product_id) : 
                                            $product = wc_get_product($product_id);
                                            if ( !$product ) continue;

                                            $product_img = get_the_post_thumbnail_url( $product_id, 'medium' );
                                            ?>
                                            <div class="collection-product-image">
                                                <img src="<?php echo esc_url( $product_img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
                                                <?php if( $index === 2 && count($product_ids) > 3) : ?>
                                                    <div class="collection-product-overlay">+<?php echo count($product_ids) - 2; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                                $index++;
                                                if($index >= 3) break;
                                            ?>
                                        <?php endforeach; ?>
                                </div>
                        <?php endif; ?>

                </article>

            <?php endwhile; ?>

            <?php the_posts_navigation(); ?>
        <?php else : ?>
            <p><?php _e( 'No collections found.', 'my-theme' ); ?></p>
        <?php endif; ?>

        </div>
        <aside class="filters-panel">
            <?php do_action('collections_render_filters'); ?>
        </aside>
        <aside class="sort-panel">
            <?php do_action('collections_render_sorting'); ?>
        </aside>

    </section>
</main>

<?php get_footer(); ?>