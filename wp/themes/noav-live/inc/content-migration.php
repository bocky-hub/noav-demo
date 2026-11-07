<?php
/**
 * One-time, loss-averse conversion from the original template placeholders.
 * The JSON is first-install content only. Published pages are the source of
 * truth after migration; updating the theme never re-seeds customer content.
 *
 * @package Noav_Live
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Import a bundled photograph into the normal, editable Media Library. */
function noav_import_bundled_media( $filename, $description = array() ) {
	$filename = wp_basename( $filename );
	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'meta_key'       => '_noav_bundled_filename',
		'meta_value'     => $filename,
		'fields'         => 'ids',
	) );
	if ( $existing && get_attached_file( $existing[0] ) && file_exists( get_attached_file( $existing[0] ) ) ) {
		return (int) $existing[0];
	}

	$source = get_template_directory() . '/assets/img/' . $filename;
	if ( ! is_file( $source ) ) {
		return new WP_Error( 'noav_missing_image', 'En bild saknas i temat: ' . $filename );
	}
	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) ) {
		return new WP_Error( 'noav_upload_directory', $uploads['error'] );
	}
	$target_name = wp_unique_filename( $uploads['path'], $filename );
	$target      = trailingslashit( $uploads['path'] ) . $target_name;
	if ( ! copy( $source, $target ) ) {
		return new WP_Error( 'noav_image_copy', 'Kunde inte kopiera en bild till mediabiblioteket.' );
	}
	$filetype = wp_check_filetype( $target_name );
	$attachment_id = wp_insert_attachment( array(
		'post_title'     => ! empty( $description['title'] ) ? sanitize_text_field( $description['title'] ) : pathinfo( $filename, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
		'post_mime_type' => $filetype['type'],
	), $target, 0, true );
	if ( is_wp_error( $attachment_id ) ) {
		wp_delete_file( $target );
		return $attachment_id;
	}
	update_post_meta( $attachment_id, '_noav_bundled_filename', $filename );
	// Metadata is seeded only for newly imported media. Editors own it thereafter.
	if ( isset( $description['alt'] ) ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $description['alt'] ) );
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $target );
	if ( is_array( $metadata ) ) {
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}
	return (int) $attachment_id;
}

/**
 * Seed only the theme's original empty placeholders. This also safely resumes
 * after an interrupted import: copied images and completed pages are reused.
 *
 * @return true|WP_Error
 */
function noav_migrate_editable_content() {
	$version = '1';
	if ( $version === get_option( 'noav_editable_content_version' ) ) {
		return true;
	}
	// Prevent two first page loads from importing the same content concurrently.
	$lock = (int) get_option( 'noav_editable_content_lock' );
	if ( $lock && $lock > time() - 300 ) {
		return new WP_Error( 'noav_migration_busy', 'Innehållet håller på att förberedas.' );
	}
	if ( $lock ) {
		delete_option( 'noav_editable_content_lock' );
	}
	if ( ! add_option( 'noav_editable_content_lock', time(), '', false ) ) {
		return new WP_Error( 'noav_migration_busy', 'Innehållet håller på att förberedas.' );
	}

	try {
		$seed_file = get_template_directory() . '/inc/seed-content.json';
		$seed = is_file( $seed_file ) ? json_decode( file_get_contents( $seed_file ), true ) : null;
		if ( ! is_array( $seed ) || empty( $seed['pages'] ) ) {
			return new WP_Error( 'noav_seed_missing', 'Startinnehållet kunde inte läsas.' );
		}
		$pending = array();
		foreach ( $seed['pages'] as $path => $content ) {
			$page = get_page_by_path( $path );
			if ( ! $page ) {
				return new WP_Error( 'noav_page_missing', 'En sida saknas: ' . $path );
			}
			if ( get_post_meta( $page->ID, '_noav_editable_migrated', true ) ) {
				continue;
			}
			$old = trim( (string) $page->post_content );
			if ( '' !== $old && '<!-- Renderas av temat (parts/…). -->' !== $old ) {
				// Human-authored content, including Gutenberg content, takes precedence.
				continue;
			}
			$pending[ $page->ID ] = $content;
		}

		$replacements = array(
			'{{NOAV_ASSETS}}' => NOAV_URI,
			'{{NOAV_HOME}}'   => untrailingslashit( home_url( '/' ) ),
		);
		foreach ( $pending as $content ) {
			preg_match_all( '/\{\{NOAV_(?:MEDIA|ID):([^}]+)\}\}/', $content, $matches );
			foreach ( array_unique( $matches[1] ) as $filename ) {
				if ( isset( $replacements[ '{{NOAV_ID:' . $filename . '}}' ] ) ) {
					continue;
				}
				$description = isset( $seed['media'][ $filename ] ) && is_array( $seed['media'][ $filename ] ) ? $seed['media'][ $filename ] : array();
				$id = noav_import_bundled_media( $filename, $description );
				if ( is_wp_error( $id ) ) {
					return $id;
				}
				$replacements[ '{{NOAV_ID:' . $filename . '}}' ] = (string) $id;
				$replacements[ '{{NOAV_MEDIA:' . $filename . '}}' ] = esc_url( wp_get_attachment_url( $id ) );
			}
		}
		foreach ( $pending as $id => $content ) {
			// Check again immediately before writing in case an editor saved meanwhile.
			$page = get_post( $id );
			$old = trim( (string) $page->post_content );
			if ( get_post_meta( $id, '_noav_editable_migrated', true ) || ( '' !== $old && '<!-- Renderas av temat (parts/…). -->' !== $old ) ) {
				continue;
			}
			add_post_meta( $id, '_noav_before_editable_content', $page->post_content, true );
			$result = wp_update_post( wp_slash( array(
				'ID'           => $id,
				'post_content' => strtr( $content, $replacements ),
			) ), true );
			if ( is_wp_error( $result ) || ! $result ) {
				return is_wp_error( $result ) ? $result : new WP_Error( 'noav_page_update', 'En sida kunde inte sparas.' );
			}
			update_post_meta( $id, '_noav_editable_migrated', $version );
		}
		update_option( 'noav_editable_content_version', $version, false );
		delete_option( 'noav_editable_content_error' );
		return true;
	} finally {
		delete_option( 'noav_editable_content_lock' );
	}
}

add_action( 'init', function () {
	$result = noav_migrate_editable_content();
	if ( is_wp_error( $result ) && 'noav_migration_busy' !== $result->get_error_code() ) {
		update_option( 'noav_editable_content_error', $result->get_error_message(), false );
	}
}, 20 );

add_action( 'admin_notices', function () {
	$error = get_option( 'noav_editable_content_error' );
	if ( $error && current_user_can( 'manage_options' ) ) {
		echo '<div class="notice notice-error"><p>' . esc_html( 'Noavs innehåll kunde inte färdigställas: ' . $error ) . '</p></div>';
	}
} );
