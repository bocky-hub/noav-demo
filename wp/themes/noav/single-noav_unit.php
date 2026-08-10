<?php
/**
 * Enhetssida.
 *
 * Sidans sektioner ligger som block på enheten, precis som på startsidan.
 * Hjältebilden byggs däremot av mallen: den behöver enhetens utvalda bild,
 * ort och platspanel, och att låta en redaktör sätta ihop det för hand vore
 * fyra chanser att glömma en bit.
 *
 * @package Noav
 */

declare( strict_types = 1 );

get_header();

while ( have_posts() ) :
	the_post();

	$noav_unit = noav_get_unit( get_post()->post_name );
	if ( null === $noav_unit ) {
		// Enheten är ännu inte publicerad — förhandsgranskning som utkast.
		$noav_unit = array(
			'name'     => get_the_title(),
			'location' => '',
			'phone'    => '',
			'image'    => (string) get_the_post_thumbnail_url( null, 'full' ),
			'imageAlt' => '',
			'artClass' => 'art-vinkelviken',
		);
	}

	$noav_cta       = noav_cta_href( $noav_unit['phone'] );
	$noav_has_photo = '' !== $noav_unit['image'];
	?>

	<section class="hero hero--unit on-dark" aria-label="<?php esc_attr_e( 'Introduktion', 'noav' ); ?>">
		<?php if ( $noav_has_photo ) : ?>
			<div class="hero-bg hero-bg--photo" aria-hidden="true">
				<img class="media-fill" src="<?php echo esc_url( $noav_unit['image'] ); ?>" alt="" fetchpriority="high">
			</div>
		<?php else : ?>
			<div class="hero-bg <?php echo esc_attr( $noav_unit['artClass'] ); ?>" aria-hidden="true"></div>
		<?php endif; ?>

		<div class="container">
			<div class="hero-inner">
				<div>
					<a class="breadcrumb hero-eyebrow" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
							stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M19 12H6"/><path d="m10.5 17-5-5 5-5"/>
						</svg>
						<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
					</a>

					<h1 class="hero-title">
						<span class="line"><span class="line-inner"><?php the_title(); ?></span></span>
					</h1>

					<?php
					// Gatuadressen är mer användbar i hjältebilden än orten,
					// men enheter under uppbyggnad har ännu ingen adress.
					$noav_hero_loc = '' !== ( $noav_unit['address'] ?? '' )
						? $noav_unit['address']
						: $noav_unit['location'];
					?>
					<?php if ( '' !== $noav_hero_loc ) : ?>
						<p class="hero-loc"><?php echo esc_html( $noav_hero_loc ); ?></p>
					<?php endif; ?>

					<?php if ( has_excerpt() ) : ?>
						<p class="hero-sub"><?php echo esc_html( (string) get_the_excerpt() ); ?></p>
					<?php endif; ?>

					<div class="hero-ctas">
						<a class="btn btn--accent" href="<?php echo esc_url( $noav_cta ); ?>">
							<?php esc_html_e( 'Platsförfrågan', 'noav' ); ?>
						</a>
						<a class="btn btn--light" href="#om"><?php esc_html_e( 'Läs om enheten', 'noav' ); ?></a>
					</div>
				</div>

				<?php echo noav_render_unit_availability(); // phpcs:ignore WordPress.Security.EscapeOutput -- escapas internt. ?>
			</div>
		</div>

		<a class="scroll-cue" href="#om" aria-label="<?php esc_attr_e( 'Bläddra ner till information om enheten', 'noav' ); ?>">
			<span><?php esc_html_e( 'Om enheten', 'noav' ); ?></span>
			<span class="cue-line" aria-hidden="true"></span>
		</a>
	</section>

	<?php
	the_content();
endwhile;

get_footer();
