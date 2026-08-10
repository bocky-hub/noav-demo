<?php
/**
 * Blockmönster.
 *
 * Själva mönstren ligger som filer i /patterns och registreras automatiskt
 * av WordPress. Den här filen skapar bara kategorierna de sorteras under,
 * så att listan i inläggsväljaren blir läsbar.
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'noav_register_pattern_categories' );
/**
 * Registrerar kategorier för temats blockmönster.
 */
function noav_register_pattern_categories(): void {
	$categories = array(
		'noav-sections' => array(
			'label'       => __( 'Noav — sektioner', 'noav' ),
			'description' => __( 'Färdiga sektioner i sajtens formspråk. Sätt ihop en sida av flera.', 'noav' ),
		),
		'noav-pages'    => array(
			'label'       => __( 'Noav — hela sidor', 'noav' ),
			'description' => __( 'Kompletta sidor att utgå från.', 'noav' ),
		),
	);

	foreach ( $categories as $slug => $args ) {
		register_block_pattern_category( $slug, $args );
	}
}

/* =========================================================================
   DELAR SOM ÅTERANVÄNDS AV MÖNSTREN
   =========================================================================
   Mönsterfilerna i /patterns är PHP och körs när de registreras. Det som
   återkommer mellan dem bor här istället för att kopieras — sex identiska
   metodkort i två filer blir sex ställen att glömma en rättning på.
   ========================================================================= */

/** Ikoner som används av flera mönster. */
const NOAV_ICON_PSYCH     = '<path d="M13.5 3.6a6.6 6.6 0 0 1 6.3 6.6c0 1.8-.7 3.3-1.8 4.4v5.6"/><path d="M13.5 3.6a6.6 6.6 0 0 0-6.6 6.5c0 .8-.3 1.6-.8 2.4l-.8 1.3c-.3.5-.1 1.1.5 1.3l1.6.4v2.1c0 .9.7 1.6 1.6 1.6h2.1v2"/><circle cx="13" cy="10.5" r="1.4"/>';
const NOAV_ICON_CLIPBOARD = '<rect x="5.5" y="4.8" width="13" height="15.7" rx="2"/><path d="M9.3 4.8a2.7 2.7 0 0 1 5.4 0"/><path d="M9 10.3h6M9 13.6h6M9 16.9h3.5"/>';
const NOAV_ICON_CROSS     = '<rect x="4.5" y="4.5" width="15" height="15" rx="3.5"/><path d="M12 8.5v7M8.5 12h7"/>';
const NOAV_ICON_CHAT      = '<path d="M4.5 6.5h8.5a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H9.2l-3.2 2.9v-2.9h-1.5a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2Z"/><path d="M17.5 9.5h1a2 2 0 0 1 2 2v2.6a2 2 0 0 1-2 2h-.4v2.6l-2.9-2.6h-1.7"/>';
const NOAV_ICON_SHIELD    = '<path d="M12 3.8 5.5 6.1v5.2c0 4.2 2.6 7.3 6.5 8.9 3.9-1.6 6.5-4.7 6.5-8.9V6.1L12 3.8Z"/><path d="m9.2 11.7 2 2 3.6-3.9"/>';
const NOAV_ICON_ARROW     = '<circle cx="12" cy="12" r="8"/><path d="M8.5 12h6.8"/><path d="m12.8 9.5 2.5 2.5-2.5 2.5"/>';

/**
 * Skriver ut ett metod- eller stödkort som blockmarkup.
 *
 * @param string $svg   Ikonens SVG-innehåll (utan svg-elementet).
 * @param string $title Rubrik.
 * @param string $text  Brödtext.
 */
function noav_pattern_card( string $svg, string $title, string $text ): void {
	?>
<!-- wp:group {"tagName":"article","className":"method-card","layout":{"type":"default"}} -->
<article class="wp-block-group method-card">
<!-- wp:html -->
<span class="icon-badge" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput -- fast SVG från konstant. ?></svg></span>
<!-- /wp:html -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $title ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $text ); ?></p>
<!-- /wp:paragraph -->
</article>
<!-- /wp:group -->
	<?php
}

/**
 * Sektionen "Behandling & metoder" med metodchipsen.
 *
 * Identisk på båda enhetssidorna — samma personal utbildas i samma metoder.
 */
function noav_pattern_methods_section(): void {
	$chips = array(
		'rePulse / reManga' => 'impulskontroll',
		'ART'               => 'ilskekontroll &amp; social färdighet',
		'ÅP'                => 'återfallsprevention',
		'Connect'           => 'anknytningsbaserat',
		'FIT'               => 'feedbackinformerad behandling',
		'MI'                => 'motiverande samtal',
		'TMO'               => 'traumamedveten omsorg',
		'Lågaffektivt bemötande' => '',
	);

	$html = '';
	foreach ( $chips as $name => $note ) {
		$html .= '<span class="chip">' . esc_html( $name );
		if ( '' !== $note ) {
			$html .= ' <small>' . $note . '</small>';
		}
		$html .= '</span>';
	}
	?>
<!-- wp:group {"tagName":"section","className":"section section--tint","anchor":"behandling","layout":{"type":"default"}} -->
<section class="wp-block-group section section--tint" id="behandling">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Behandling &amp; metoder</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Struktur som skapar förändring</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede">Varje behandling utformas individuellt utifrån socialtjänstens uppdrag. Grunden är alltid densamma: tydlighet, förutsägbarhet och trygghet — för det är genom goda relationer och egen delaktighet som förändring blir möjlig. Personalen är utbildad i följande metoder:</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:html -->
<div class="chip-grid"><?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- byggd med esc_html ovan. ?></div>
<!-- /wp:html -->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
	<?php
}

/**
 * Kontaktsektionen på en enhetssida.
 *
 * @param string $heading Rubrik.
 * @param string $lede    Inledande stycke.
 * @param string $unit    Enhetens slug.
 */
function noav_pattern_contact_section( string $heading, string $lede, string $unit ): void {
	?>
<!-- wp:group {"tagName":"section","className":"section","anchor":"kontakt","layout":{"type":"default"}} -->
<section class="wp-block-group section" id="kontakt">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Kontakt</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html( $heading ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede"><?php echo esc_html( $lede ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:noav/unit-contact {"unit":"<?php echo esc_attr( $unit ); ?>"} /-->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
	<?php
}
