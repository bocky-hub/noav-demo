<?php
/**
 * Reservmall.
 *
 * Används för arkiv och sökresultat. Sajten har i praktiken bara startsida,
 * enhetssidor och enstaka fristående sidor, men WordPress kräver att den
 * här filen finns — och en besökare som hamnar här ska få något vettigt.
 *
 * @package Noav
 */

declare( strict_types = 1 );

get_header();
?>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="section-head">
				<?php if ( is_search() ) : ?>
					<p class="eyebrow"><?php esc_html_e( 'Sökresultat', 'noav' ); ?></p>
					<h1>
						<?php
						printf(
							/* translators: %s: sökordet. */
							esc_html__( 'Träffar för ”%s”', 'noav' ),
							esc_html( get_search_query() )
						);
						?>
					</h1>
				<?php else : ?>
					<h1><?php echo esc_html( (string) get_the_archive_title() ); ?></h1>
				<?php endif; ?>
			</div>

			<div class="method-grid" data-reveal-group>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<article class="method-card">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'prev_text' => __( 'Föregående', 'noav' ),
					'next_text' => __( 'Nästa', 'noav' ),
				)
			);
			?>

		<?php else : ?>
			<div class="section-head">
				<h1><?php esc_html_e( 'Inget hittades', 'noav' ); ?></h1>
				<p class="lede">
					<?php esc_html_e( 'Sidan du letar efter finns inte längre. Gå till startsidan för att se våra enheter och aktuella lediga platser.', 'noav' ); ?>
				</p>
			</div>
			<a class="btn btn--accent" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Till startsidan', 'noav' ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
