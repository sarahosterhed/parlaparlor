<?php get_header(); ?>


    <main class="main">

        <?php if( have_posts() ) : ?>
            <?php while( have_posts() ) : the_post(); ?>

                <?php the_title(); ?>
                <?php the_content(); ?>

            <?php endwhile; ?>
                <?php else: 
                    echo '<p>No content found.</p>';
                    ?>
         <?php endif; ?>

    </main>

<?php get_footer(); ?>