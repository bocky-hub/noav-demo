<?php
/**
 * Sidhuvud.
 *
 * @package Noav
 */

declare( strict_types = 1 );

$noav_cta = noav_cta_href();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Hoppa till innehållet', 'noav' ); ?></a>

<header class="site-header">
	<div class="container">
		<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php echo esc_attr( sprintf( /* translators: %s: sajtens namn. */ __( '%s — till startsidan', 'noav' ), get_bloginfo( 'name' ) ) ); ?>">
			NOAV<span class="wordmark-dot">.</span>
		</a>

		<nav class="main-nav" aria-label="<?php esc_attr_e( 'Huvudmeny', 'noav' ); ?>">
			<?php noav_nav_menu( 'primary' ); ?>
		</nav>

		<a class="btn btn--accent header-cta" href="<?php echo esc_url( $noav_cta ); ?>">
			<?php esc_html_e( 'Platsförfrågan', 'noav' ); ?>
		</a>

		<button class="nav-toggle" aria-expanded="false" aria-controls="mobileMenu"
			aria-label="<?php esc_attr_e( 'Öppna meny', 'noav' ); ?>">
			<span class="bar"></span><span class="bar"></span><span class="bar"></span>
		</button>
	</div>
</header>

<div class="mobile-menu" id="mobileMenu">
	<nav aria-label="<?php esc_attr_e( 'Mobilmeny', 'noav' ); ?>">
		<?php noav_nav_menu( 'primary', 'mobile-menu-item' ); ?>
		<a class="mobile-menu-item btn btn--accent" href="<?php echo esc_url( $noav_cta ); ?>">
			<?php esc_html_e( 'Platsförfrågan', 'noav' ); ?>
		</a>
	</nav>
</div>

<main id="main">
