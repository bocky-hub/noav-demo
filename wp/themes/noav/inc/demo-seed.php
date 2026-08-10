<?php
/**
 * Demoinnehåll.
 *
 * Fyller en tom installation med Noavs två enheter, startsidan, menyn och
 * kontaktuppgifterna. Motsvarar bin/seed.sh, men i PHP — WordPress Playground
 * kör i webbläsaren och har inget wp-cli att anropa.
 *
 * Körs bara när temat aktiveras OCH ingen enhet finns sedan tidigare. På en
 * installation som redan har innehåll är det alltså verkningslöst: ingen ska
 * riskera att få demotexter påklistrade över sin riktiga sajt.
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NOAV_SEED_FLAG = 'noav_pending_seed';

add_action( 'after_switch_theme', 'noav_flag_seed' );
/**
 * Markerar att innehållet ska sättas upp.
 *
 * Själva arbetet sker på init, inte här: blockmönstren registreras på init,
 * och startsidans innehåll hämtas från ett mönster.
 */
function noav_flag_seed(): void {
	update_option( NOAV_SEED_FLAG, '1' );
}

add_action( 'init', 'noav_maybe_seed', 99 );
/**
 * Sätter upp demoinnehållet om det behövs.
 */
function noav_maybe_seed(): void {
	if ( '1' !== (string) get_option( NOAV_SEED_FLAG, '' ) ) {
		return;
	}
	// Ta bort flaggan först. Skulle något gå fel nedan är ett halvfyllt
	// innehåll bättre än en aktivering som försöker om vid varje sidvisning.
	delete_option( NOAV_SEED_FLAG );

	if ( noav_units_exist() ) {
		return; // Sajten har redan enheter — rör inget.
	}

	noav_seed_content();
}

/**
 * Finns det någon enhet redan?
 *
 * Frågar databasen direkt istället för via noav_get_units(), som har en
 * statisk cache per anrop. Cachen kan ha fyllts som tom innan seedingen
 * körde — och en seed som tror att sajten är tom när den inte är det skapar
 * enheterna en gång till. Symptomet blir dubbla platsantal.
 */
