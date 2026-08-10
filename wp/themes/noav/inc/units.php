<?php
/**
 * Enheter och lediga platser — sajtens datakälla.
 *
 * Varje HVB-enhet är en post av typen `noav_unit`. Postens slug (post_name)
 * är den nyckel som markup och JavaScript använder, t.ex. `vinkelviken`.
 * Byter man slug byter man alltså nyckel — därför är slugen låst i
 * redigeraren för de två enheter som seedas.
 *
 * Antalet lediga platser redigeras i en metabox på enheten. Det är den enda
 * plats i systemet där siffran bor: både serverrenderad markup och
 * `window.NOAV_AVAILABILITY` (som assets/js/main.js läser) matas härifrån.
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Metanycklar. Underscore-prefix = döljs från standardfält i redigeraren. */
const NOAV_META = array(
	'total'        => '_noav_total',
	'available'    => '_noav_available',
	'updated'      => '_noav_available_updated',
	'location'     => '_noav_location',
	'address'      => '_noav_address',
	'phone'        => '_noav_phone',
	'email'        => '_noav_email',
	'short'        => '_noav_short',
	'art_class'    => '_noav_art_class',
);

/* =========================================================================
   1. INNEHÅLLSTYP
   ========================================================================= */

add_action( 'init', 'noav_register_unit_post_type' );
/**
 * Registrerar innehållstypen för enheter.
 */
