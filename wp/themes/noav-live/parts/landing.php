<?php
/**
 * Landing / unit chooser. Shared top menu — the visitor picks a unit here and
 * the unit's own menu appears once inside. Styled with the noav.se design
 * system (same fonts, cream palette, discreet brown accent, rounded imagery).
 *
 * Minimalist direction: generous whitespace, quiet typography, the accent
 * color used sparingly — a hairline mark, the labels, the headings, the
 * button. No card boxes, no heavy shadows, no gradient washes.
 *
 * Unit names, locations and photographs follow the original design.
 */
?>
<section style="background-color: rgb(250, 247, 240);">
	<div class="container mx-auto px-6" style="padding-top:clamp(3.5rem, 6vw, 7rem);padding-bottom:clamp(4.5rem, 8vw, 8.5rem);">

		<!-- Enheter -->
		<div class="grid grid-cols-1 lg:grid-cols-2" style="max-width:68rem;margin:0 auto;gap:clamp(3rem, 6vw, 5.5rem);">

			<!-- Vinkelviken -->
			<article class="flex flex-col items-center text-center">
				<h2 class="heading-large" style="color:rgb(82,63,41);margin-top:0;"><a href="<?php echo esc_url( home_url( '/vinkelviken/' ) ); ?>" style="color:inherit;text-decoration:none">Vinkelviken</a></h2>
				<div class="body-normal" style="margin-top:.65rem;font-size:12px;letter-spacing:.16em;color:rgb(82,63,41);font-weight:600;">HVB</div>
				<p class="body-normal" style="color:rgb(17,24,39);opacity:.78;margin-top:.75rem;">Hörby, Skåne</p>

				<div style="display:flex;align-items:center;justify-content:center;gap:14px;margin:1.5rem auto 0;color:rgb(82,63,41);width:150px;opacity:.4;">
					<span style="flex:1;height:1px;background:currentColor;"></span>
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"/><path d="M5 10v9h14v-9"/><path d="M10 19v-5h4v5"/></svg>
					<span style="flex:1;height:1px;background:currentColor;"></span>
				</div>

				<a href="<?php echo esc_url( home_url( '/vinkelviken' ) ); ?>" class="block w-full aspect-w-16 aspect-h-9 rounded-2xl md:rounded-3xl lg:rounded-4xl" style="margin-top:2.25rem;overflow:hidden;box-shadow:0 10px 28px -20px rgba(60,45,30,.3);">
					<img class="object-cover" src="<?php echo NOAV_URI; ?>/img/dKi4ztLDoMxA28pmF0jTQWJ9UJBgmaT8YB32FkMnPZCaLfrXnBlNA2jHIdermPmy.jpg" alt="Vinkelviken" style="object-position:center 55%;">
				</a>

			</article>

			<!-- Söderbacken -->
			<article class="flex flex-col items-center text-center">
				<h2 class="heading-large" style="color:rgb(82,63,41);margin-top:0;"><a href="<?php echo esc_url( home_url( '/kyrkhult/' ) ); ?>" style="color:inherit;text-decoration:none">Söderbacken</a></h2>
				<div class="body-normal" style="margin-top:.65rem;font-size:12px;letter-spacing:.16em;color:rgb(82,63,41);font-weight:600;">HVB</div>
				<p class="body-normal" style="color:rgb(17,24,39);opacity:.78;margin-top:.75rem;">Kyrkhult, Blekinge</p>

				<div style="display:flex;align-items:center;justify-content:center;gap:14px;margin:1.5rem auto 0;color:rgb(82,63,41);width:150px;opacity:.4;">
					<span style="flex:1;height:1px;background:currentColor;"></span>
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"/><path d="M5 10v9h14v-9"/><path d="M10 19v-5h4v5"/></svg>
					<span style="flex:1;height:1px;background:currentColor;"></span>
				</div>

				<a href="<?php echo esc_url( home_url( '/kyrkhult' ) ); ?>" class="block w-full aspect-w-16 aspect-h-9 rounded-2xl md:rounded-3xl lg:rounded-4xl" style="margin-top:2.25rem;overflow:hidden;">
					<img class="object-cover" src="<?php echo NOAV_URI; ?>/img/soderbacken-exterior.jpeg" alt="Söderbackens hus" style="object-position:center 78%;">
				</a>

			</article>

		</div>
	</div>
</section>
