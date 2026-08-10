<?php
/**
 * Menywalker som skriver ut rena ankarelement.
 *
 * WordPress standardwalker bygger <ul><li><a>. Sajtens .main-nav och
 * .mobile-menu är formgivna för <a> direkt i behållaren — med listelement
 * dyker punktmarkeringar upp mellan menyposterna, och <li> utan omgivande
 * <ul> är dessutom ogiltig HTML.
 *
 * @package Noav
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Skriver ut menyn som en följd av <a>-element.
 */
class Noav_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Undermenyer används inte — menyn är alltid ett plan djup.
	 *
	 * @param string $output Utdata.
	 * @param int    $depth  Nivå.
	 * @param array  $args   Argument.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Motsvarande avslutning.
	 *
	 * @param string $output Utdata.
	 * @param int    $depth  Nivå.
	 * @param array  $args   Argument.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Ingen avslutande tagg behövs — start_el() skriver hela elementet.
	 *
	 * @param string  $output Utdata.
	 * @param WP_Post $item   Menypost.
	 * @param int     $depth  Nivå.
	 * @param array   $args   Argument.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}

	/**
	 * Skriver ut en menypost som ett ankarelement.
	 *
	 * @param string   $output Utdata.
	 * @param WP_Post  $item   Menypost.
	 * @param int      $depth  Nivå.
	 * @param stdClass $args   Argument från wp_nav_menu().
	 * @param int      $id     Oanvänt.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$atts = array(
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
		);

		/** Samma filter som standardwalkern använder, så temats egna
		 * justeringar av klass och aria-current fortsätter gälla. */
		$atts = (array) apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$markup = '';
		foreach ( $atts as $name => $value ) {
			if ( '' === $value || false === $value || null === $value ) {
				continue;
			}
			$value   = 'href' === $name ? esc_url( (string) $value ) : esc_attr( (string) $value );
			$markup .= sprintf( ' %s="%s"', $name, $value );
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$output .= '<a' . $markup . '>' . esc_html( $title ) . '</a>';
	}
}
