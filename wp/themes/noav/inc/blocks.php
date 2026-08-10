<?php
/**
 * Serverrenderade block för platsdata.
 *
 * Fyra block läser antalet lediga platser och kan därför inte vara statiskt
 * innehåll i redigeraren — siffran ska ändras när personalen ändrar den, inte
 * när någon råkar öppna sidan i redigeraren:
 *
 *   noav/availability-grid — korten under "Lediga platser" på startsidan
 *   noav/unit-cards        — enhetskorten under "Våra enheter"
 *   noav/availability-pill — den lilla statusraden i hjältebilden
 *   noav/unit-availability — sifferpanelen i en enhetssidas hjältebild
 *
 * Blocken renderas både på servern och av assets/js/main.js. Server­renderingen
 * gör att siffran finns i HTML-källan — bra för sökmotorer, och besökaren ser
 * aldrig en nolla blinka förbi innan JavaScript hunnit igång.
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'noav_register_blocks' );
/**
 * Registrerar blocken.
 */
function noav_register_blocks(): void {
	$blocks = array(
		'availability-grid' => array(
			'title'       => __( 'Noav: lediga platser (kort)', 'noav' ),
			'description' => __( 'Ett kort per enhet med antal lediga platser, status och datum.', 'noav' ),
			'render'      => 'noav_render_availability_grid',
			'attributes'  => array(),
		),
		'unit-cards'        => array(
			'title'       => __( 'Noav: enhetskort', 'noav' ),
			'description' => __( 'Bildkort som länkar till enheterna, med platsantal som märke.', 'noav' ),
			'render'      => 'noav_render_unit_cards',
			'attributes'  => array(),
		),
		'availability-pill' => array(
			'title'       => __( 'Noav: statusrad', 'noav' ),
			'description' => __( 'Sammanlagt antal lediga platser över samtliga enheter.', 'noav' ),
			'render'      => 'noav_render_availability_pill',
			'attributes'  => array(
				'suffix' => array(
					'type'    => 'string',
					'default' => __( 'just nu — båda enheterna', 'noav' ),
				),
			),
		),
		'unit-availability' => array(
			'title'       => __( 'Noav: platspanel för enhet', 'noav' ),
			'description' => __( 'Stor siffra med lediga platser för den enhet sidan visar.', 'noav' ),
			'render'      => 'noav_render_unit_availability',
			'attributes'  => array(
				'unit' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		),
		'contact-list'      => array(
			'title'       => __( 'Noav: kontaktpersoner', 'noav' ),
			'description' => __( 'Kontaktpersonerna från Inställningar → Noav.', 'noav' ),
			'render'      => 'noav_render_contact_list',
			'attributes'  => array(),
		),
		'unit-contact'      => array(
			'title'       => __( 'Noav: kontaktkort för enhet', 'noav' ),
			'description' => __( 'Enhetens adress och nummer, plus kontaktpersonerna.', 'noav' ),
			'render'      => 'noav_render_unit_contact',
			'attributes'  => array(
				'unit' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		),
	);

	foreach ( $blocks as $name => $config ) {
		register_block_type(
			'noav/' . $name,
			array(
				'api_version'     => 3,
				'title'           => $config['title'],
				'description'     => $config['description'],
				'category'        => 'noav',
				'icon'            => 'clipboard',
				'attributes'      => $config['attributes'],
				'render_callback' => $config['render'],
				'editor_script'   => 'noav-blocks',
				'supports'        => array(
					'html'     => false,
					'reusable' => false,
				),
			)
		);
	}
}

add_filter( 'block_categories_all', 'noav_register_block_category' );
/**
 * Egen blockkategori så att blocken går att hitta i inläggsväljaren.
 *
 * @param array<int,array<string,mixed>> $categories Kategorier.
 * @return array<int,array<string,mixed>>
 */
function noav_register_block_category( array $categories ): array {
	array_unshift(
		$categories,
		array(
			'slug'  => 'noav',
			'title' => __( 'Noav', 'noav' ),
			'icon'  => null,
		)
	);
	return $categories;
}

add_action( 'init', 'noav_register_block_script', 5 );
/**
 * Registrerar redigerarskriptet.
 *
 * Skriptet är skrivet i vanlig JavaScript utan JSX, så temat behöver ingen
 * npm-byggkedja. Förhandsvisningen i redigeraren kommer från ServerSideRender,
 * som anropar samma render_callback som sidan använder.
 */
function noav_register_block_script(): void {
	wp_register_script(
		'noav-blocks',
		NOAV_URI . '/assets/js/blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-server-side-render', 'wp-block-editor', 'wp-components' ),
		NOAV_VERSION,
		true
	);
	wp_set_script_translations( 'noav-blocks', 'noav', NOAV_DIR . '/languages' );
}

/* =========================================================================
   RENDERING
   ========================================================================= */

/**
 * Skriver ut attribut för statusprickar och statustext.
 *
 * @param int $available Antal lediga platser.
 * @return array{key:string,label:string}
 */
function noav_status_attrs( int $available ): array {
	return noav_status( $available );
}

/**
 * Renderar en statusprick.
 *
 * @param string $key       Enhetens slug eller "all".
 * @param int    $available Antal lediga platser.
 */
function noav_dot( string $key, int $available ): string {
	$status = noav_status( $available );
	return sprintf(
		'<span class="avail-dot%s" data-avail-dot="%s" data-status="%s" role="img" aria-label="%s"></span>',
		$available > 0 ? ' is-pulsing' : '',
		esc_attr( $key ),
		esc_attr( $status['key'] ),
		esc_attr( sprintf( /* translators: %s: statustext. */ __( 'Status: %s', 'noav' ), $status['label'] ) )
	);
}

/**
 * Korten under "Lediga platser".
 */
function noav_render_availability_grid(): string {
	$units = noav_get_units();
	if ( empty( $units ) ) {
		return noav_empty_notice( __( 'Inga enheter är publicerade ännu.', 'noav' ) );
	}

	$html = '<div class="avail-grid" data-reveal-group>';

	foreach ( $units as $slug => $unit ) {
		$status = noav_status( $unit['available'] );

		$html .= sprintf(
			'<article class="avail-card" aria-label="%s">',
			esc_attr( sprintf( /* translators: %s: enhetens namn. */ __( 'Lediga platser på %s', 'noav' ), $unit['name'] ) )
		);

		$html .= '<div class="avail-card-head">';
		$html .= '<h3>' . esc_html( $unit['name'] ) . '</h3>';
		if ( '' !== $unit['location'] ) {
			$html .= '<p class="avail-loc">' . esc_html( $unit['location'] ) . '</p>';
		}
		$html .= '</div>';

		$html .= '<p class="avail-number">';
		$html .= sprintf(
			'<span data-avail="%s" data-avail-field="available" data-count data-count-target="%d" aria-live="polite">%d</span>',
			esc_attr( $slug ),
			$unit['available'],
			$unit['available']
		);
		$html .= sprintf(
			'<span class="avail-of">%s <span data-avail="%s" data-avail-field="total">%d</span> %s</span>',
			esc_html__( 'av', 'noav' ),
			esc_attr( $slug ),
			$unit['total'],
			esc_html__( 'platser', 'noav' )
		);
		$html .= '</p>';

		$html .= '<p class="avail-status">';
		$html .= noav_dot( $slug, $unit['available'] );
		$html .= sprintf(
			'<span class="status-text" data-avail="%s" data-avail-field="status" data-status="%s">%s</span>',
			esc_attr( $slug ),
			esc_attr( $status['key'] ),
			esc_html( $status['label'] )
		);
		$html .= '</p>';

		if ( '' !== $unit['updatedAt'] ) {
			$html .= sprintf(
				'<p class="avail-meta">%s <span data-avail="%s" data-avail-field="updatedAt">%s</span></p>',
				esc_html__( 'Uppdaterad', 'noav' ),
				esc_attr( $slug ),
				esc_html( $unit['updatedAt'] )
			);
		}

		$html .= sprintf(
			'<a class="btn btn--ghost" href="%s">%s</a>',
			esc_url( $unit['permalink'] ),
			esc_html__( 'Till enheten →', 'noav' )
		);

		$html .= '</article>';
	}

	return $html . '</div>';
}

/**
 * Enhetskorten under "Våra enheter".
 */
function noav_render_unit_cards(): string {
	$units = noav_get_units();
	if ( empty( $units ) ) {
		return noav_empty_notice( __( 'Inga enheter är publicerade ännu.', 'noav' ) );
	}

	$arrow = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h13"/><path d="m13.5 7 5 5-5 5"/></svg>';
	$html  = '<div class="unit-cards" data-reveal-group>';

	foreach ( $units as $slug => $unit ) {
		$has_photo = '' !== $unit['image'];

		$html .= sprintf( '<a class="unit-card" href="%s">', esc_url( $unit['permalink'] ) );

		$html .= sprintf(
			'<div class="unit-card-visual %s%s"%s>',
			esc_attr( $unit['artClass'] ),
			$has_photo ? ' has-photo' : '',
			// Utan foto är rutan ren dekoration i CSS — då behöver den en
			// egen beskrivning för att inte bli ett tomt hål i uppläsningen.
			$has_photo ? '' : sprintf(
				' role="img" aria-label="%s"',
				esc_attr( sprintf( /* translators: %s: enhetens namn. */ __( 'Dekorativ illustration för %s', 'noav' ), $unit['name'] ) )
			)
		);

		if ( $has_photo ) {
			$html .= sprintf(
				'<img class="media-fill" src="%s" alt="%s" loading="lazy">',
				esc_url( $unit['image'] ),
				esc_attr( $unit['imageAlt'] )
			);
		}

		$html .= '<span class="unit-card-badge">';
		$html .= noav_dot( $slug, $unit['available'] );
		$html .= sprintf(
			'<span><span data-avail="%1$s" data-avail-field="available">%2$d</span> <span data-avail="%1$s" data-avail-field="platser">%3$s</span></span>',
			esc_attr( $slug ),
			$unit['available'],
			esc_html( noav_places_label( $unit['available'] ) )
		);
		$html .= '</span></div>';

		$html .= '<div class="unit-card-body">';
		$html .= '<h3>' . esc_html( $unit['name'] );
		$html .= '<span class="unit-card-arrow" aria-hidden="true">' . $arrow . '</span></h3>';
		if ( '' !== $unit['location'] ) {
			$html .= '<p class="unit-card-loc">' . esc_html( $unit['location'] ) . '</p>';
		}
		if ( '' !== $unit['short'] ) {
			$html .= '<p>' . esc_html( $unit['short'] ) . '</p>';
		}
		$html .= '</div></a>';
	}

	return $html . '</div>';
}

/**
 * Statusraden i startsidans hjältebild.
 *
 * @param array<string,mixed> $attributes Blockattribut.
 */
function noav_render_availability_pill( array $attributes = array() ): string {
	$totals = noav_availability_totals();
	$suffix = (string) ( $attributes['suffix'] ?? __( 'just nu — båda enheterna', 'noav' ) );

	return sprintf(
		'<p class="hero-pill" role="status">%s<span><strong data-avail="all" data-avail-field="available" data-count data-count-target="%d" aria-live="polite">%d</strong> <span data-avail="all" data-avail-field="platser">%s</span> %s</span></p>',
		noav_dot( 'all', $totals['available'] ),
		$totals['available'],
		$totals['available'],
		esc_html( noav_places_label( $totals['available'] ) ),
		esc_html( $suffix )
	);
}

/**
 * Sifferpanelen i en enhetssidas hjältebild.
 *
 * @param array<string,mixed> $attributes Blockattribut.
 */
function noav_render_unit_availability( array $attributes = array() ): string {
	$slug = (string) ( $attributes['unit'] ?? '' );

	// Normalfallet: blocket står på en enhetssida och gäller den enheten.
	if ( '' === $slug ) {
		$post = get_post();
		if ( $post instanceof WP_Post && 'noav_unit' === $post->post_type ) {
			$slug = $post->post_name;
		}
	}

	$unit = '' !== $slug ? noav_get_unit( $slug ) : null;
	if ( null === $unit ) {
		return noav_empty_notice( __( 'Välj vilken enhet panelen gäller i blockets inställningar.', 'noav' ) );
	}

	$status = noav_status( $unit['available'] );

	$html = sprintf(
		'<div class="unit-avail" role="status" aria-label="%s">',
		esc_attr( sprintf( /* translators: %s: enhetens namn. */ __( 'Lediga platser på %s just nu', 'noav' ), $unit['name'] ) )
	);

	$html .= sprintf(
		'<p class="unit-avail-number" aria-live="polite"><span data-avail="%s" data-avail-field="available" data-count data-count-target="%d">%d</span></p>',
		esc_attr( $slug ),
		$unit['available'],
		$unit['available']
	);

	$html .= sprintf(
		'<p class="unit-avail-label"><span data-avail="%s" data-avail-field="platser">%s</span> %s</p>',
		esc_attr( $slug ),
		esc_html( noav_places_label( $unit['available'] ) ),
		esc_html__( 'just nu', 'noav' )
	);

	$html .= '<p class="unit-avail-status">';
	$html .= noav_dot( $slug, $unit['available'] );
	$html .= sprintf(
		'<span class="status-text" data-avail="%s" data-avail-field="status" data-status="%s">%s</span>',
		esc_attr( $slug ),
		esc_attr( $status['key'] ),
		esc_html( $status['label'] )
	);
	$html .= '</p>';

	$html .= '<p class="unit-avail-meta">';
	if ( '' !== $unit['updatedAt'] ) {
		$html .= sprintf(
			'%s <span data-avail="%s" data-avail-field="updatedAt">%s</span> · ',
			esc_html__( 'Uppdaterad', 'noav' ),
			esc_attr( $slug ),
			esc_html( $unit['updatedAt'] )
		);
	}
	$html .= sprintf(
		'<span data-avail="%s" data-avail-field="total">%d</span> %s',
		esc_attr( $slug ),
		$unit['total'],
		esc_html__( 'platser totalt', 'noav' )
	);
	$html .= '</p></div>';

	return $html;
}

/**
 * Kontaktkortet på en enhetssida.
 *
 * @param array<string,mixed> $attributes Blockattribut.
 */
function noav_render_unit_contact( array $attributes = array() ): string {
	$slug = (string) ( $attributes['unit'] ?? '' );
	if ( '' === $slug ) {
		$post = get_post();
		if ( $post instanceof WP_Post && 'noav_unit' === $post->post_type ) {
			$slug = $post->post_name;
		}
	}

	$unit = '' !== $slug ? noav_get_unit( $slug ) : null;
	if ( null === $unit ) {
		return noav_empty_notice( __( 'Välj vilken enhet kontaktkortet gäller i blockets inställningar.', 'noav' ) );
	}

	$html = '<div class="contact-card" data-reveal>';

	$html .= '<address><strong>' . esc_html( $unit['name'] ) . '</strong>';
	foreach ( array( $unit['address'], $unit['location'] ) as $line ) {
		if ( '' !== $line ) {
			$html .= '<br>' . esc_html( $line );
		}
	}
	$html .= '</address>';

	$html .= '<div class="contact-rows">';

	// Enhetens eget nummer först, sedan de gemensamma kontaktpersonerna.
	$rows = array();
	if ( '' !== $unit['phone'] || '' !== $unit['email'] ) {
		$rows[] = array(
			'role'  => $unit['name'],
			'name'  => __( 'Enhetstelefon', 'noav' ),
			'phone' => $unit['phone'],
			'email' => $unit['email'],
		);
	}
	$rows = array_merge( $rows, noav_contacts() );

	foreach ( $rows as $row ) {
		$html .= '<div class="contact-row">';
		$html .= '<span class="who">' . esc_html( $row['name'] );
		if ( '' !== $row['role'] ) {
			$html .= '<small>' . esc_html( $row['role'] ) . '</small>';
		}
		$html .= '</span>';

		$tel = noav_tel_href( $row['phone'] );
		if ( '' !== $tel ) {
			$html .= sprintf(
				'<a href="%s">%s</a>',
				esc_url( 'tel:' . $tel ),
				esc_html( $row['phone'] )
			);
		}

		if ( '' !== $row['email'] ) {
			$html .= sprintf(
				'<span class="mail"><a href="%s">%s</a></span>',
				esc_url( 'mailto:' . $row['email'] ),
				esc_html( $row['email'] )
			);
		}
		$html .= '</div>';
	}

	$html .= '</div>';

	$tel = noav_tel_href( '' !== $unit['phone'] ? $unit['phone'] : (string) noav_setting( 'main_phone', '' ) );
	if ( '' !== $tel ) {
		$html .= sprintf(
			'<a class="btn btn--accent" href="%s">%s</a>',
			esc_url( 'tel:' . $tel ),
			esc_html__( 'Ring oss om platsförfrågan', 'noav' )
		);
	}

	return $html . '</div>';
}

/**
 * Kontaktpersonerna i sektionen för platsförfrågan.
 *
 * Läser Inställningar → Noav, så att ett ändrat nummer slår igenom här och
 * i sidfoten samtidigt.
 */
function noav_render_contact_list(): string {
	$contacts = noav_contacts();
	if ( empty( $contacts ) ) {
		return noav_empty_notice( __( 'Lägg till kontaktpersoner under Inställningar → Noav.', 'noav' ) );
	}

	$html = '<div class="cta-contacts" data-reveal-group>';

	foreach ( $contacts as $contact ) {
		$html .= '<div class="cta-contact">';

		if ( '' !== $contact['role'] ) {
			$html .= '<p class="role">' . esc_html( $contact['role'] ) . '</p>';
		}
		if ( '' !== $contact['name'] ) {
			$html .= '<p class="name">' . esc_html( $contact['name'] ) . '</p>';
		}

		$tel = noav_tel_href( $contact['phone'] );
		if ( '' !== $tel ) {
			$html .= sprintf(
				'<a href="%s">%s</a>',
				esc_url( 'tel:' . $tel ),
				esc_html( $contact['phone'] )
			);
		}

		if ( '' !== $contact['email'] ) {
			$html .= sprintf(
				'<span class="mail"><a href="%s">%s</a></span>',
				esc_url( 'mailto:' . $contact['email'] ),
				esc_html( $contact['email'] )
			);
		}

		$html .= '</div>';
	}

	return $html . '</div>';
}

/**
 * Meddelande som bara visas för redaktörer, aldrig för besökare.
 *
 * @param string $message Texten.
 */
function noav_empty_notice( string $message ): string {
	if ( ! is_admin() && ! current_user_can( 'edit_posts' ) ) {
		return '';
	}
	return '<p class="noav-editor-notice">' . esc_html( $message ) . '</p>';
}
