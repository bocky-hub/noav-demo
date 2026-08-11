<?php
/**
 * Page template — renders the matching section for the current unit + slug.
 * e.g. /vinkelviken/oss/  -> parts/vinkelviken/oss.php
 *      /kyrkhult/         -> parts/kyrkhult/home.php
 *
 * @package Noav_Live
 */
get_header();

$unit    = noav_current_unit();
$section = noav_current_section();
$part    = get_template_directory() . "/parts/$unit/$section.php";

if ( $unit && file_exists( $part ) ) {
	include $part;
} else {
	?>
	<section class="relative"><div class="relative z-10 container mx-auto pt-8 lg:pt-12 pb-8 lg:pb-12"><div class="rich-text-block" style="color: rgb(17, 24, 39);">
	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>
	</div></div></section>
	<?php
}

get_footer();
