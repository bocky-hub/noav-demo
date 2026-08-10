<?php
/**
 * Temauppsättning: stöd, menyer, stilar och skript.
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'noav_theme_setup' );
/**
 * Registrerar temats stöd och menyplatser.
 */
function noav_theme_setup(): void {
	load_theme_textdomain( 'noav', NOAV_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	// Designsystemet i assets/css/styles.css ska gälla även inne i
	// blockredigeraren, annars ser redaktören inte vad hon bygger.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/styles.css' );

	// Sajtens färger och typografi kommer från styles.css, inte från
	// blockredigerarens egna verktyg. Att stänga av dem hindrar att en
	// redaktör råkar sätta en färg som faller utanför designsystemet.
	add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'disable-custom-font-sizes' );
	add_theme_support( 'disable-custom-gradients' );

	register_nav_menus(
		array(
			'primary' => __( 'Huvudmeny', 'noav' ),
			'footer'  => __( 'Sidfot — enheter & socialt', 'noav' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'noav_enqueue_assets' );
/**
 * Köar stilar och skript.
 *
 * Animationsbiblioteken laddas från jsDelivr precis som i den statiska
 * demon. main.js är byggd för att klara att de uteblir: utan GSAP visas
 * allt innehåll i CSS-grundläget och siffrorna är redan renderade.
 */
function noav_enqueue_assets(): void {
	wp_enqueue_style(
		'noav-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,550;9..144,600&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'noav-styles',
		NOAV_URI . '/assets/css/styles.css',
		array( 'noav-fonts' ),
		NOAV_VERSION
	);

	wp_enqueue_script( 'noav-gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'noav-scrolltrigger', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js', array( 'noav-gsap' ), '3.12.5', true );
	wp_enqueue_script( 'noav-lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.14/dist/lenis.min.js', array(), '1.1.14', true );

	wp_enqueue_script(
		'noav-main',
		NOAV_URI . '/assets/js/main.js',
		array( 'noav-gsap', 'noav-scrolltrigger', 'noav-lenis' ),
		NOAV_VERSION,
		true
	);

	// Platsdatan injiceras som JSON — inte via wp_localize_script, som
	// gör om alla värden till strängar. main.js summerar siffrorna, och
	// "2" + "1" hade blivit "21".
	wp_add_inline_script(
		'noav-main',
		'window.NOAV_AVAILABILITY = ' . wp_json_encode( noav_availability_payload() ) . ';',
		'before'
	);
}

/**
 * Bygger JS-paketet med platsdata.
 *
 * @return array<string,mixed>
 */
function noav_availability_payload(): array {
	$payload = noav_get_availability();

	/**
	 * Tillåt att ?vinkelviken=3 i adressfältet skriver över platsantalet.
	 *
	 * Praktiskt vid en demo eller pitch, men olämpligt på en publicerad
	 * sajt: vem som helst kan då skärmdumpa en sida som visar ett antal
	 * lediga platser som inte stämmer. Därför avstängt som standard.
	 *
	 * @param bool $allow Om URL-överstyrning ska tillåtas.
	 */
	$payload['allowUrlOverride'] = (bool) apply_filters( 'noav_allow_url_override', false );

	return $payload;
}

add_action( 'enqueue_block_assets', 'noav_enqueue_editor_assets' );
/**
 * Laddar designsystemet i blockredigerarens iframe.
 *
 * add_editor_style() räcker inte när redigeraren körs i iframe och stilen
 * innehåller egna CSS-variabler på :root — den här raden ser till att
 * redaktören ser samma typografi och färger som besökaren.
 */
function noav_enqueue_editor_assets(): void {
	if ( ! is_admin() ) {
		return;
	}
	wp_enqueue_style(
		'noav-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,550;9..144,600&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap',
		array(),
		null
	);
}

/* =========================================================================
   MENY
   ========================================================================= */

/**
 * Skriver ut huvudmenyn, med temats egen meny som reserv.
 *
 * @param string $location Menyplats.
 * @param string $class    CSS-klass på ankarelementen.
 */
function noav_nav_menu( string $location = 'primary', string $class = '' ): void {
	if ( ! has_nav_menu( $location ) ) {
		noav_fallback_menu( $class );
		return;
	}

	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'link_class'     => $class,
			'walker'         => new Noav_Nav_Walker(),
			'fallback_cb'    => false,
		)
	);
}

add_filter( 'nav_menu_link_attributes', 'noav_nav_link_attributes', 10, 3 );
/**
 * Ger menylänkarna rätt klass och markerar aktuell sida.
 *
 * @param array<string,string> $atts Attribut.
 * @param WP_Post              $item Menypost.
 * @param stdClass             $args Argument från wp_nav_menu().
 * @return array<string,string>
 */
function noav_nav_link_attributes( array $atts, $item, $args ): array {
	if ( ! empty( $args->link_class ) ) {
		$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' ' . $args->link_class );
	}

	// WordPress räknar en egen länk till /#kontakt som "aktuell sida" på
	// startsidan, eftersom jämförelsen bortser från ankaret. Resultatet blir
	// att fyra menyposter markeras samtidigt. En länk till ett avsnitt är
	// aldrig en aktuell sida — hoppa över dem.
	$is_anchor = str_contains( (string) ( $atts['href'] ?? '' ), '#' );

	if ( ! $is_anchor && ( ! empty( $item->current ) || ! empty( $item->current_item_ancestor ) ) ) {
		$atts['aria-current'] = 'page';
	}

	return $atts;
}

/**
 * Temats reservmeny, används innan en meny har byggts i wp-admin.
 *
 * @param string $class CSS-klass på länkarna.
 */
function noav_fallback_menu( string $class = '' ): void {
	$attr  = '' !== $class ? ' class="' . esc_attr( $class ) . '"' : '';
	$items = array( home_url( '/' ) => __( 'Hem', 'noav' ) );

	foreach ( noav_get_units() as $unit ) {
		$items[ $unit['permalink'] ] = $unit['name'];
	}

	$anchor_base = is_front_page() ? '' : home_url( '/' );
	$items[ $anchor_base . '#behandling' ] = __( 'Behandling', 'noav' );
	$items[ $anchor_base . '#malgrupp' ]   = __( 'Målgrupp', 'noav' );
	$items[ $anchor_base . '#kontakt' ]    = __( 'Kontakt', 'noav' );

	foreach ( $items as $url => $label ) {
		printf(
			'<a href="%s"%s>%s</a>',
			esc_url( $url ),
			$attr, // phpcs:ignore WordPress.Security.EscapeOutput -- byggd av esc_attr ovan.
			esc_html( $label )
		);
	}
}
