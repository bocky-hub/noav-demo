<?php
/**
 * Organisations- och kontaktuppgifter.
 *
 * Allt som återkommer i sidhuvud, sidfot och kontaktsektionen — org.nr,
 * telefonnummer, e-post, adresser, sociala länkar och kontaktpersoner —
 * bor i ett enda alternativ (`noav_settings`) och redigeras under
 * Inställningar → Noav.
 *
 * Anledningen att de inte ligger som text i mallarna: ett telefonnummer
 * förekommer på sex ställen i sajten, och ett nummer som bara uppdaterats
 * på fem av dem är värre än inget nummer alls.
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NOAV_OPTION = 'noav_settings';

/** Hur många kontaktpersoner som kan sparas. */
const NOAV_MAX_CONTACTS = 6;

/**
 * Standardvärden. Används som reserv innan sidan har sparats en gång.
 *
 * @return array<string,mixed>
 */
function noav_default_settings(): array {
	return array(
		'org_number'      => '559452-7045',
		'tagline'         => __( 'HVB-hem för ungdomar 13–17 år, med lågaffektivt bemötande och evidensbaserade metoder.', 'noav' ),
		'main_phone'      => '',
		'general_email'   => '',
		'copyright'       => __( 'Alla rättigheter förbehållna.', 'noav' ),
		'compliance'      => __( 'Kvalitetsledningssystem enligt SOSFS 2011:9 · Kollektivavtal via Vårdföretagarna', 'noav' ),
		'show_demo_badge' => false,
		'instagram'       => '',
		'facebook'        => '',
		'linkedin'        => '',
		'instagram_label' => 'Instagram — @noavab',
		'contacts'        => array(),
	);
}

/**
 * Läser en inställning.
 *
 * @param string $key     Nyckel.
 * @param mixed  $default Reservvärde om nyckeln saknas helt.
 * @return mixed
 */
function noav_setting( string $key, $default = null ) {
	static $settings = null;
	if ( null === $settings ) {
		$settings = wp_parse_args(
			(array) get_option( NOAV_OPTION, array() ),
			noav_default_settings()
		);
	}

	$value = $settings[ $key ] ?? $default;

	// Ett sparat men tomt textfält ska falla tillbaka på standardvärdet,
	// annars försvinner t.ex. sidfotens text så fort någon råkar rensa den.
	if ( '' === $value && null !== $default ) {
		return $default;
	}
	return $value;
}

/**
 * Kontaktpersoner, utan tomma rader.
 *
 * @return array<int,array{role:string,name:string,phone:string,email:string}>
 */
function noav_contacts(): array {
	$rows = (array) noav_setting( 'contacts', array() );
	return array_values(
		array_filter(
			$rows,
			static fn( $row ): bool => is_array( $row ) && ( '' !== trim( (string) ( $row['name'] ?? '' ) ) || '' !== trim( (string) ( $row['role'] ?? '' ) ) )
		)
	);
}

/**
 * Gör ett svenskt telefonnummer till ett tel:-värde.
 *
 * "08-123 45 67" blir "+4681234567". Ett nummer som redan börjar med +
 * lämnas som det är.
 *
 * @param string $phone Numret som det visas.
 */
function noav_tel_href( string $phone ): string {
	$phone = trim( $phone );
	if ( '' === $phone ) {
		return '';
	}
	if ( str_starts_with( $phone, '+' ) ) {
		return '+' . preg_replace( '/\D/', '', $phone );
	}

	$digits = (string) preg_replace( '/\D/', '', $phone );
	if ( '' === $digits ) {
		return '';
	}
	// Inhemskt format: byt inledande 0 mot landskoden.
	if ( str_starts_with( $digits, '0' ) ) {
		return '+46' . substr( $digits, 1 );
	}
	return '+' . $digits;
}

/**
 * Målet för en "Platsförfrågan"-knapp.
 *
 * Finns ett nummer blir det en tel:-länk. Saknas det pekar knappen på
 * kontaktavsnittet i stället för att döljas — knappen är ett bärande element i
 * sidhuvudet, och en sajt där den försvinner för att ett fält är tomt ser
 * trasig ut snarare än ofullständig.
 *
 * @param string $phone Enhetens nummer. Tomt = använd huvudnumret.
 */
function noav_cta_href( string $phone = '' ): string {
	$number = '' !== $phone ? $phone : (string) noav_setting( 'main_phone', '' );
	$tel    = noav_tel_href( $number );

	if ( '' !== $tel ) {
		return 'tel:' . $tel;
	}

	// Startsidan och enhetssidorna har var sitt kontaktavsnitt — stanna på
	// sidan i stället för att kasta besökaren till startsidan.
	$has_own_section = is_front_page() || is_singular( 'noav_unit' );

	return ( $has_own_section ? '' : home_url( '/' ) ) . '#kontakt';
}

