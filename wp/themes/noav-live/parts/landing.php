<?php
/**
 * Landing / unit chooser. No top menu — the visitor picks a unit here and
 * the unit's own menu appears once inside. Styled with the noav.se design
 * system (same fonts, cream palette, discreet brown accent, rounded imagery).
 *
 * Minimalist direction: generous whitespace, quiet typography, the accent
 * color used sparingly — a hairline mark, the labels, the headings, the
 * button. No card boxes, no heavy shadows, no gradient washes.
 *
 * Kept as plain HTML + only NOAV_URI / home_url tokens, plus
 * noav_platser_badge() — antalet lediga platser per enhet, redigerbart i
 * wp-admin → Lediga platser.
 */
?>
<section style="background-color: rgb(250, 247, 240);">
	<div class="container mx-auto px-6" style="padding-top:clamp(3.5rem, 6vw, 7rem);padding-bottom:clamp(4.5rem, 8vw, 8.5rem);">

		<!-- Intro -->
		<div class="flex flex-col items-center text-center mx-auto" style="max-width:34rem;gap:1.25rem;margin-bottom:clamp(3.5rem, 7vw, 6.5rem);">
			<span style="display:block;width:44px;height:2px;background-color:rgb(82,63,41);border-radius:2px;"></span>
			<h1 class="heading-large" style="color:rgb(17,24,39);">Våra verksamheter</h1>
			<p class="body-normal" style="color:rgb(17,24,39);opacity:.7;">Noav driver behandling och boende med värme, struktur och hög personaltäthet — välj den enhet du vill läsa mer om.</p>
		</div>

		<!-- Enheter -->
		<div class="grid grid-cols-1 lg:grid-cols-2" style="max-width:68rem;margin:0 auto;gap:clamp(3rem, 6vw, 5.5rem);">

			<!-- Vinkelviken -->
			<article class="flex flex-col items-center text-center">
				<a href="<?php echo esc_url( home_url( '/vinkelviken' ) ); ?>" class="block w-full aspect-w-16 aspect-h-9 rounded-2xl md:rounded-3xl lg:rounded-4xl" style="overflow:hidden;box-shadow:0 10px 28px -20px rgba(60,45,30,.3);">
					<img class="object-cover" src="<?php echo NOAV_URI; ?>/img/dKi4ztLDoMxA28pmF0jTQWJ9UJBgmaT8YB32FkMnPZCaLfrXnBlNA2jHIdermPmy.jpg" alt="Vinkelviken" style="object-position:center 55%;">
				</a>

				<div class="body-normal" style="margin-top:2.25rem;font-size:12px;letter-spacing:.16em;text-transform:uppercase;color:rgb(82,63,41);font-weight:600;">HVB-hem</div>
				<h2 class="heading-large" style="color:rgb(82,63,41);margin-top:.4rem;">Vinkelviken</h2>

				<div style="display:flex;align-items:center;justify-content:center;gap:14px;margin:1.5rem auto 0;color:rgb(82,63,41);width:150px;opacity:.4;">
					<span style="flex:1;height:1px;background:currentColor;"></span>
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"/><path d="M5 10v9h14v-9"/><path d="M10 19v-5h4v5"/></svg>
					<span style="flex:1;height:1px;background:currentColor;"></span>
				</div>

				<?php noav_platser_badge( 'vinkelviken' ); ?>

				<p class="body-normal" style="color:rgb(17,24,39);opacity:.78;max-width:26rem;margin:1.25rem auto 0;">Lugnt och familjärt HVB i Hörby för ungdomar 13–17 år, som arbetar lågaffektivt och kravanpassat.</p>

				<a href="<?php echo esc_url( home_url( '/vinkelviken' ) ); ?>" style="margin-top:2.25rem;display:inline-flex;align-items:center;justify-content:center;border-width:2px;border-style:solid;background-color:rgb(82,63,41);color:rgb(255,255,255);border-radius:40px;border-color:rgb(82,63,41);padding:14px 34px;font-family:var(--body-fontFamily);font-weight:600;">Läs mer →</a>
			</article>

			<!-- Kyrkhult (platshållare — ny enhet) -->
			<article class="flex flex-col items-center text-center">
				<a href="<?php echo esc_url( home_url( '/kyrkhult' ) ); ?>" class="block w-full aspect-w-16 aspect-h-9 rounded-2xl md:rounded-3xl lg:rounded-4xl" style="overflow:hidden;background:linear-gradient(135deg,#e8dcc8,#d8c6a8);">
					<span style="display:flex;flex-direction:column;align-items:center;justify-content:center;color:#8a785c;">
						<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="opacity:.75;"><path d="M3 11l9-7 9 7"/><path d="M5 10v9h14v-9"/><path d="M10 19v-5h4v5"/></svg>
						<span class="body-normal" style="margin-top:.75rem;letter-spacing:.14em;text-transform:uppercase;font-size:12px;opacity:.85;">Bild kommer</span>
					</span>
				</a>

				<div class="body-normal" style="margin-top:2.25rem;font-size:12px;letter-spacing:.16em;text-transform:uppercase;color:rgb(82,63,41);font-weight:600;">HVB-hem · Kommer snart</div>
				<h2 class="heading-large" style="color:rgb(82,63,41);margin-top:.4rem;">Kyrkhult</h2>

				<div style="display:flex;align-items:center;justify-content:center;gap:14px;margin:1.5rem auto 0;color:rgb(82,63,41);width:150px;opacity:.4;">
					<span style="flex:1;height:1px;background:currentColor;"></span>
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"/><path d="M5 10v9h14v-9"/><path d="M10 19v-5h4v5"/></svg>
					<span style="flex:1;height:1px;background:currentColor;"></span>
				</div>

				<?php noav_platser_badge( 'kyrkhult' ); ?>

				<p class="body-normal" style="color:rgb(17,24,39);opacity:.78;max-width:26rem;margin:1.25rem auto 0;">Ny enhet på Vilshultsvägen 15. Mer information om Kyrkhult kommer snart.</p>

				<a href="<?php echo esc_url( home_url( '/kyrkhult' ) ); ?>" style="margin-top:2.25rem;display:inline-flex;align-items:center;justify-content:center;border-width:2px;border-style:solid;background-color:rgb(82,63,41);color:rgb(255,255,255);border-radius:40px;border-color:rgb(82,63,41);padding:14px 34px;font-family:var(--body-fontFamily);font-weight:600;">Läs mer →</a>
			</article>

		</div>
	</div>
</section>
