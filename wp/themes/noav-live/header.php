<?php
/**
 * Header — reproduces the noav.se document shell, now unit-aware.
 *
 * On the landing page (front page) the top menu is hidden — only the logo
 * shows. Inside a unit (vinkelviken / kyrkhult) the full menu appears,
 * scoped to that unit, plus an "Alla enheter" link back to the landing.
 *
 * @package Noav_Live
 */
$noav_unit    = function_exists( 'noav_current_unit' ) ? noav_current_unit() : '';
$noav_landing = ( '' === $noav_unit );
?><!DOCTYPE html>
<html <?php language_attributes(); ?> style="scroll-padding-top: 128px; --head-fontFamily: 'Merriweather', serif; --head-fontWeight: 400; --head-fontStyle: normal; --body-fontFamily: 'Nunito', sans-serif; --body-fontWeight: 400; --body-fontStyle: normal; --body-fontHeight: 1.25; --header-logo-fontFamily: 'Bree Serif', serif; --footer-logo-fontFamily: 'Bree Serif', serif; --header-logo-fontWeight: 400; --footer-logo-fontWeight: 400;">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="icon" href="<?php echo NOAV_URI; ?>/img/1dtcc2gE5qyWZ3T5yiqCAvh2gWrgRRvFl0j4TUd0ArXkHOSGt8j16ZdsxH5jqmdX.png">
	<meta name="keywords" content="HVB, behandlingshem, ungdomar, Noav AB, Hörby, Vinkelviken, Kyrkhult, Sweden">
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
<?php if ( $noav_landing ) : ?>
	<header id="website-header" class="!z-[2000] transition-colors duration-300 sticky top-0" style="background-color: rgb(255, 255, 255); color: rgb(17, 24, 39);">
		<div class="relative z-10 flex items-center justify-center mx-auto pt-8 pb-8 px-6 lg:px-12">
			<a class="max-w-full overflow-hidden grid" target="_self" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img class="hidden lg:block transition-all object-contain" src="<?php echo NOAV_URI; ?>/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png" alt="Noav" style="height:120px">
				<img class="lg:hidden transition-all object-contain" src="<?php echo NOAV_URI; ?>/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png" alt="Noav" style="height:64px">
			</a>
		</div>
	</header>
<?php else : ?>
	<header id="website-header" class="!z-[2000] transition-colors duration-300 sticky top-0" style="background-color: rgb(255, 255, 255); color: rgb(17, 24, 39);">
		<div class="relative z-10 grid items-center lg:gap-6 xl:gap-10 mx-auto pt-8 pb-8 px-6 lg:px-12" style="grid-template-columns:auto auto auto">
			<div class="col-span-2 lg:col-span-1">
				<a class="max-w-full overflow-hidden grid" target="_self" href="<?php echo esc_url( home_url( '/' . $noav_unit ) ); ?>">
					<img class="hidden lg:block transition-all object-contain" src="<?php echo NOAV_URI; ?>/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png" alt="Noav" style="height:174px">
					<img class="lg:hidden transition-all object-contain" src="<?php echo NOAV_URI; ?>/img/25ajhhe94M3miqtjN3O4vBkcRfQuAFCMFg8YG4Izo0ohYjDOSkmzxPJJMvp7n03p.png" alt="Noav" style="height:64px">
				</a>
				<div style="margin-top:8px;display:flex;align-items:center;gap:10px;">
					<span aria-hidden="true" style="width:22px;height:2px;background:rgb(82,63,41);opacity:.55;display:inline-block;flex:none;"></span>
					<span style="font-family:var(--head-fontFamily);color:rgb(82,63,41);font-size:18px;letter-spacing:.02em;line-height:1;"><?php echo esc_html( noav_units()[ $noav_unit ] ); ?></span>
				</div>
			</div>
			<div class="hidden lg:flex item-center justify-end gap-10 lg:col-span-2">
				<ul class="hidden items-center flex-wrap lg:flex justify-end gap-x-6" style="color: rgb(17, 24, 39);">
					<li class="border-b-2" style="border-color:transparent;background-color:transparent;color:currentColor"><a class="block body-normal whitespace-nowrap py-1.5" style="opacity:.65" target="_self" href="<?php echo esc_url( home_url( '/' ) ); ?>">‹ Alla enheter</a></li>
					<?php foreach ( noav_sections() as $slug => $label ) : ?>
						<li class="border-b-2" style="border-color:transparent;background-color:transparent;color:currentColor"><a class="block body-normal whitespace-nowrap py-1.5" target="_self" href="<?php echo esc_url( home_url( "/$noav_unit/$slug" ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php include get_template_directory() . '/parts/_social.html'; ?>
			</div>
			<?php include get_template_directory() . '/parts/_hamburger.html'; ?>
		</div>
	</header>
<?php endif; ?>