/* =========================================================================
   INSTÄLLNINGSSIDA
   ========================================================================= */

add_action( 'admin_menu', 'noav_add_settings_page' );
/**
 * Lägger sidan under Inställningar.
 */
function noav_add_settings_page(): void {
	add_options_page(
		__( 'Noav — uppgifter', 'noav' ),
		__( 'Noav', 'noav' ),
		'manage_options',
		'noav-settings',
		'noav_render_settings_page'
	);
}

add_action( 'admin_init', 'noav_register_settings' );
/**
 * Registrerar alternativet och dess fält.
 */
function noav_register_settings(): void {
	register_setting(
		'noav_settings_group',
		NOAV_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'noav_sanitize_settings',
			'default'           => noav_default_settings(),
		)
	);

	add_settings_section(
		'noav_org',
		__( 'Organisation', 'noav' ),
		static function (): void {
			echo '<p>' . esc_html__( 'Visas i sidfoten på samtliga sidor.', 'noav' ) . '</p>';
		},
		'noav-settings'
	);

	$org = array(
		'org_number' => array( __( 'Organisationsnummer', 'noav' ), 'text', '' ),
		'tagline'    => array( __( 'Kort beskrivning', 'noav' ), 'textarea', __( 'Meningen under logotypen i sidfoten.', 'noav' ) ),
		'copyright'  => array( __( 'Copyright-rad', 'noav' ), 'text', '' ),
		'compliance' => array( __( 'Kvalitets- och avtalsrad', 'noav' ), 'text', '' ),
	);
	foreach ( $org as $key => list( $label, $type, $hint ) ) {
		noav_add_field( $key, $label, $type, 'noav_org', $hint );
	}

	noav_add_field(
		'show_demo_badge',
		__( 'Visa demo-märkning', 'noav' ),
		'checkbox',
		'noav_org',
		__( 'Skriver ut "Demo — ej publicerad webbplats" i sidfoten. Stäng av innan sajten publiceras.', 'noav' )
	);

	add_settings_section(
		'noav_contact',
		__( 'Kontakt', 'noav' ),
		static function (): void {
			echo '<p>' . esc_html__( 'Enheternas egna adresser och nummer redigeras på respektive enhet, inte här.', 'noav' ) . '</p>';
		},
		'noav-settings'
	);

	noav_add_field( 'main_phone', __( 'Huvudnummer', 'noav' ), 'text', 'noav_contact', __( 'Verksamhetschefens nummer. Används av knappen "Platsförfrågan".', 'noav' ) );
	noav_add_field( 'general_email', __( 'Allmän e-post', 'noav' ), 'text', 'noav_contact', '' );

	add_settings_field(
		'noav_contacts',
		__( 'Kontaktpersoner', 'noav' ),
		'noav_render_contacts_field',
		'noav-settings',
		'noav_contact'
	);

	add_settings_section(
		'noav_social',
		__( 'Sociala kanaler', 'noav' ),
		static function (): void {
			echo '<p>' . esc_html__( 'Lämna tomt för att utesluta länken ur sidfoten.', 'noav' ) . '</p>';
		},
		'noav-settings'
	);

	noav_add_field( 'instagram', __( 'Instagram — adress', 'noav' ), 'url', 'noav_social', '' );
	noav_add_field( 'instagram_label', __( 'Instagram — text', 'noav' ), 'text', 'noav_social', '' );
	noav_add_field( 'facebook', __( 'Facebook — adress', 'noav' ), 'url', 'noav_social', '' );
	noav_add_field( 'linkedin', __( 'LinkedIn — adress', 'noav' ), 'url', 'noav_social', '' );
}

/**
 * Registrerar ett enkelt fält.
 *
 * @param string $key     Nyckel i alternativet.
 * @param string $label   Etikett.
 * @param string $type    text | textarea | url | checkbox.
 * @param string $section Sektions-ID.
 * @param string $hint    Hjälptext.
 */
function noav_add_field( string $key, string $label, string $type, string $section, string $hint = '' ): void {
	add_settings_field(
		'noav_' . $key,
		$label,
		'noav_render_field',
		'noav-settings',
		$section,
		array(
			'key'       => $key,
			'type'      => $type,
			'hint'      => $hint,
			'label_for' => 'noav-field-' . $key,
		)
	);
}

/**
 * Renderar ett enkelt fält.
 *
 * @param array<string,string> $args Fältargument.
 */
