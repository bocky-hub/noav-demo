<?php
/** Editable WordPress page content, styled by the Noav theme. */
get_header();
?>
<main id="noav-content" class="noav-content">
<?php while ( have_posts() ) : the_post(); ?>
<?php if ( ! is_front_page() && ! get_post_meta( get_the_ID(), '_noav_editable_migrated', true ) ) : ?>
<h1 class="noav-custom-page-title"><?php the_title(); ?></h1>
<?php endif; ?>
<?php the_content(); ?>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