function noav_register_unit_post_type(): void {
	register_post_type(
		'noav_unit',
		array(
			'labels'        => array(
				'name'               => __( 'Enheter', 'noav' ),
				'singular_name'      => __( 'Enhet', 'noav' ),
				'add_new_item'       => __( 'Lägg till ny enhet', 'noav' ),
				'edit_item'          => __( 'Redigera enhet', 'noav' ),
				'view_item'          => __( 'Visa enhet', 'noav' ),
				'search_items'       => __( 'Sök enheter', 'noav' ),
				'not_found'          => __( 'Inga enheter hittades', 'noav' ),
				'menu_name'          => __( 'Enheter', 'noav' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'menu_icon'     => 'dashicons-admin-home',
			'menu_position' => 20,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'custom-fields' ),
			'rewrite'       => array( 'slug' => 'enheter', 'with_front' => false ),
			'show_in_rest'  => true,
			'rest_base'     => 'units',
			// Blockredigeraren behöver en mall så att enhetssidan börjar
			// med rätt sektioner istället för ett tomt fält.
			'template'      => array(
				array( 'core/pattern', array( 'slug' => 'noav/enhet-innehall' ) ),
			),
		)
	);
}

/* =========================================================================
   2. METAFÄLT
   ========================================================================= */

add_action( 'init', 'noav_register_unit_meta' );
/**
 * Registrerar metafälten så att de går att läsa via REST och blockredigeraren.
 */
function noav_register_unit_meta(): void {
	$fields = array(
		NOAV_META['total']     => array( 'integer', __( 'Totalt antal platser', 'noav' ) ),
		NOAV_META['available'] => array( 'integer', __( 'Lediga platser just nu', 'noav' ) ),
		NOAV_META['updated']   => array( 'string', __( 'Platsantalet uppdaterat', 'noav' ) ),
		NOAV_META['location']  => array( 'string', __( 'Ort', 'noav' ) ),
		NOAV_META['address']   => array( 'string', __( 'Gatuadress', 'noav' ) ),
		NOAV_META['phone']     => array( 'string', __( 'Telefonnummer', 'noav' ) ),
		NOAV_META['email']     => array( 'string', __( 'E-postadress', 'noav' ) ),
		NOAV_META['short']     => array( 'string', __( 'Kort beskrivning', 'noav' ) ),
		NOAV_META['art_class'] => array( 'string', __( 'Gradient-variant', 'noav' ) ),
	);

	foreach ( $fields as $key => list( $type, $label ) ) {
		register_post_meta(
			'noav_unit',
			$key,
			array(
				'type'              => $type,
				'description'       => $label,
				'single'            => true,
				'default'           => 'integer' === $type ? 0 : '',
				'show_in_rest'      => true,
				'sanitize_callback' => 'integer' === $type ? 'absint' : 'sanitize_text_field',
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			)
		);
	}
}

/* =========================================================================
   3. METABOX — DET PERSONALEN FAKTISKT ANVÄNDER
   ========================================================================= */

add_action( 'add_meta_boxes', 'noav_add_unit_meta_box' );
/**
 * Lägger till rutan för platsantal och enhetsfakta.
 */
function noav_add_unit_meta_box(): void {
	add_meta_box(
		'noav_unit_facts',
		__( 'Lediga platser & enhetsfakta', 'noav' ),
		'noav_render_unit_meta_box',
		'noav_unit',
		'side',
		'high'
	);
}

/**
 * Renderar metaboxen.
 *
 * @param WP_Post $post Enheten som redigeras.
 */
function noav_render_unit_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'noav_save_unit', 'noav_unit_nonce' );

	$total     = (int) get_post_meta( $post->ID, NOAV_META['total'], true );
	$available = (int) get_post_meta( $post->ID, NOAV_META['available'], true );
	$updated   = (string) get_post_meta( $post->ID, NOAV_META['updated'], true );
	$status    = noav_status( $available );

	// Exempeltexterna i fälten är medvetet generiska. Verkliga adresser och
	// nummer hör inte i temats kod — de fylls i här, på sajten.
	$text = array(
		NOAV_META['location'] => array( __( 'Ort', 'noav' ), __( 'Ort, landskap', 'noav' ) ),
		NOAV_META['address']  => array( __( 'Gatuadress', 'noav' ), __( 'Gatan 1, 123 45 Orten', 'noav' ) ),
		NOAV_META['phone']    => array( __( 'Telefon', 'noav' ), __( '0XX-XX XX XX', 'noav' ) ),
		NOAV_META['email']    => array( __( 'E-post', 'noav' ), __( 'enheten@exempel.se', 'noav' ) ),
	);
	?>
	<style>
		.noav-mb p { margin: 0 0 1em; }
		.noav-mb label { display: block; font-weight: 600; margin-bottom: .25em; }
		.noav-mb input[type="number"] { width: 6em; }
		.noav-mb input[type="text"], .noav-mb textarea { width: 100%; }
		.noav-mb-status { padding: .6em .8em; border-radius: 6px; font-size: 12px; line-height: 1.4; }
		.noav-mb-status[data-status="ok"]   { background: #e6f4ea; color: #1a5c32; }
		.noav-mb-status[data-status="warn"] { background: #fdf3e0; color: #7a4c0b; }
		.noav-mb-status[data-status="none"] { background: #f0f0f1; color: #50575e; }
		.noav-mb-hint { color: #646970; font-size: 12px; }
	</style>
	<div class="noav-mb">
		<p>
			<label for="noav-available"><?php esc_html_e( 'Lediga platser just nu', 'noav' ); ?></label>
			<input type="number" id="noav-available" name="<?php echo esc_attr( NOAV_META['available'] ); ?>"
				value="<?php echo esc_attr( (string) $available ); ?>" min="0" step="1">
		</p>
		<p>
			<label for="noav-total"><?php esc_html_e( 'Totalt antal platser', 'noav' ); ?></label>
			<input type="number" id="noav-total" name="<?php echo esc_attr( NOAV_META['total'] ); ?>"
				value="<?php echo esc_attr( (string) $total ); ?>" min="0" step="1">
			<span class="noav-mb-hint"><?php esc_html_e( 'Tillståndsgivet antal platser.', 'noav' ); ?></span>
		</p>
		<p class="noav-mb-status" data-status="<?php echo esc_attr( $status['key'] ); ?>">
			<strong><?php echo esc_html( $status['label'] ); ?></strong><br>
			<?php
			if ( '' !== $updated ) {
				printf(
					/* translators: %s: datum i formatet ÅÅÅÅ-MM-DD. */
					esc_html__( 'Platsantalet uppdaterades %s.', 'noav' ),
					esc_html( $updated )
				);
			} else {
				esc_html_e( 'Datumet sätts automatiskt när du ändrar antalet lediga platser.', 'noav' );
			}
			?>
		</p>
		<hr>
		<?php foreach ( $text as $key => list( $label, $placeholder ) ) : ?>
			<p>
				<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
				<input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"
					value="<?php echo esc_attr( (string) get_post_meta( $post->ID, $key, true ) ); ?>"
					placeholder="<?php echo esc_attr( $placeholder ); ?>">
			</p>
		<?php endforeach; ?>
		<p>
			<label for="<?php echo esc_attr( NOAV_META['short'] ); ?>"><?php esc_html_e( 'Kort beskrivning', 'noav' ); ?></label>
			<textarea id="<?php echo esc_attr( NOAV_META['short'] ); ?>" name="<?php echo esc_attr( NOAV_META['short'] ); ?>"
				rows="4"><?php echo esc_textarea( (string) get_post_meta( $post->ID, NOAV_META['short'], true ) ); ?></textarea>
			<span class="noav-mb-hint"><?php esc_html_e( 'Visas på enhetskortet på startsidan.', 'noav' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( NOAV_META['art_class'] ); ?>"><?php esc_html_e( 'Gradient om foto saknas', 'noav' ); ?></label>
			<select id="<?php echo esc_attr( NOAV_META['art_class'] ); ?>" name="<?php echo esc_attr( NOAV_META['art_class'] ); ?>">
				<?php
				$current = (string) get_post_meta( $post->ID, NOAV_META['art_class'], true );
				$options = array(
					'art-vinkelviken' => __( 'Petrol/salvia', 'noav' ),
					'art-kyrkhult'    => __( 'Petrol/skogsgrön', 'noav' ),
				);
				foreach ( $options as $value => $label ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $value ),
						selected( $current, $value, false ),
						esc_html( $label )
					);
				}
				?>
			</select>
			<span class="noav-mb-hint"><?php esc_html_e( 'Används som bakgrund när enheten inte har någon utvald bild.', 'noav' ); ?></span>
		</p>
	</div>
	<?php
}

add_action( 'save_post_noav_unit', 'noav_save_unit_meta', 10, 2 );
/**
 * Sparar metaboxens fält.
 *
 * @param int     $post_id Post-ID.
 * @param WP_Post $post    Posten.
 */
function noav_save_unit_meta( int $post_id, WP_Post $post ): void {
	if ( ! isset( $_POST['noav_unit_nonce'] ) ) {
		return;
	}
	$nonce = sanitize_text_field( wp_unslash( (string) $_POST['noav_unit_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'noav_save_unit' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Totalen först — den behövs för att kunna begränsa antalet lediga.
	$total = isset( $_POST[ NOAV_META['total'] ] )
		? absint( wp_unslash( $_POST[ NOAV_META['total'] ] ) )
		: (int) get_post_meta( $post_id, NOAV_META['total'], true );
	update_post_meta( $post_id, NOAV_META['total'], $total );

	if ( isset( $_POST[ NOAV_META['available'] ] ) ) {
		$previous  = (int) get_post_meta( $post_id, NOAV_META['available'], true );
		$available = absint( wp_unslash( $_POST[ NOAV_META['available'] ] ) );

		// Fler lediga än totala platser är alltid ett skrivfel — kapa tyst
		// istället för att låta sajten visa "9 av 7 platser".
		$available = min( $available, $total );
		update_post_meta( $post_id, NOAV_META['available'], $available );

		if ( $available !== $previous || '' === (string) get_post_meta( $post_id, NOAV_META['updated'], true ) ) {
			update_post_meta( $post_id, NOAV_META['updated'], current_time( 'Y-m-d' ) );
		}
	}

	foreach ( array( 'location', 'address', 'phone', 'email', 'art_class' ) as $name ) {
		$key = NOAV_META[ $name ];
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( (string) $_POST[ $key ] ) ) );
		}
	}

	if ( isset( $_POST[ NOAV_META['short'] ] ) ) {
		update_post_meta(
			$post_id,
			NOAV_META['short'],
			sanitize_textarea_field( wp_unslash( (string) $_POST[ NOAV_META['short'] ] ) )
		);
	}
}

/* =========================================================================
   4. LÄSNING — ETT ENDA STÄLLE
   ========================================================================= */

/**
 * Statuslogik. Speglar getStatus() i assets/js/main.js — ändras den ena
 * måste den andra ändras med, annars kan server och klient visa olika text.
 *
 * @param int $available Antal lediga platser.
 * @return array{key:string,label:string}
 */
function noav_status( int $available ): array {
	if ( $available >= 3 ) {
		return array( 'key' => 'ok', 'label' => __( 'God tillgänglighet', 'noav' ) );
	}
	if ( $available >= 1 ) {
		return array( 'key' => 'warn', 'label' => __( 'Begränsat antal platser', 'noav' ) );
	}
	return array( 'key' => 'none', 'label' => __( 'Inga lediga platser just nu', 'noav' ) );
}

/**
 * Hämtar samtliga enheter som en array.
 *
 * @return array<string,array<string,mixed>> Nycklad på enhetens slug.
 */
function noav_get_units(): array {
	static $cache = null;
	if ( is_array( $cache ) ) {
		return $cache;
	}

	$posts = get_posts(
		array(
			'post_type'        => 'noav_unit',
			'post_status'      => 'publish',
			'numberposts'      => 20,
			'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'suppress_filters' => false,
		)
	);

	$units = array();
	foreach ( $posts as $post ) {
		$total     = (int) get_post_meta( $post->ID, NOAV_META['total'], true );
		$available = min( (int) get_post_meta( $post->ID, NOAV_META['available'], true ), $total );

		$units[ $post->post_name ] = array(
			'id'        => $post->ID,
			'slug'      => $post->post_name,
			'name'      => get_the_title( $post ),
			'permalink' => (string) get_permalink( $post ),
			'total'     => $total,
			'available' => $available,
			'updatedAt' => (string) get_post_meta( $post->ID, NOAV_META['updated'], true ),
			'location'  => (string) get_post_meta( $post->ID, NOAV_META['location'], true ),
			'address'   => (string) get_post_meta( $post->ID, NOAV_META['address'], true ),
			'phone'     => (string) get_post_meta( $post->ID, NOAV_META['phone'], true ),
			'email'     => (string) get_post_meta( $post->ID, NOAV_META['email'], true ),
			'short'     => (string) get_post_meta( $post->ID, NOAV_META['short'], true ),
			'artClass'  => (string) get_post_meta( $post->ID, NOAV_META['art_class'], true ) ?: 'art-vinkelviken',
			'image'     => (string) get_the_post_thumbnail_url( $post, 'full' ),
			'imageAlt'  => (string) get_post_meta( (int) get_post_thumbnail_id( $post ), '_wp_attachment_image_alt', true ),
		);
	}

	$cache = $units;
	return $units;
}

/**
 * Bygger det datapaket som JavaScript läser som window.NOAV_AVAILABILITY.
 *
 * Formen är medvetet identisk med den gamla js/availability.js, så att
 * assets/js/main.js kan behållas i stort sett oförändrad.
 *
 * @return array{updatedAt:string,units:array<string,array{name:string,total:int,available:int,updatedAt:string}>}
 */
function noav_get_availability(): array {
	$units   = array();
	$updated = '';

	foreach ( noav_get_units() as $slug => $unit ) {
		$units[ $slug ] = array(
			'name'      => $unit['name'],
			'total'     => $unit['total'],
			'available' => $unit['available'],
			'updatedAt' => $unit['updatedAt'],
		);
		// Sajtens samlade "uppdaterad"-datum är det senaste av enheternas.
		if ( $unit['updatedAt'] > $updated ) {
			$updated = $unit['updatedAt'];
		}
	}

	return array(
		'updatedAt' => $updated,
		'units'     => $units,
	);
}

/**
 * Slår upp en enhet på slug.
 *
 * @param string $slug Enhetens slug, t.ex. "vinkelviken".
 * @return array<string,mixed>|null
 */
function noav_get_unit( string $slug ): ?array {
	return noav_get_units()[ $slug ] ?? null;
}

/**
 * Summerar lediga och totala platser över alla enheter.
 *
 * @return array{available:int,total:int}
 */
function noav_availability_totals(): array {
	$available = 0;
	$total     = 0;
	foreach ( noav_get_units() as $unit ) {
		$available += $unit['available'];
		$total     += $unit['total'];
	}
	return array( 'available' => $available, 'total' => $total );
}

/**
 * Böjer "ledig plats" / "lediga platser" efter antal.
 *
 * @param int $available Antal lediga platser.
 */
function noav_places_label( int $available ): string {
	return 1 === $available
		? __( 'ledig plats', 'noav' )
		: __( 'lediga platser', 'noav' );
}

/* =========================================================================
   5. ADMINLISTA — SE ALLA PLATSANTAL PÅ ETT STÄLLE
   ========================================================================= */

add_filter( 'manage_noav_unit_posts_columns', 'noav_unit_columns' );
/**
 * Lägger till kolumner för platsantal i enhetslistan.
 *
 * @param array<string,string> $columns Befintliga kolumner.
 * @return array<string,string>
 */
function noav_unit_columns( array $columns ): array {
	$date = $columns['date'] ?? null;
	unset( $columns['date'] );

	$columns['noav_available'] = __( 'Lediga platser', 'noav' );
	$columns['noav_status']    = __( 'Status', 'noav' );
	$columns['noav_updated']   = __( 'Platsantal uppdaterat', 'noav' );

	if ( null !== $date ) {
		$columns['date'] = $date;
	}
	return $columns;
}

add_action( 'manage_noav_unit_posts_custom_column', 'noav_unit_column_content', 10, 2 );
/**
 * Fyller de egna kolumnerna.
 *
 * @param string $column  Kolumnnyckel.
 * @param int    $post_id Post-ID.
 */
function noav_unit_column_content( string $column, int $post_id ): void {
	$available = (int) get_post_meta( $post_id, NOAV_META['available'], true );

	switch ( $column ) {
		case 'noav_available':
			printf(
				'%d / %d',
				$available,
				(int) get_post_meta( $post_id, NOAV_META['total'], true )
			);
			break;
		case 'noav_status':
			echo esc_html( noav_status( $available )['label'] );
			break;
		case 'noav_updated':
			$updated = (string) get_post_meta( $post_id, NOAV_META['updated'], true );
			echo esc_html( '' !== $updated ? $updated : '—' );
			break;
	}
}
