<?php 
$collection_id = $args['collection_id'];

// Get collection title & description
$title = get_the_title( $collection_id );
$description = get_the_excerpt( $collection_id );

// Get collection featured image
$image = get_the_post_thumbnail_url( $collection_id, 'collection_image' );

$product_ids = get_post_meta( $collection_id, 'mp_collection_products', true );


// CTA link
$cta_url = add_query_arg(
    [ 'add-collection-to-cart' => $collection_id ],
    home_url()
);
$cta_text = __( 'Köp kollektion', 'parlaparlor' );
?>


<div class="collection-showcase-wrapper">
    <section class="collection-showcase">

        <h2 class="collection-heading"><?php echo esc_html( $title ); ?></h2>

        <div class="collection-image">
            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr('En kollektionsbild av ' . $title) ?>">
        </div>

        <div class="collection-content">
            <p class="collection-description"><?php echo esc_html( $description ); ?></p>

            <?php if ( ! empty( $product_ids ) ) : ?>
            <?php foreach ( $product_ids as $product_id ) : 
                $product = wc_get_product( $product_id );
                if ( ! $product ) continue;
                $prod_img = get_the_post_thumbnail_url( $product_id, 'medium' );
                $product_url = get_permalink( $product_id );
            ?>
                <a href="<?php echo esc_url($product_url) ?>" class="collection-product">
                    <div class="collection-product-image">
                        <img src="<?php echo esc_url( $prod_img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
                    </div>
                    <p><?php echo esc_html( $product->get_name() ); ?></p>
                    <span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                </a>
            <?php endforeach; ?>
            <?php endif; ?>

        </div>
        <a href="<?php echo esc_url( $cta_url ); ?>" class="button">
        <?php echo esc_html( $cta_text ); ?>
        </a>
    </section>
</div>