function noav_render_field( array $args ): void {
	$key   = $args['key'];
	$type  = $args['type'];
	$id    = 'noav-field-' . $key;
	$name  = NOAV_OPTION . '[' . $key . ']';
	$value = noav_setting( $key, '' );

	switch ( $type ) {
		case 'textarea':
			printf(
				'<textarea id="%s" name="%s" rows="3" class="large-text">%s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( (string) $value )
			);
			break;

		case 'checkbox':
			printf(
				'<label><input type="checkbox" id="%s" name="%s" value="1"%s> %s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( (bool) $value, true, false ),
				esc_html__( 'Aktiverad', 'noav' )
			);
			break;

		default:
			printf(
				'<input type="%s" id="%s" name="%s" value="%s" class="regular-text">',
				'url' === $type ? 'url' : 'text',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value )
			);
	}

	if ( '' !== $args['hint'] ) {
		printf( '<p class="description">%s</p>', esc_html( $args['hint'] ) );
	}
}

/**
 * Renderar tabellen med kontaktpersoner.
 *
 * Alltid en tom rad sist, så att en ny person kan läggas till utan att
 * något behöver klickas fram.
 */
function noav_render_contacts_field(): void {
	$rows = noav_contacts();
	$rows[] = array( 'role' => '', 'name' => '', 'phone' => '', 'email' => '' );
	$rows = array_slice( $rows, 0, NOAV_MAX_CONTACTS );

	echo '<table class="widefat striped" style="max-width:52em">';
	echo '<thead><tr>';
	printf( '<th>%s</th>', esc_html__( 'Roll / enhet', 'noav' ) );
	printf( '<th>%s</th>', esc_html__( 'Namn', 'noav' ) );
	printf( '<th>%s</th>', esc_html__( 'Telefon', 'noav' ) );
	printf( '<th>%s</th>', esc_html__( 'E-post', 'noav' ) );
	echo '</tr></thead><tbody>';

	foreach ( $rows as $index => $row ) {
		echo '<tr>';
		foreach ( array( 'role', 'name', 'phone', 'email' ) as $field ) {
			printf(
				'<td><input type="text" name="%s[contacts][%d][%s]" value="%s" class="regular-text" style="width:100%%"></td>',
				esc_attr( NOAV_OPTION ),
				(int) $index,
				esc_attr( $field ),
				esc_attr( (string) ( $row[ $field ] ?? '' ) )
			);
		}
		echo '</tr>';
	}

	echo '</tbody></table>';
	printf(
		'<p class="description">%s</p>',
		esc_html__( 'Töm namn och roll på en rad för att ta bort personen. Ordningen här är ordningen på sajten.', 'noav' )
	);
}

/**
 * Städar och validerar inskickade värden.
 *
 * @param mixed $input Rådata från formuläret.
 * @return array<string,mixed>
 */
function noav_sanitize_settings( $input ): array {
	$input  = is_array( $input ) ? $input : array();
	$output = array();

	foreach ( array( 'org_number', 'main_phone', 'copyright', 'compliance', 'instagram_label' ) as $key ) {
		$output[ $key ] = sanitize_text_field( (string) ( $input[ $key ] ?? '' ) );
	}

	$output['tagline']       = sanitize_textarea_field( (string) ( $input['tagline'] ?? '' ) );
	$output['general_email'] = sanitize_email( (string) ( $input['general_email'] ?? '' ) );

	foreach ( array( 'instagram', 'facebook', 'linkedin' ) as $key ) {
		$output[ $key ] = esc_url_raw( (string) ( $input[ $key ] ?? '' ) );
	}

	$output['show_demo_badge'] = ! empty( $input['show_demo_badge'] );

	$contacts = array();
	foreach ( (array) ( $input['contacts'] ?? array() ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$role  = sanitize_text_field( (string) ( $row['role'] ?? '' ) );
		$name  = sanitize_text_field( (string) ( $row['name'] ?? '' ) );
		$phone = sanitize_text_field( (string) ( $row['phone'] ?? '' ) );
		$email = sanitize_email( (string) ( $row['email'] ?? '' ) );

		// En rad utan roll och namn är en tom rad, inte en person.
		if ( '' === $role && '' === $name ) {
			continue;
		}
		$contacts[] = compact( 'role', 'name', 'phone', 'email' );
	}
	$output['contacts'] = array_slice( $contacts, 0, NOAV_MAX_CONTACTS );

	return $output;
}

/**
 * Renderar inställningssidan.
 */
function noav_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'noav_settings_group' );
			do_settings_sections( 'noav-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
