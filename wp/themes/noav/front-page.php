<?php
/**
 * Startsida.
 *
 * Hela innehållet kommer från sidans block, så att varje rubrik och stycke
 * kan ändras i wp-admin. Mallen bidrar bara med sidhuvud och sidfot.
 *
 * Har ingen sida satts som startsida (Inställningar → Läsning) faller
 * WordPress tillbaka på senaste inlägg — då visas ett meddelande till
 * inloggad personal istället för en tom sida.
 *
 * @package Noav
 */

declare( strict_types = 1 );

get_header();

if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
	the_post();
	the_content();
} else {
	?>
	<section class="section">
		<div class="container">
			<div class="section-head">
				<h1><?php bloginfo( 'name' ); ?></h1>
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<p class="lede">
						<?php esc_html_e( 'Ingen startsida är vald ännu. Gå till Inställningar → Läsning och välj vilken sida som ska visas på startsidan.', 'noav' ); ?>
					</p>
					<a class="btn btn--accent" href="<?php echo esc_url( admin_url( 'options-reading.php' ) ); ?>">
						<?php esc_html_e( 'Öppna läsinställningar', 'noav' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

get_footer();
