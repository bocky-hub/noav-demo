<?php
/**
 * Noav – Vinkelvikens HVB (kopia) — theme functions.
 *
 * Faithful WordPress reproduction of noav.se, split into a landing page and
 * two unit branches: vinkelviken (real) and kyrkhult (placeholder).
 *
 * @package Noav_Live
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NOAV_URI', get_template_directory_uri() . '/assets' );

/** The units, in landing order. slug => display name. */
function noav_units() {
	return array(
		'vinkelviken' => 'Vinkelviken',
		'kyrkhult'    => 'Kyrkhult',
	);
}

/** The section pages inside each unit, in menu order. slug => label. */
function noav_sections() {
	return array(
		'oss'            => 'Om oss',
		'malgrupp'       => 'Målgrupp',
		'behandling'     => 'Behandling',
		'platsforfrogan' => 'Platsförfrågan',
		'kontakt'        => 'Kontakt',
		'aktuellt'       => 'Aktuellt',
		'galleri'        => 'Galleri',
	);
}

/** Totalt antal platser per enhet. */
function noav_platser_totalt( $unit ) {
	$totalt = array(
		'vinkelviken' => 6,
		'kyrkhult'    => 6,
	);
	return isset( $totalt[ $unit ] ) ? $totalt[ $unit ] : 0;
}

/** Antal lediga platser för en enhet (0..totalt). Redigeras i wp-admin → Lediga platser. */
function noav_platser_lediga( $unit ) {
	$totalt = noav_platser_totalt( $unit );
	$value  = (int) get_option( 'noav_lediga_' . $unit, $totalt );
	return max( 0, min( $totalt, $value ) );
}

/** Platsbrickan på landningssidans enhetskort: "N lediga platser av 6" / "Fullbelagt just nu". */
function noav_platser_badge( $unit ) {
	$totalt = noav_platser_totalt( $unit );
	if ( $totalt < 1 ) {
		return;
	}
	$lediga = noav_platser_lediga( $unit );
	if ( $lediga > 0 ) {
		$text = 1 === $lediga ? '1 ledig plats av ' . $totalt : $lediga . ' lediga platser av ' . $totalt;
		$dot  = 'rgb(74,124,89)';
		$fg   = 'rgb(56,98,70)';
		$bd   = 'rgba(74,124,89,.32)';
		$bg   = 'rgba(74,124,89,.08)';
	} else {
		$text = 'Fullbelagt just nu';
		$dot  = 'rgb(176,88,64)';
		$fg   = 'rgb(82,63,41)';
		$bd   = 'rgba(82,63,41,.25)';
		$bg   = 'rgba(82,63,41,.05)';
	}
	printf(
		'<div style="margin-top:1.5rem;display:inline-flex;align-items:center;gap:9px;padding:9px 20px;border-radius:40px;border:1px solid %1$s;background-color:%2$s;"><span style="width:8px;height:8px;border-radius:50%%;background-color:%3$s;"></span><span class="body-normal" style="font-size:14px;font-weight:600;color:%4$s;">%5$s</span></div>',
		$bd,
		$bg,
		$dot,
		$fg,
		esc_html( $text )
	);
}

/** Current unit slug (vinkelviken/kyrkhult), or '' on the landing/other. */
function noav_current_unit() {
	if ( ! is_page() ) {
		return '';
	}
	$id        = get_queried_object_id();
	$ancestors = get_post_ancestors( $id );
	$top       = $ancestors ? (int) end( $ancestors ) : (int) $id;
	$slug      = get_post_field( 'post_name', $top );
	return array_key_exists( $slug, noav_units() ) ? $slug : '';
}

