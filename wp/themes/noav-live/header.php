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
?><!DOCTYPE html>
<html <?php language_attributes(); ?> style="scroll-padding-top: 128px; --head-fontFamily: 'Merriweather', serif; --head-fontWeight: 400; --head-fontStyle: normal; --body-fontFamily: 'Nunito', sans-serif; --body-fontWeight: 400; --body-fontStyle: normal; --body-fontHeight: 1.25; --header-logo-fontFamily: 'Bree Serif', serif; --footer-logo-fontFamily: 'Bree Serif', serif; --header-logo-fontWeight: 400; --footer-logo-fontWeight: 400;">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="icon" href="<?php echo NOAV_URI; ?>/img/1dtcc2gE5qyWZ3T5yiqCAvh2gWrgRRvFl0j4TUd0ArXkHOSGt8j16ZdsxH5jqmdX.png">
	<meta name="keywords" content="HVB, behandlingshem, ungdomar, Noav AB, Hörby, Vinkelviken, Söderbacken, Kyrkhult, Sweden">
	<meta property="og:type" content="website">
	<meta name="twitter:card" content="summary">
	<meta name="robots" content="all">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'overflow-x-hidden' ); ?>>
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
					<img class="noav-desktop-logo hidden lg:block object-contain" src="<?php echo NOAV_URI; ?>/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png" alt="Noav">
					<img class="lg:hidden transition-all object-contain" src="<?php echo NOAV_URI; ?>/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png" alt="Noav" style="height:64px">
				</a>
			</div>
			<nav class="hidden lg:flex items-center justify-end gap-10" aria-label="<?php echo $noav_landing ? 'Huvudmeny' : 'Enhetsmeny'; ?>">
				<ul class="items-center flex-wrap flex justify-end gap-x-6" style="color: rgb(17, 24, 39);">
					<?php if ( ! $noav_landing ) : ?>
					<li class="border-b-2" style="border-color:transparent"><a class="block body-normal whitespace-nowrap py-1.5" style="opacity:.65" href="<?php echo esc_url( home_url( '/' ) ); ?>">‹ Alla enheter</a></li>
					<?php endif; ?>
					<?php foreach ( $noav_landing ? noav_global_sections() : noav_sections() as $slug => $label ) : ?>
					<?php $is_current = $slug === $noav_section; ?>
					<li class="border-b-2" style="border-color:transparent"><a class="block body-normal whitespace-nowrap py-1.5" href="<?php echo esc_url( home_url( $noav_landing ? "/$slug/" : "/$noav_unit/$slug/" ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $noav_landing ) { include get_template_directory() . '/parts/_social.html'; } ?>
			</nav>
			<?php include get_template_directory() . '/parts/_hamburger.html'; ?>
		</div>
	</header>
	<div class="noav-header-spacer" aria-hidden="true"></div>
