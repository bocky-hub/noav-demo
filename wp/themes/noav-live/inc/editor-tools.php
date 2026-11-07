<?php
/** Staff-facing settings and the two functional content blocks. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function noav_site_setting( $key, $default = '' ) {
	$settings = get_option( 'noav_site_settings', array() );
	return is_array( $settings ) && array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
}

function noav_social_defaults() {
	return array(
		'instagram' => 'https://instagram.com/noavab',
		'facebook' => 'https://facebook.com/profile.php?id=61566319867531',
		'linkedin' => 'https://linkedin.com/in/vinkelviken-hvb-48b83032b',
	);
}

/** Staff-managed page menus. A missing setting uses the existing navigation. */
function noav_navigation_items( $context = 'global' ) {
	$contexts = array_merge( array( 'global' ), array_keys( noav_units() ) );
	if ( ! in_array( $context, $contexts, true ) ) { $context = 'global'; }
	$ids = noav_site_setting( 'menu_' . $context, null );
	if ( ! is_array( $ids ) ) {
		$ids = array();
		$sections = 'global' === $context ? noav_global_sections() : noav_sections();
		foreach ( $sections as $slug => $label ) {
			$page = get_page_by_path( ( 'global' === $context ? '' : $context . '/' ) . $slug );
			if ( $page ) { $ids[] = $page->ID; }
		}
	}
	$items = array();
	foreach ( array_unique( array_map( 'absint', $ids ) ) as $id ) {
		$page = get_post( $id );
		if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status ) { continue; }
		$items[] = array( 'id' => $id, 'url' => get_permalink( $id ), 'label' => get_the_title( $id ) );
	}
	return $items;
}

/** Reuse the original icon markup, with staff-managed destinations. */
function noav_render_social_links() {
	$file = get_template_directory() . '/parts/_social.html';
	if ( ! is_readable( $file ) ) { return; }
	$html = file_get_contents( $file );
	$names = array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn' );
	$html = preg_replace_callback( '#<li><a\s+target="_blank"\s+href="([^"]*)">(.*?)</a></li>#s', function ( $match ) use ( $names ) {
		foreach ( noav_social_defaults() as $key => $default ) {
			if ( false !== strpos( $match[1], $key . '.com' ) ) {
				$url = noav_site_setting( $key, $default );
				return $url ? '<li><a target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $names[ $key ] ) . '" href="' . esc_url( $url ) . '">' . $match[2] . '</a></li>' : '';
			}
		}
		return '';
	}, $html );
	echo $html; // Fixed local icon markup; all editable values escaped above.
}

add_action( 'init', function () {
	$dir = get_template_directory();
	wp_register_script( 'noav-editor-blocks', get_template_directory_uri() . '/assets/js/editor-blocks.js', array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ), filemtime( $dir . '/assets/js/editor-blocks.js' ), true );
	register_block_type( 'noav/map', array(
		'api_version' => 3,
		'editor_script' => 'noav-editor-blocks',
		'attributes' => array(
			'latitude' => array( 'type' => 'number', 'default' => 55.8575946 ),
			'longitude' => array( 'type' => 'number', 'default' => 13.6611013 ),
			'label' => array( 'type' => 'string', 'default' => 'Noav' ),
			'zoom' => array( 'type' => 'number', 'default' => 16 ),
		),
		'supports' => array( 'html' => false ),
		'render_callback' => function ( $attrs ) {
			$lat = max( -90, min( 90, (float) $attrs['latitude'] ) );
			$lng = max( -180, min( 180, (float) $attrs['longitude'] ) );
			$label = sanitize_text_field( $attrs['label'] );
			$zoom = max( 1, min( 19, (int) $attrs['zoom'] ) );
			return sprintf( '<div %1$s><div data-noav-map data-lat="%2$s" data-lng="%3$s" data-label="%4$s" data-zoom="%5$d" style="height:360px;width:100%%;border-radius:16px;overflow:hidden" role="region" aria-label="%4$s"></div><p><a href="%6$s" target="_blank" rel="noopener noreferrer">%7$s</a></p></div>',
				get_block_wrapper_attributes( array( 'class' => 'noav-map-block' ) ), esc_attr( $lat ), esc_attr( $lng ), esc_attr( $label ), $zoom,
				esc_url( 'https://www.openstreetmap.org/?mlat=' . $lat . '&mlon=' . $lng . '#map=' . $zoom . '/' . $lat . '/' . $lng ), esc_html( 'Öppna kartan: ' . $label ) );
		},
	) );
	register_block_type( 'noav/availability', array(
		'api_version' => 3,
		'editor_script' => 'noav-editor-blocks',
		'attributes' => array( 'unit' => array( 'type' => 'string', 'enum' => array( 'vinkelviken', 'kyrkhult' ), 'default' => 'vinkelviken' ) ),
		'supports' => array( 'html' => false ),
		'render_callback' => function ( $attrs ) {
			if ( ! isset( noav_units()[ $attrs['unit'] ] ) ) { return ''; }
			ob_start();
			noav_platser_badge( $attrs['unit'] );
			return '<div ' . get_block_wrapper_attributes( array( 'class' => 'noav-availability-block' ) ) . '>' . ob_get_clean() . '</div>';
		},
	) );
} );

