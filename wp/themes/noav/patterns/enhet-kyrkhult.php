<?php
/**
 * Title: Enhetssida — Kyrkhult
 * Slug: noav/enhet-kyrkhult
 * Categories: noav-pages
 * Description: Sektionerna på Kyrkhults enhetssida, med platshållare för det som kompletteras när enheten etableras.
 * Keywords: enhet, kyrkhult, olofström, blekinge
 * Viewport Width: 1400
 * Inserter: true
 *
 * Texter markerade med klassen "placeholder" ska ersättas i redigeraren när
 * uppgifterna finns. Ta bort span-taggen samtidigt som texten byts, annars
 * står den kvar i den avvikande stilen.
 *
 * @package Noav
 */

declare( strict_types = 1 );
?>

<!-- wp:group {"tagName":"section","className":"section","anchor":"om","layout":{"type":"default"}} -->
<section class="wp-block-group section" id="om">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">
<!-- wp:group {"className":"split","layout":{"type":"default"}} -->
<div class="wp-block-group split">

<!-- wp:group {"className":"prose","layout":{"type":"default"}} -->
<div class="wp-block-group prose">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Om enheten</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Samma grund — mitt i Blekinges skogsbygd</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Kyrkhult ligger i Olofströms kommun, i en naturnära del av Blekinge. Här bygger vi upp en enhet med samma hemlika prägel som Vinkelviken: en liten, lugn miljö med tydliga rutiner, gemensamma måltider och vuxna som alltid finns nära.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Personalgruppen sätts samman av behandlingspedagoger och socionomer med erfarenhet av svår beteendeproblematik samt neuropsykiatriska och psykiatriska svårigheter. <span class="placeholder">[Platshållare: namn på föreståndare och personalgrupp kompletteras.]</span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Enheten arbetar under samma kvalitetsledningssystem och med samma metodik som övriga Noav — det som skiljer är platsen, inte grundtryggheten.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:html -->
<div class="art-panel art-kyrkhult" role="img" aria-label="Illustration i petrolblå och skogsgröna toner som föreställer ett blekingskt skogslandskap"></div>
<!-- /wp:html -->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<?php noav_pattern_methods_section(); ?>

<!-- wp:group {"tagName":"section","className":"section","anchor":"stod","layout":{"type":"default"}} -->
<section class="wp-block-group section" id="stod">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Stöd runt ungdomen</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Ett helt team — inte bara ett boende</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede">Runt varje ungdom finns ett nätverk av kompetenser som kopplas in utifrån behov och uppdrag. Strukturen speglar Vinkelvikens — de lokala samarbetena etableras nu.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"support-grid","layout":{"type":"default"}} -->
<div class="wp-block-group support-grid">
<?php
noav_pattern_card(
	NOAV_ICON_PSYCH,
	'Psykologstöd',
	'Samtal och bedömningar erbjuds inom ramen för placeringen, via Noavs samarbetande psykologtjänst.'
);
noav_pattern_card(
	NOAV_ICON_CLIPBOARD,
	'NPF-utredning',
	'Vid behov genomförs neuropsykiatrisk utredning via samarbetande psykologtjänst, så att rätt stöd kan sättas in tidigt.'
);
?>
<!-- wp:group {"tagName":"article","className":"method-card","layout":{"type":"default"}} -->
<article class="wp-block-group method-card">
<!-- wp:html -->
<span class="icon-badge" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><?php echo NOAV_ICON_CROSS; // phpcs:ignore WordPress.Security.EscapeOutput -- fast SVG. ?></svg></span>
<!-- /wp:html -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Hälsa &amp; sjukvårdskontakt</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ungdomarnas hälsa följs upp löpande. <span class="placeholder">[Platshållare: sjukskötersketjänst för enheten kompletteras.]</span></p>
<!-- /wp:paragraph -->
</article>
<!-- /wp:group -->

<?php
noav_pattern_card(
	NOAV_ICON_CHAT,
	'Familjesamtal & nätverksarbete',
	'Vi arbetar aktivt med familj och nätverk — relationerna hemma är ofta en avgörande del av en hållbar förändring.'
);
?>
<!-- wp:group {"tagName":"article","className":"method-card","layout":{"type":"default"}} -->
<article class="wp-block-group method-card">
<!-- wp:html -->
<span class="icon-badge" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><?php echo NOAV_ICON_SHIELD; // phpcs:ignore WordPress.Security.EscapeOutput -- fast SVG. ?></svg></span>
<!-- /wp:html -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Lokal samverkan</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Samverkan med polis och andra lokala aktörer byggs upp enligt samma modell som i Hörby. <span class="placeholder">[Platshållare: kontaktperson kompletteras.]</span></p>
<!-- /wp:paragraph -->
</article>
<!-- /wp:group -->

<?php
noav_pattern_card(
	NOAV_ICON_ARROW,
	'Utsluss & eftervård',
	'Utslussen planeras tillsammans med placerande kommun i god tid, med målet att övergången till nästa steg blir trygg och hållbar.'
);
?>
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section section--tint","anchor":"vardag","layout":{"type":"default"}} -->
<section class="wp-block-group section section--tint" id="vardag">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">
<!-- wp:group {"className":"split","layout":{"type":"default"}} -->
<div class="wp-block-group split">

<!-- wp:html -->
<div class="art-panel art-vinkelviken" role="img" aria-label="Illustration i petrolblå och salviagröna toner som föreställer natur och friluftsliv"></div>
<!-- /wp:html -->

<!-- wp:group {"className":"prose","layout":{"type":"default"}} -->
<div class="wp-block-group prose">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Vardag &amp; fritid</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">En vardag som liknar alla andras</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Även i Kyrkhult är den fungerande vardagen en del av behandlingen: fast veckorytm med skola, aktiviteter och återhämtning — och en aktiv fritid i en av Sveriges mest naturnära miljöer.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"check-list"} -->
<ul class="wp-block-list check-list">
<!-- wp:list-item --><li>Skola eller daglig sysselsättning med läxhjälp — <span class="placeholder">[Platshållare: lokala skolsamarbeten kompletteras]</span></li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Anpassade vardagsaktiviteter måndag–fredag</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Aktiv fritid med gemensamma aktiviteter</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Nära till skog och sjö, med en friluftsdag i veckan</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Lokala fritidssamarbeten — <span class="placeholder">[Platshållare: föreningar och aktörer kompletteras]</span></li><!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<?php
noav_pattern_contact_section(
	'Kontakta Kyrkhult',
	'Under uppbyggnaden hanteras alla placeringsförfrågningar för Kyrkhult av verksamhetschefen. Ring så berättar vi om aktuell tillgänglighet och planerna för enheten.',
	'kyrkhult'
);
