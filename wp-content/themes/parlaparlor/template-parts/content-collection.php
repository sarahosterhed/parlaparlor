<?php

    $collection_id = get_the_ID();

    $cta_url = add_query_arg(
        [ 'add-collection-to-cart' => $collection_id ],
        home_url()
    );
    $cta_text = __( 'Köp kollektion', 'parlaparlor' );
?>


<article id="post-<?php the_ID(); ?>" class="main-grid collection-wrapper">

        <?php if(has_post_thumbnail()) : ?>
            <div class="single-collection-image">
                <?php the_post_thumbnail('post_image') ?>
            </div>
        <?php endif; ?>

        <div class="single-collection-content">
            <div class="single-collection-text-content">
                <h2><?php the_title(); ?></h2>
                <?php the_content('<p class="ingress"', '</p>'); ?>
            </div>

            <?php if ( is_singular( 'collection' ) ) : ?>
                <?php
                    $product_ids = get_post_meta( get_the_ID(), 'mp_collection_products', true);

                    if(!empty($product_ids) && is_array( $product_ids ) ) : ?>
                        <section class="single-collection-products">
                            <h4><?php _e( 'Produkter i den här kollektionen', 'my-theme' ); ?></h4>
                                <?php foreach ( $product_ids as $product_id ) : 
                                    $product = wc_get_product( $product_id ); 
                                    if ( $product ) : ?>
                                        <div class="single-collection-product">
                                            <a href="<?php echo get_permalink( $product_id ); ?>">
                                                <?php echo $product->get_image( 'thumbnail' ); ?>
                                                <span><?php echo $product->get_name(); ?></span>
                                                <span><?php echo $product->get_price_html(); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                        </section>
                    <?php endif; ?>
                    <h4>Totalpris</h4>
                    <p class="collection-price">
                        <?php echo wc_price( mp_get_collection_total_price( get_the_ID() ) ); ?>
                    </p>

                    <a href="<?php echo esc_url( $cta_url ); ?>" class="button">
                        <?php echo esc_html( $cta_text ); ?>
                    </a>
            <?php endif; ?>
        </div>


</article>