add_action( 'enqueue_block_editor_assets', function () {
	$units = array();
	foreach ( noav_units() as $slug => $name ) {
		$units[ $slug ] = array( 'name' => $name, 'total' => noav_platser_totalt( $slug ), 'available' => noav_platser_lediga( $slug ) );
	}
	wp_localize_script( 'noav-editor-blocks', 'noavEditor', array( 'units' => $units, 'availabilityUrl' => admin_url( 'admin.php?page=noav-lediga-platser' ) ) );
} );

// Queue before noav-main so maps are ready when the existing behaviour runs.
add_action( 'wp_enqueue_scripts', function () {
	$post = get_post();
	if ( $post && has_block( 'noav/map', $post ) ) {
		wp_enqueue_style( 'noav-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'noav-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
	}
}, 5 );

add_action( 'admin_menu', function () {
	add_menu_page( 'Redigera Noavs hemsida', 'Noav – redigera', 'edit_pages', 'noav-redigera', 'noav_staff_start_page', 'dashicons-edit-page', 3 );
	add_submenu_page( 'noav-redigera', 'Kom igång', 'Kom igång', 'edit_pages', 'noav-redigera', 'noav_staff_start_page' );
	add_submenu_page( 'noav-redigera', 'Logotyp och webbplatsinställningar', 'Logotyp och inställningar', 'edit_pages', 'noav-installningar', 'noav_settings_page' );
} );

function noav_staff_start_page() {
	if ( ! current_user_can( 'edit_pages' ) ) { return; }
	?>
	<div class="wrap"><h1>Redigera Noavs hemsida</h1>
	<p>Välj en sida, klicka på texten eller bilden och gör din ändring. Klicka på <strong>Förhandsgranska</strong> för att kontrollera och sedan <strong>Spara/Uppdatera</strong> för att publicera.</p>
	<table class="widefat striped" style="max-width:900px"><thead><tr><th>Sida</th><th>Ändra innehåll</th><th>Visa sidan</th></tr></thead><tbody>
	<?php
	$paths = array( 'hem' => 'Startsida', 'oss' => 'Om oss', 'kontakt' => 'Kontakt', 'platsforfrogan' => 'Platsförfrågan', 'aktuellt' => 'Aktuellt' );
	foreach ( noav_units() as $slug => $name ) {
		$paths[ $slug ] = $name . ' – startsida';
		foreach ( noav_sections() as $section => $label ) { $paths[ $slug . '/' . $section ] = $name . ' – ' . $label; }
	}
	foreach ( $paths as $path => $label ) {
		$page = get_page_by_path( $path );
		if ( ! $page || ! current_user_can( 'edit_post', $page->ID ) ) { continue; }
		printf( '<tr><td>%1$s</td><td><a href="%2$s">Redigera</a></td><td><a href="%3$s" target="_blank" rel="noopener">Visa</a></td></tr>', esc_html( $label ), esc_url( get_edit_post_link( $page->ID, 'raw' ) ), esc_url( get_permalink( $page->ID ) ) );
	}
	?>
	</tbody></table>
	<h2>Vanliga ändringar</h2><ul style="list-style:disc;padding-left:24px;max-width:900px">
	<li><strong>Texter och kontaktuppgifter:</strong> öppna sidan och skriv direkt i texten. Ändra även länkens mål när ett telefonnummer eller en e-postadress byts.</li>
	<li><strong>Bilder:</strong> klicka på bilden och välj Ersätt → Mediabibliotek eller Ladda upp. Ange alternativ text som beskriver bilden.</li>
	<li><strong>Galleri:</strong> klicka på galleriet och välj Lägg till för fler bilder. Markera en bild för att ersätta, ta bort eller ändra bildtext. Bilder kan flyttas med pilarna eller listvyn.</li>
	<li><strong>Aktuellt:</strong> öppna Aktuellt och lägg till rubrik, datum, text och bild. Du kan även markera en befintlig nyhetsgrupp i listvyn, välja Duplicera och ändra kopians innehåll.</li>
	<li><strong>Fler innehållsdelar:</strong> klicka på + för att lägga till exempelvis rubrik, stycke, bild, galleri, knapp eller en Noav-karta.</li>
	<li><strong>Menyer:</strong> öppna Logotyp och inställningar för att välja vilka sidor som ska finnas i menyn och i vilken ordning. Menylänkens namn följer sidans rubrik.</li>
	<li><strong>Ångra:</strong> använd Ångra i redigeraren. Tidigare sparade versioner finns under Sidinställningar → Versioner.</li>
	</ul>
	<p><a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=noav-lediga-platser' ) ); ?>">Ändra lediga platser</a> <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=noav-installningar' ) ); ?>">Ändra logotyp, sociala länkar och platsantal</a> <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>">Mediabibliotek</a></p>
	<p>Ändringar sparas på den WordPress-installation där du är inloggad. Playground-demot är en egen förhandsvisning i din webbläsare; ändringar där uppdaterar inte noav.se.</p>
	</div>
	<?php
}

