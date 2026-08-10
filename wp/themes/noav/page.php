<?php
/**
 * Fristående sida.
 *
 * Sidor byggda helt av blockmönster (som startsidan) skriver ut sina egna
 * sektioner. En vanlig textsida saknar den ramen, och skulle utan hjälp
 * hamna kant i kant med skärmen — därför läggs .section/.container till
 * bara när innehållet inte redan börjar med en egen sektion.
 *
 * @package Noav
 */

declare( strict_types = 1 );

get_header();

while ( have_posts() ) :
	the_post();

	$noav_content   = (string) apply_filters( 'the_content', get_the_content() );
	$noav_is_framed = (bool) preg_match( '/^\s*<(section|div)[^>]*class="[^"]*\b(section|hero)\b/', $noav_content );

	if ( $noav_is_framed ) {
		echo $noav_content; // phpcs:ignore WordPress.Security.EscapeOutput -- redan filtrerat av the_content.
	} else {
		?>
		<section class="section">
			<div class="container">
				<div class="section-head" data-reveal>
					<h1><?php the_title(); ?></h1>
				</div>
				<div class="prose">
					<?php echo $noav_content; // phpcs:ignore WordPress.Security.EscapeOutput -- redan filtrerat av the_content. ?>
				</div>
			</div>
		</section>
		<?php
	}
endwhile;

get_footer();
