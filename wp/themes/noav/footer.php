<?php
/**
 * Sidfot.
 *
 * Uppgifterna kommer från Inställningar → Noav och från enheterna, så att
 * ett ändrat telefonnummer slår igenom på alla sidor samtidigt.
 *
 * @package Noav
 */

declare( strict_types = 1 );

$noav_units   = noav_get_units();
$noav_primary = reset( $noav_units );

$noav_org       = (string) noav_setting( 'org_number', '' );
$noav_email     = (string) noav_setting( 'general_email', '' );
$noav_main      = (string) noav_setting( 'main_phone', '' );
$noav_main_tel  = noav_tel_href( $noav_main );

$noav_social = array_filter(
	array(
		(string) noav_setting( 'instagram', '' ) => (string) noav_setting( 'instagram_label', 'Instagram' ),
		(string) noav_setting( 'facebook', '' )  => __( 'Facebook', 'noav' ),
		(string) noav_setting( 'linkedin', '' )  => __( 'LinkedIn', 'noav' ),
	),
	static fn( $label, $url ): bool => '' !== $url,
	ARRAY_FILTER_USE_BOTH
);
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-grid">
			<div>
				<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">NOAV<span class="wordmark-dot">.</span></a>
				<p><?php echo esc_html( (string) noav_setting( 'tagline', '' ) ); ?></p>
				<?php if ( '' !== $noav_org ) : ?>
					<p class="footer-note">
						<?php
						printf(
							/* translators: 1: sajtens namn, 2: organisationsnummer. */
							esc_html__( '%1$s · Org.nr %2$s', 'noav' ),
							esc_html( get_bloginfo( 'name' ) ),
							esc_html( $noav_org )
						);
						?>
					</p>
				<?php endif; ?>
				<?php if ( noav_setting( 'show_demo_badge', false ) ) : ?>
					<p class="footer-note">
						<span class="demo-tag"><?php esc_html_e( 'Demo — ej publicerad webbplats', 'noav' ); ?></span>
					</p>
				<?php endif; ?>
			</div>

			<div>
				<h4><?php esc_html_e( 'Kontakt', 'noav' ); ?></h4>
				<?php if ( is_array( $noav_primary ) && '' !== $noav_primary['address'] ) : ?>
					<p>
						<?php echo esc_html( $noav_primary['name'] ); ?><br>
						<?php echo esc_html( $noav_primary['address'] ); ?>
					</p>
				<?php endif; ?>
				<p>
					<?php if ( is_array( $noav_primary ) && '' !== $noav_primary['phone'] ) : ?>
						<a href="<?php echo esc_url( 'tel:' . noav_tel_href( $noav_primary['phone'] ) ); ?>">
							<?php echo esc_html( $noav_primary['phone'] ); ?>
						</a>
						<?php esc_html_e( '(enheten)', 'noav' ); ?><br>
					<?php endif; ?>
					<?php if ( '' !== $noav_main_tel ) : ?>
						<a href="<?php echo esc_url( 'tel:' . $noav_main_tel ); ?>"><?php echo esc_html( $noav_main ); ?></a>
						<?php esc_html_e( '(verksamhetschef)', 'noav' ); ?>
					<?php endif; ?>
				</p>
				<?php if ( '' !== $noav_email ) : ?>
					<p class="footer-note">
						<a href="<?php echo esc_url( 'mailto:' . $noav_email ); ?>"><?php echo esc_html( $noav_email ); ?></a>
					</p>
				<?php endif; ?>
			</div>

			<div>
				<h4><?php esc_html_e( 'Enheter & socialt', 'noav' ); ?></h4>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				<?php else : ?>
					<ul>
						<?php foreach ( $noav_units as $noav_unit ) : ?>
							<li>
								<a href="<?php echo esc_url( $noav_unit['permalink'] ); ?>">
									<?php echo esc_html( $noav_unit['name'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
						<?php foreach ( $noav_social as $noav_url => $noav_label ) : ?>
							<li>
								<a href="<?php echo esc_url( $noav_url ); ?>" rel="noopener" target="_blank">
									<?php echo esc_html( $noav_label ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>

		<div class="footer-bottom">
			<p>
				<?php
				printf(
					/* translators: 1: årtal, 2: sajtens namn, 3: copyright-rad. */
					esc_html__( '© %1$s %2$s. %3$s', 'noav' ),
					esc_html( (string) current_time( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) ),
					esc_html( (string) noav_setting( 'copyright', '' ) )
				);
				?>
			</p>
			<p><?php echo esc_html( (string) noav_setting( 'compliance', '' ) ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
