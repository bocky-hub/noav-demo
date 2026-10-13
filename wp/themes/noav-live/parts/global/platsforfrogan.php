<?php
/** Shared contact information, keeping the existing content for each unit. */
foreach ( noav_units() as $unit => $name ) :
?>
<div class="container mx-auto px-6 pt-8 lg:pt-12">
	<h1 class="heading-medium" style="color:rgb(82,63,41);"><?php echo esc_html( $name ); ?></h1>
</div>
<?php
	include get_template_directory() . '/parts/' . $unit . '/platsforfrogan.php';
endforeach;
?>