/** Current section slug ('home' on a unit's front page, else oss/malgrupp/…). */
function noav_current_section() {
	$id   = get_queried_object_id();
	$slug = get_post_field( 'post_name', $id );
	return array_key_exists( $slug, noav_units() ) ? 'home' : $slug;
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'noav-fonts',
		'https://fonts.googleapis.com/css2?family=Merriweather:wght@400&family=Nunito:wght@400;600;700;800&family=Bree+Serif:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'noav-durable-base', $uri . '/assets/css/edd0a66d0b86bfe4.css', array( 'noav-fonts' ), file_exists( "$dir/assets/css/edd0a66d0b86bfe4.css" ) ? filemtime( "$dir/assets/css/edd0a66d0b86bfe4.css" ) : '1.0.0' );
	wp_enqueue_style( 'noav-durable-page', $uri . '/assets/css/6ad5f70cb56137d7.css', array( 'noav-durable-base' ), file_exists( "$dir/assets/css/6ad5f70cb56137d7.css" ) ? filemtime( "$dir/assets/css/6ad5f70cb56137d7.css" ) : '1.0.0' );
	wp_enqueue_style( 'noav-copy', $uri . '/assets/css/noav-copy.css', array( 'noav-durable-page' ), file_exists( "$dir/assets/css/noav-copy.css" ) ? filemtime( "$dir/assets/css/noav-copy.css" ) : '1.0.0' );

	if ( is_page() && 'kontakt' === noav_current_section() ) {
		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
	}

	wp_enqueue_script( 'noav-main', $uri . '/assets/js/main.js', array(), file_exists( "$dir/assets/js/main.js" ) ? filemtime( "$dir/assets/js/main.js" ) : '1.0.0', true );
} );

/**
 * Create the whole page tree on activation: landing front page + two unit
 * parents + seven section pages under each. Idempotent.
 */
add_action( 'after_switch_theme', function () {

	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	update_option( 'blogname', 'Noav' );
	update_option( 'blogdescription', 'HVB och skyddat boende' );

	$ensure = function ( $slug, $title, $parent = 0 ) {
		$existing = get_page_by_path( $parent ? get_post_field( 'post_name', $parent ) . '/' . $slug : $slug );
		if ( $existing ) {
			return $existing->ID;
		}
		return wp_insert_post( array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $parent,
			'post_content' => '<!-- Renderas av temat (parts/…). -->',
		) );
	};

	// Landing (front page).
	$landing_id = $ensure( 'hem', 'Noav' );
	if ( $landing_id && ! is_wp_error( $landing_id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $landing_id );
	}

	// Units + their section pages.
	foreach ( noav_units() as $uslug => $uname ) {
		$parent_id = $ensure( $uslug, $uname );
		if ( ! $parent_id || is_wp_error( $parent_id ) ) {
			continue;
		}
		foreach ( noav_sections() as $sslug => $slabel ) {
			$ensure( $sslug, $slabel, $parent_id );
		}
	}

	flush_rewrite_rules();
} );

/**
 * wp-admin → "Lediga platser": ett enkelt formulär där personalen sätter antal
 * lediga platser per enhet. Visas på landningssidans enhetskort.
 */
add_action( 'admin_menu', function () {
	add_menu_page(
		'Lediga platser',
		'Lediga platser',
		'edit_pages',
		'noav-lediga-platser',
		'noav_platser_admin_page',
		'dashicons-groups',
		25
	);
} );

function noav_platser_admin_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$saved = false;
	if ( isset( $_POST['noav_platser_nonce'] ) ) {
		check_admin_referer( 'noav_platser_save', 'noav_platser_nonce' );
		foreach ( noav_units() as $slug => $name ) {
			$totalt = noav_platser_totalt( $slug );
			$raw    = isset( $_POST[ 'noav_lediga_' . $slug ] ) ? (int) $_POST[ 'noav_lediga_' . $slug ] : 0;
			update_option( 'noav_lediga_' . $slug, max( 0, min( $totalt, $raw ) ) );
		}
		$saved = true;
	}
	?>
	<div class="wrap">
		<h1>Lediga platser</h1>
		<?php if ( $saved ) : ?>
			<div class="notice notice-success is-dismissible"><p>Sparat — landningssidan visar nu de nya siffrorna.</p></div>
		<?php endif; ?>
		<p>Antalet visas på startsidans enhetskort. Sätt till <strong>0</strong> så visas ”Fullbelagt just nu”.</p>
		<form method="post">
			<?php wp_nonce_field( 'noav_platser_save', 'noav_platser_nonce' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( noav_units() as $slug => $name ) : $totalt = noav_platser_totalt( $slug ); ?>
					<tr>
						<th scope="row"><label for="noav_lediga_<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></label></th>
						<td>
							<input type="number" min="0" max="<?php echo (int) $totalt; ?>" step="1"
								id="noav_lediga_<?php echo esc_attr( $slug ); ?>"
								name="noav_lediga_<?php echo esc_attr( $slug ); ?>"
								value="<?php echo (int) noav_platser_lediga( $slug ); ?>" style="width:5em;">
							av <?php echo (int) $totalt; ?> platser lediga
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button( 'Spara' ); ?>
		</form>
	</div>
	<?php
}
