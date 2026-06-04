<?php
/*
Template Name: Create Collection
*/
get_header();
?>

<main class="main">
    <section class="intro-section">
        <h1><?php the_title(); ?></h1>
        <p class="ingress"><?php _e('Här kan du dela dina kreationer så att fler kan upptäcka dem. Skapa en kollektion och låt andra inspireras för att kanske bli en ny favorit för någon annan.'); ?></p>
        <hr />
    </section>

    <?php if( have_posts() ) : ?>
        <?php while( have_posts() ) : the_post(); ?>
            <section class="main-grid">
                <?php the_content(); ?>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php get_footer(); ?>