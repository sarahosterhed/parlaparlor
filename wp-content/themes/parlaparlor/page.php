<?php get_header(); ?>

    <main class="main">

        <?php if( have_posts() ) : ?>
            <?php while( have_posts() ) : the_post(); ?>
                <?php the_post_thumbnail('post_image'); ?>
                <?php the_title(); ?>
                <?php the_content(); ?>

            <?php endwhile; ?>
        <?php else: ?>
            <?php echo '<p>No content found.</p>'; ?>
         <?php endif; ?>

    </main>

<?php get_footer(); ?>