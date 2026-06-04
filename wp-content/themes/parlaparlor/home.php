<?php
/* Template Name: Home */

get_header(); ?>

<?php
    $hero_image = get_field('hero_image');
    if ( $hero_image ) : ?>
        <section class="hero" style="background-image: url('<?php echo esc_url($hero_image); ?>')">
            <div class="hero-content">
                <h1><?php bloginfo('name'); ?></h1>
                <p class="ingress"><?php bloginfo('description'); ?></p>
            </div>
        </section>
    <?php endif ?>

    <main class="main">
        <section class="intro-section">
            <h2 class="home-heading"><?php echo _e('Utvalda Kollektioner', 'my_theme') ?></h2>
            <p class="ingress">Här hittar du utvalda kollektioner skapade av våra användare med inspirerande kombinationer av pärlor, hängen och smyckesdelar som enkelt kan beställas hem. Varje kollektion är unik och framtagen för att göra ditt smyckesskapande både enklare och mer personligt.</p>
            <!-- SATODO: switch to admin-editable content instead -->

            <hr />
        </section>

        <?php
            $collection_ids = [156, 158, 160];

            foreach ( $collection_ids as $collection_id ) {
                get_template_part(
                    'template-parts/content', 'selected-collection',
                    [ 'collection_id' => $collection_id ]
                );
            }
        ?>

    </main>

<?php get_footer(); ?>