add_action( 'wp_dashboard_setup', function () {
	if ( current_user_can( 'edit_pages' ) ) {
		wp_add_dashboard_widget( 'noav_staff_start', 'Noavs hemsida', function () {
			echo '<p>Här ändrar ni sidornas texter, bilder, gallerier och nyheter.</p><p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=noav-redigera' ) ) . '">Redigera hemsidan</a></p>';
		} );
	}
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( false === strpos( $hook, 'noav-installningar' ) ) { return; }
	wp_enqueue_media();
	$file = get_template_directory() . '/assets/js/settings.js';
	wp_enqueue_script( 'noav-settings', get_template_directory_uri() . '/assets/js/settings.js', array( 'media-views' ), filemtime( $file ), true );
} );

function noav_settings_page() {
	if ( ! current_user_can( 'edit_pages' ) ) { return; }
	$saved = false;
	if ( isset( $_POST['noav_settings_nonce'] ) ) {
		check_admin_referer( 'noav_settings_save', 'noav_settings_nonce' );
		$settings = get_option( 'noav_site_settings', array() );
		$settings = is_array( $settings ) ? $settings : array();
		foreach ( array( 'logo_id', 'favicon_id' ) as $key ) {
			$id = isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : 0;
			if ( ! $id || ( wp_attachment_is_image( $id ) && current_user_can( 'edit_post', $id ) ) ) { $settings[ $key ] = $id; }
		}
		foreach ( noav_social_defaults() as $key => $default ) {
			$settings[ $key ] = isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ? esc_url_raw( wp_unslash( $_POST[ $key ] ), array( 'http', 'https' ) ) : '';
		}
		foreach ( noav_units() as $slug => $name ) {
			$settings[ 'total_' . $slug ] = isset( $_POST[ 'total_' . $slug ] ) ? max( 1, min( 1000, (int) $_POST[ 'total_' . $slug ] ) ) : 6;
		}
		foreach ( array_merge( array( 'global' ), array_keys( noav_units() ) ) as $context ) {
			$key = 'menu_' . $context;
			$ids = isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ? array_map( 'absint', $_POST[ $key ] ) : array();
			$settings[ $key ] = array_values( array_filter( array_unique( $ids ), function ( $id ) {
				$page = get_post( $id );
				return $page && 'page' === $page->post_type && 'publish' === $page->post_status && current_user_can( 'edit_post', $id );
			} ) );
		}
		update_option( 'noav_site_settings', $settings );
		// Use WordPress's standard icon API too, so wp_head emits one consistent icon.
		update_option( 'site_icon', (int) ( $settings['favicon_id'] ?? 0 ) );
		if ( isset( $_POST['site_title'] ) && is_string( $_POST['site_title'] ) ) {
			$title = sanitize_text_field( wp_unslash( $_POST['site_title'] ) );
			if ( '' !== $title ) { update_option( 'blogname', $title ); }
		}
		if ( isset( $_POST['site_description'] ) && is_string( $_POST['site_description'] ) ) {
			update_option( 'blogdescription', sanitize_text_field( wp_unslash( $_POST['site_description'] ) ) );
		}
		$saved = true;
	}
	?>
	<div class="wrap"><h1>Logotyp och webbplatsinställningar</h1>
	<?php if ( $saved ) : ?><div class="notice notice-success is-dismissible"><p>Inställningarna är sparade.</p></div><?php endif; ?>
	<form method="post"><?php wp_nonce_field( 'noav_settings_save', 'noav_settings_nonce' ); ?>
	<table class="form-table"><tbody>
	<tr><th scope="row"><label for="noav-site-title">Webbplatsens namn</label></th><td><input class="regular-text" required type="text" id="noav-site-title" name="site_title" value="<?php echo esc_attr( get_option( 'blogname' ) ); ?>"></td></tr>
	<tr><th scope="row"><label for="noav-site-description">Kort beskrivning</label></th><td><input class="regular-text" type="text" id="noav-site-description" name="site_description" value="<?php echo esc_attr( get_option( 'blogdescription' ) ); ?>"></td></tr>
	<?php foreach ( array( 'logo_id' => 'Logotyp i sidhuvudet', 'favicon_id' => 'Webbplatsikon (favicon)' ) as $key => $label ) :
		$id = absint( noav_site_setting( $key, 0 ) );
		$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
	?>
	<tr><th scope="row"><?php echo esc_html( $label ); ?></th><td><div class="noav-image-setting" data-noav-image-setting data-default-preview="<?php echo esc_url( NOAV_URI . ( 'logo_id' === $key ? '/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png' : '/img/1dtcc2gE5qyWZ3T5yiqCAvh2gWrgRRvFl0j4TUd0ArXkHOSGt8j16ZdsxH5jqmdX.png' ) ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo $id; ?>">
	<img alt="" src="<?php echo esc_url( $url ?: NOAV_URI . ( 'logo_id' === $key ? '/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png' : '/img/1dtcc2gE5qyWZ3T5yiqCAvh2gWrgRRvFl0j4TUd0ArXkHOSGt8j16ZdsxH5jqmdX.png' ) ); ?>" style="display:block;max-width:220px;max-height:120px;object-fit:contain;margin-bottom:12px">
	<button type="button" class="button" data-noav-select-image>Välj eller ladda upp bild</button> <button type="button" class="button" data-noav-clear-image>Återställ standardbild</button>
	<p class="description">Bilden hämtas från mediabiblioteket. Spara inställningarna när du är klar.</p></div></td></tr>
	<?php endforeach; ?>
	<?php foreach ( noav_social_defaults() as $key => $default ) : ?>
	<tr><th scope="row"><label for="noav-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( ucfirst( $key ) ); ?></label></th><td><input class="regular-text" type="url" id="noav-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( noav_site_setting( $key, $default ) ); ?>"><p class="description">Lämna tomt för att dölja länken.</p></td></tr>
	<?php endforeach; ?>
	<?php foreach ( noav_units() as $slug => $name ) : ?>
	<tr><th scope="row"><label for="total-<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?> – totalt antal platser</label></th><td><input type="number" min="1" max="1000" step="1" id="total-<?php echo esc_attr( $slug ); ?>" name="total_<?php echo esc_attr( $slug ); ?>" value="<?php echo (int) noav_site_setting( 'total_' . $slug, 6 ); ?>"><p class="description">Antalet lediga platser ändras separat under Lediga platser.</p></td></tr>
	<?php endforeach; ?>
	<?php foreach ( array_merge( array( 'global' => 'Huvudmeny' ), noav_units() ) as $context => $name ) : ?>
	<tr><th scope="row"><?php echo esc_html( $name . ( 'global' === $context ? '' : ' – enhetsmeny' ) ); ?></th><td><div data-noav-menu data-menu-name="menu_<?php echo esc_attr( $context ); ?>">
	<ol class="noav-menu-items" style="margin:0 0 12px 20px">
	<?php foreach ( noav_navigation_items( $context ) as $item ) : ?>
	<li style="margin-bottom:8px"><input type="hidden" name="menu_<?php echo esc_attr( $context ); ?>[]" value="<?php echo (int) $item['id']; ?>"><span><?php echo esc_html( $item['label'] ); ?></span> <button type="button" class="button button-small" data-menu-up aria-label="Flytta upp">↑</button> <button type="button" class="button button-small" data-menu-down aria-label="Flytta ned">↓</button> <button type="button" class="button button-small" data-menu-remove>Ta bort ur menyn</button></li>
	<?php endforeach; ?>
	</ol>
	<?php wp_dropdown_pages( array( 'name' => 'noav-menu-picker-' . $context, 'id' => 'noav-menu-picker-' . $context, 'show_option_none' => 'Välj sida att lägga till', 'option_none_value' => '0', 'sort_column' => 'post_title', 'post_status' => 'publish' ) ); ?>
	<button type="button" class="button" data-menu-add>Lägg till i menyn</button>
	<p class="description">Flytta med pilarna. Namnet följer sidans rubrik. Att ta bort en menylänk raderar inte sidan.</p></div></td></tr>
	<?php endforeach; ?>
	</tbody></table><?php submit_button( 'Spara inställningar' ); ?></form></div>
	<?php
}
