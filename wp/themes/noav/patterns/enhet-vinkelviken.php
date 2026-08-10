<?php
/**
 * Title: Enhetssida — Vinkelviken
 * Slug: noav/enhet-vinkelviken
 * Categories: noav-pages
 * Description: Sektionerna på Vinkelvikens enhetssida: om enheten, metoder, stöd, vardag och kontakt.
 * Keywords: enhet, vinkelviken, hörby
 * Viewport Width: 1400
 * Inserter: true
 *
 * Hjältebilden ingår inte — den byggs av single-noav_unit.php från enhetens
 * utvalda bild, adress och platspanel.
 *
 * @package Noav
 */

declare( strict_types = 1 );

$img = NOAV_URI . '/assets/img';
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
<h2 class="wp-block-heading">Ett litet hem med stora resurser</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vinkelviken är ett hemlikt HVB i Hörby, mitt i Skåne. Här bor ungdomarna i en lugn, familjär miljö där vardagen är förutsägbar: gemensamma måltider, tydliga rutiner och vuxna som alltid finns i närheten.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Personalgruppen består av behandlingspedagoger och socionomer med lång erfarenhet av ungdomar med svår beteendeproblematik, neuropsykiatriska funktionsnedsättningar och psykiatriska tillstånd. Till verksamheten finns en psykolog knuten, vilket ger snabb tillgång till bedömningar och samtal inom ramen för placeringen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Den höga personaltätheten gör att vi kan arbeta nära varje ungdom — och anpassa både krav och stöd efter dagsform.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:html -->
<div class="art-panel art-vinkelviken has-photo"><img class="media-fill" src="<?php echo esc_url( $img . '/vinkelviken-bullar.jpg' ); ?>" alt="Nybakade kanelbullar på en plåt i Vinkelvikens kök" loading="lazy"></div>
<!-- /wp:html -->

</div>
<!-- /wp:group -->

<!-- wp:html -->
<div class="photo-row"><figure><img src="<?php echo esc_url( $img . '/vinkelviken-sovrum.jpg' ); ?>" alt="Ett av ungdomarnas rum — säng, skrivbord och fönster mot grönskan" loading="lazy"><figcaption>Eget rum</figcaption></figure><figure><img src="<?php echo esc_url( $img . '/vinkelviken-kok.jpg' ); ?>" alt="Ljust kök med utgång mot trädgården" loading="lazy"><figcaption>Köket</figcaption></figure><figure><img src="<?php echo esc_url( $img . '/vinkelviken-allrum.jpg' ); ?>" alt="Gemensamt allrum med matbord och sittgrupp" loading="lazy"><figcaption>Gemensamma ytor</figcaption></figure></div>
<!-- /wp:html -->

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
<p class="lede">Runt varje ungdom finns ett nätverk av kompetenser som kopplas in utifrån behov och uppdrag.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"support-grid","layout":{"type":"default"}} -->
<div class="wp-block-group support-grid">
<?php
noav_pattern_card(
	NOAV_ICON_PSYCH,
	'Psykolog',
	'Via BUS-mottagningen erbjuds samtal inom ramen för placeringen — med snabb start och ett samlat team runt ungdomen.'
);
noav_pattern_card(
	NOAV_ICON_CLIPBOARD,
	'NPF-utredning',
	'Vid behov genomförs neuropsykiatrisk utredning via samarbetande psykologtjänst, så att rätt stöd kan sättas in tidigt.'
);
noav_pattern_card(
	NOAV_ICON_CROSS,
	'Sjuksköterska på deltid',
	'Vår sjuksköterska har bakgrund inom BUP, vuxenpsykiatri och somatisk vård och följer upp ungdomarnas hälsa löpande.'
);
noav_pattern_card(
	NOAV_ICON_CHAT,
	'Familjesamtal & nätverksarbete',
	'Vi arbetar aktivt med familj och nätverk — relationerna hemma är ofta en avgörande del av en hållbar förändring.'
);
noav_pattern_card(
	NOAV_ICON_SHIELD,
	'Samverkan med lokal polis',
	'Enheten har en kontaktperson hos polisen och deltar i det lokala brottsförebyggande arbetet.'
);
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
<div class="art-panel art-kyrkhult has-photo"><img class="media-fill" src="<?php echo esc_url( $img . '/vinkelviken-odling.jpg' ); ?>" alt="Odlingslådor i trädgården på Vinkelviken" loading="lazy"></div>
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
<p>En fungerande vardag är en del av behandlingen. Veckan har en fast rytm med skola, aktiviteter och återhämtning — och en aktiv fritid som ger nya sammanhang att lyckas i.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"check-list"} -->
<ul class="wp-block-list check-list">
<!-- wp:list-item --><li>Skola eller daglig sysselsättning med läxhjälp — i samarbete med kommunala och privata skolor</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Anpassade vardagsaktiviteter måndag–fredag</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Aktiv fritid med gemensamma aktiviteter</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Nära till naturen, med en friluftsdag i veckan</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Samarbete med lokal ryttarförening — ridskola eller praktik för den som vill</li><!-- /wp:list-item -->
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
	'Kontakta Vinkelviken',
	'Ring oss gärna direkt vid en placeringsförfrågan — vi svarar på frågor om tillgänglighet, målgrupp och hur en placering hos oss går till.',
	'vinkelviken'
);