function noav_units_exist(): bool {
	$existing = get_posts(
		array(
			'post_type'        => 'noav_unit',
			'post_status'      => 'any',
			'numberposts'      => 1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);

	return ! empty( $existing );
}

/**
 * Skapar allt demoinnehåll.
 */
function noav_seed_content(): void {
	$images = noav_seed_images();

	$vinkelviken = noav_seed_unit(
		array(
			'slug'    => 'vinkelviken',
			'title'   => 'Vinkelvikens HVB',
			'excerpt' => 'Ett hemlikt behandlingshem för ungdomar 13–17 år, med tydlig struktur, hög personaltäthet och en lugn miljö nära naturen.',
			'order'   => 1,
			'thumb'   => $images['vinkelviken-entre'] ?? 0,
			'pattern' => 'noav/enhet-vinkelviken',
			'meta'    => array(
				'total'     => 7,
				'available' => 2,
				'updated'   => '2026-07-07',
				'location'  => 'Hörby, Skåne',
				'address'   => '',
				'phone'     => '',
				'email'     => '',
				'art_class' => 'art-vinkelviken',
				'short'     => 'Ett hemlikt HVB med sju platser, psykolog knuten till verksamheten och en aktiv vardag med skola, friluftsliv och ridning. Här bor ungdomarna i en lugn miljö med tydliga rutiner och vuxna som alltid finns nära.',
			),
		)
	);

	$kyrkhult = noav_seed_unit(
		array(
			'slug'    => 'kyrkhult',
			'title'   => 'Kyrkhults HVB',
			'excerpt' => 'Noav AB:s enhet i Blekinge — ett hemlikt behandlingshem för ungdomar 13–17 år, byggt på samma metodik och kvalitetsarbete som Vinkelviken.',
			'order'   => 2,
			'thumb'   => 0,
			'pattern' => 'noav/enhet-kyrkhult',
			'meta'    => array(
				'total'     => 6,
				'available' => 1,
				'updated'   => '2026-07-07',
				'location'  => 'Olofströms kommun, Blekinge',
				'address'   => '',
				'phone'     => '',
				'email'     => '',
				'art_class' => 'art-kyrkhult',
				'short'     => 'Vår enhet i Blekinge tar emot ungdomar med samma målgrupp och metodik som Vinkelviken, i en naturnära miljö mitt i skogsbygden. Enhetssidan byggs ut i takt med att verksamheten etableras.',
			),
		)
	);

	noav_seed_options();
	$home = noav_seed_front_page();
	noav_seed_menu( $home, array( $vinkelviken, $kyrkhult ) );

	// Enheterna läses via en cache som fylldes innan de fanns.
	wp_cache_flush();
}

/**
 * Lägger temats bilder i mediabiblioteket.
 *
 * @return array<string,int> Filnamn utan ändelse => bilage-ID.
 */
function noav_seed_images(): array {
	$files = array(
		'hero-skymning'        => array( 'Skymning över landskapet', '' ),
		'vinkelviken-entre'    => array( 'Vinkelvikens entré', 'Entrén till Vinkelvikens HVB' ),
		'vinkelviken-huset'    => array( 'Vinkelvikens hus', 'Vinkelvikens HVB — vitt tegelhus med trädgård och trästaket' ),
		'vinkelviken-bullar'   => array( 'Kanelbullar i köket', 'Nybakade kanelbullar på en plåt i Vinkelvikens kök' ),
		'vinkelviken-sovrum'   => array( 'Ungdomsrum', 'Ett av ungdomarnas rum — säng, skrivbord och fönster mot grönskan' ),
		'vinkelviken-kok'      => array( 'Köket', 'Ljust kök med utgång mot trädgården' ),
		'vinkelviken-allrum'   => array( 'Allrummet', 'Gemensamt allrum med matbord och sittgrupp' ),
		'vinkelviken-odling'   => array( 'Odlingslådor', 'Odlingslådor i trädgården på Vinkelviken' ),
	);

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$ids = array();
	foreach ( $files as $name => list( $title, $alt ) ) {
		$source = NOAV_DIR . '/assets/img/' . $name . '.jpg';
		if ( ! file_exists( $source ) ) {
			continue;
		}

		$contents = file_get_contents( $source ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- lokal temafil.
		if ( false === $contents ) {
			continue;
		}

		$upload = wp_upload_bits( $name . '.jpg', null, $contents );
		if ( ! empty( $upload['error'] ) ) {
			continue;
		}

		$id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => $title,
				'post_name'      => $name,
				'post_status'    => 'inherit',
			),
			$upload['file']
		);
		if ( is_wp_error( $id ) || 0 === $id ) {
			continue;
		}

		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
		if ( '' !== $alt ) {
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		}
		$ids[ $name ] = (int) $id;
	}

	return $ids;
}

/**
 * Skapar en enhet.
 *
 * @param array<string,mixed> $unit Uppgifter om enheten.
 * @return int Post-ID, eller 0 om det misslyckades.
 */
function noav_seed_unit( array $unit ): int {
	// Bältet utöver hängslena: en enhet med den här slugen får bara finnas
	// en gång, oavsett hur seedingen anropades.
	$existing = get_posts(
		array(
			'post_type'     => 'noav_unit',
			'post_status'   => 'any',
			'name'          => $unit['slug'],
			'numberposts'   => 1,
			'fields'        => 'ids',
			'no_found_rows' => true,
		)
	);
	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'noav_unit',
			'post_status'  => 'publish',
			'post_title'   => $unit['title'],
			'post_name'    => $unit['slug'],
			'post_excerpt' => $unit['excerpt'],
			'menu_order'   => $unit['order'],
			'post_content' => noav_pattern_content( $unit['pattern'] ),
		),
		true
	);

	if ( is_wp_error( $id ) || 0 === $id ) {
		return 0;
	}

	foreach ( $unit['meta'] as $name => $value ) {
		update_post_meta( $id, NOAV_META[ $name ], $value );
	}

	if ( 0 !== $unit['thumb'] ) {
		set_post_thumbnail( $id, $unit['thumb'] );
	}

	return (int) $id;
}

