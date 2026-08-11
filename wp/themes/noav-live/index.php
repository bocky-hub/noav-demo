<?php
/**
 * Fallback template (blog / archives). The copied site is page-based, so this
 * is only a graceful fallback that keeps the shared shell.
 *
 * @package Noav_Live
 */
get_header();
?>
<section class="relative"><div class="relative z-10 container mx-auto pt-8 lg:pt-12 pb-8 lg:pb-12">
	<div class="flex flex-col gap-4 mb-6 items-center text-center mx-auto">
		<h1 class="break-word heading-large" style="color: rgb(17, 24, 39);"><?php echo esc_html( wp_get_document_title() ); ?></h1>
	</div>
	<?php if ( have_posts() ) : ?>
		<div class="flex flex-col gap-10 max-w-3xl mx-auto">
		<?php while ( have_posts() ) : the_post(); ?>
			<article>
				<h2 class="heading-medium" style="color: rgb(17, 24, 39);"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="rich-text-block" style="color: rgb(17, 24, 39);"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div></section>
<?php
get_footer();
