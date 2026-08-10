<?php
/**
 * Noav-temat — startpunkt.
 *
 * Håll den här filen tom på logik. Varje delsystem bor i inc/ så att
 * det går att hitta rätt fil utan att läsa allt:
 *
 *   setup.php    — theme supports, menyer, köade stilar och skript
 *   units.php    — enheter (CPT) och lediga platser: datakällan
 *   settings.php — organisations- och kontaktuppgifter (inställningssida)
 *   blocks.php   — serverrenderade block som läser platsdata
 *   patterns.php — blockmönster för sajtens sektioner
 *   demo-seed.php— fyller en tom installation med demoinnehåll
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NOAV_VERSION', '1.0.0' );
define( 'NOAV_DIR', get_template_directory() );
define( 'NOAV_URI', get_template_directory_uri() );

require_once NOAV_DIR . '/inc/class-noav-nav-walker.php';
require_once NOAV_DIR . '/inc/setup.php';
require_once NOAV_DIR . '/inc/units.php';
require_once NOAV_DIR . '/inc/settings.php';
require_once NOAV_DIR . '/inc/blocks.php';
require_once NOAV_DIR . '/inc/patterns.php';
require_once NOAV_DIR . '/inc/demo-seed.php';
