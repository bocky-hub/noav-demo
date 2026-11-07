<?php
/**
 * Header — reproduces the noav.se document shell, now unit-aware.
 *
 * Shared pages appear beside the front-page navigation. Inside a unit,
 * the menu shows only unit-specific sections and a link back to all units.
 *
 * @package Noav_Live
 */
$noav_unit    = function_exists( 'noav_current_unit' ) ? noav_current_unit() : '';
$noav_landing = ( '' === $noav_unit );
$noav_section = noav_current_section();
$noav_logo_id = (int) noav_site_setting( 'logo_id', 0 );
$noav_logo_url = $noav_logo_id ? wp_get_attachment_image_url( $noav_logo_id, 'full' ) : NOAV_URI . '/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png';
$noav_logo_alt = $noav_logo_id ? get_post_meta( $noav_logo_id, '_wp_attachment_image_alt', true ) : get_bloginfo( 'name' );
$noav_icon_id = (int) noav_site_setting( 'favicon_id', 0 );
$noav_icon_url = $noav_icon_id ? wp_get_attachment_image_url( $noav_icon_id, 'full' ) : NOAV_URI . '/img/1dtcc2gE5qyWZ3T5yiqCAvh2gWrgRRvFl0j4TUd0ArXkHOSGt8j16ZdsxH5jqmdX.png';
$noav_navigation = noav_navigation_items( $noav_landing ? 'global' : $noav_unit );
?><!DOCTYPE html>
<html <?php language_attributes(); ?> style="scroll-padding-top: 128px; --head-fontFamily: 'Merriweather', serif; --head-fontWeight: 700; --head-fontStyle: normal; --body-fontFamily: 'Nunito', sans-serif; --body-fontWeight: 400; --body-fontStyle: normal; --body-fontHeight: 1; --header-logo-fontFamily: 'Bree Serif', serif; --footer-logo-fontFamily: 'Bree Serif', serif; --header-logo-fontWeight: 400; --footer-logo-fontWeight: 400;">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<?php if ( ! has_site_icon() ) : ?><link rel="icon" href="<?php echo esc_url( $noav_icon_url ); ?>"><?php endif; ?>
	<meta name="keywords" content="HVB, behandlingshem, ungdomar, Noav AB, Hörby, Vinkelviken, Söderbacken, Kyrkhult, Sweden">
	<meta property="og:type" content="website">
	<meta name="twitter:card" content="summary">

	<?php wp_head(); ?>
</head>
<body <?php body_class( 'overflow-x-hidden' ); ?>>
<?php wp_body_open(); ?>
<div id="__next">
	<style>
		#nprogress { pointer-events: none; }
		#nprogress .bar { background: #2A2924; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 3px; }
	</style>
	<div id="main-body" class="flex flex-col h-full overflow-y-auto overflow-x-hidden smooth-scroll transition-all">
	<header id="website-header" class="!z-[2000] transition-colors duration-300 sticky top-0" style="background-color: rgb(255, 255, 255); color: rgb(17, 24, 39);">
		<div class="relative z-10 grid items-center lg:gap-6 xl:gap-10 mx-auto pt-8 pb-8 px-6 lg:px-12" style="grid-template-columns:auto minmax(0,1fr) auto">
			<div>
				<a class="max-w-full overflow-hidden grid" target="_self" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Till startsidan">
					<img class="noav-desktop-logo hidden lg:block object-contain" src="<?php echo esc_url( $noav_logo_url ); ?>" alt="<?php echo esc_attr( $noav_logo_alt ?: get_bloginfo( 'name' ) ); ?>">
					<img class="lg:hidden transition-all object-contain" src="<?php echo esc_url( $noav_logo_url ); ?>" alt="<?php echo esc_attr( $noav_logo_alt ?: get_bloginfo( 'name' ) ); ?>" style="height:64px">
				</a>
			</div>
			<nav class="hidden lg:flex items-center justify-end gap-10" aria-label="<?php echo $noav_landing ? 'Huvudmeny' : 'Enhetsmeny'; ?>">
				<ul class="items-center flex-wrap flex justify-end gap-x-6" style="color: rgb(17, 24, 39);">
					<?php if ( ! $noav_landing ) : ?>
					<li class="border-b-2" style="border-color:transparent"><a class="block body-normal whitespace-nowrap py-1.5" style="opacity:.65" href="<?php echo esc_url( home_url( '/' ) ); ?>">‹ Alla enheter</a></li>
					<?php endif; ?>
					<?php foreach ( $noav_navigation as $nav_item ) : ?>
					<?php $is_current = (int) $nav_item['id'] === get_queried_object_id(); ?>
					<li class="border-b-2" style="border-color:transparent"><a class="block body-normal whitespace-nowrap py-1.5" href="<?php echo esc_url( $nav_item['url'] ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $nav_item['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $noav_landing ) { noav_render_social_links(); } ?>
			</nav>
			<?php include get_template_directory() . '/parts/_hamburger.html'; ?>
		</div>
	</header>
	<div class="noav-header-spacer" aria-hidden="true"></div>