/**
 * Skapar startsidan och pekar ut den som förstasida.
 *
 * @return int Post-ID.
 */
function noav_seed_front_page(): int {
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Start',
			'post_name'    => 'start',
			'post_content' => noav_pattern_content( 'noav/startsida' ),
		),
		true
	);

	if ( is_wp_error( $id ) || 0 === $id ) {
		return 0;
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $id );

	return (int) $id;
}

/**
 * Hämtar ett blockmönsters innehåll.
 *
 * Mönstret expanderas till riktig blockmarkup istället för att sparas som en
 * referens. Skillnaden syns i redigeraren: expanderade block går att klicka i
 * och skriva om, en referens gör det inte.
 *
 * @param string $slug Mönstrets slug.
 */
function noav_pattern_content( string $slug ): string {
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	return is_array( $pattern ) ? (string) $pattern['content'] : '';
}

/**
 * Bygger huvudmenyn.
 *
 * @param int             $home  Startsidans ID.
 * @param array<int,int>  $units Enheternas ID:n.
 */
function noav_seed_menu( int $home, array $units ): void {
	$menu_id = wp_create_nav_menu( 'Huvudmeny' );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'  => 'Hem',
			'menu-item-url'    => home_url( '/' ),
			'menu-item-type'   => 'custom',
			'menu-item-status' => 'publish',
		)
	);

	foreach ( $units as $unit_id ) {
		if ( 0 === $unit_id ) {
			continue;
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => str_replace( 's HVB', '', get_the_title( $unit_id ) ),
				'menu-item-object'    => 'noav_unit',
				'menu-item-object-id' => $unit_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}

	foreach ( array( 'behandling' => 'Behandling', 'malgrupp' => 'Målgrupp', 'kontakt' => 'Kontakt' ) as $anchor => $label ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $label,
				'menu-item-url'    => home_url( '/#' . $anchor ),
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			)
		);
	}

	$locations           = (array) get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Sparar organisations- och kontaktuppgifterna.
 */
function noav_seed_options(): void {
	update_option(
		NOAV_OPTION,
		array(
			'org_number'      => '559452-7045',
			'tagline'         => 'HVB-hem för ungdomar 13–17 år, med lågaffektivt bemötande och evidensbaserade metoder.',
			'main_phone'      => '',
			'general_email'   => '',
			'copyright'       => 'Alla rättigheter förbehållna.',
			'compliance'      => 'Kvalitetsledningssystem enligt SOSFS 2011:9 · Kollektivavtal via Vårdföretagarna',
			'show_demo_badge' => true,
			'instagram'       => '',
			'instagram_label' => 'Instagram — @noavab',
			'facebook'        => '',
			'linkedin'        => '',
			// Namn och nummer är platshållare. Verkliga kontaktuppgifter fylls
			// i under Inställningar → Noav på den sajt som ska publiceras —
			// inte här i koden, som ligger i ett publikt repo.
			'contacts'        => array(
				array( 'role' => 'Verksamhetschef', 'name' => '[Namn kompletteras]', 'phone' => '', 'email' => '' ),
				array( 'role' => 'Vinkelvikens HVB — Hörby', 'name' => '[Nummer kompletteras]', 'phone' => '', 'email' => '' ),
				array( 'role' => 'Kyrkhults HVB — Olofström', 'name' => '[Nummer kompletteras]', 'phone' => '', 'email' => '' ),
			),
		)
	);

	update_option( 'blogname', 'Noav AB' );
	update_option( 'blogdescription', 'HVB-hem för ungdomar 13–17 år i Hörby och Olofström' );
	update_option( 'timezone_string', 'Europe/Stockholm' );
	update_option( 'date_format', 'Y-m-d' );
	update_option( 'permalink_structure', '/%postname%/' );

	// Kommentarer hör inte hemma på en verksamhetssajt av det här slaget.
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );

	flush_rewrite_rules();
}
