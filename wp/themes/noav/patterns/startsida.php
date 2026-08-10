<?php
/**
 * Title: Startsida — Noav AB
 * Slug: noav/startsida
 * Categories: noav-pages
 * Description: Hela startsidan: hjältebild, lediga platser, enheter, behandling, målgrupp, kvalitet och platsförfrågan.
 * Keywords: startsida, hem, förstasida
 * Viewport Width: 1400
 * Inserter: true
 *
 * Sektionerna är vanliga Gutenberg-block, så all text kan ändras i
 * redigeraren. Undantagen är de fyra platsblocken (noav/...), som måste
 * hämta siffrorna från enheterna vid varje sidvisning, och några HTML-block
 * där markupen bär designen: hjältebildens bakgrundslager, knappraderna och
 * ikonerna. Text i HTML-block redigeras genom att klicka på blocket.
 *
 * @package Noav
 */

declare( strict_types = 1 );

$img   = NOAV_URI . '/assets/img';
$units = noav_get_units();

$vinkelviken = $units['vinkelviken']['permalink'] ?? home_url( '/' );
$kyrkhult    = $units['kyrkhult']['permalink'] ?? home_url( '/' );
$tel         = noav_tel_href( (string) noav_setting( 'main_phone', '' ) );

// Metodkorten skrivs av noav_pattern_card() i inc/patterns.php, som även
// enhetssidornas mönster använder.
$card = 'noav_pattern_card';
?>

<!-- wp:group {"tagName":"section","className":"hero","layout":{"type":"default"}} -->
<section class="wp-block-group hero">
<!-- wp:html -->
<div class="hero-bg hero-bg--photo" aria-hidden="true"><img class="media-fill" src="<?php echo esc_url( $img . '/hero-skymning.jpg' ); ?>" alt="" fetchpriority="high"></div>
<!-- /wp:html -->

<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">
<!-- wp:group {"className":"hero-inner","layout":{"type":"default"}} -->
<div class="wp-block-group hero-inner">

<!-- wp:paragraph {"className":"eyebrow hero-eyebrow"} -->
<p class="eyebrow hero-eyebrow">HVB-hem för ungdomar 13–17 år</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"hero-title"} -->
<h1 class="wp-block-heading hero-title">Ett tryggt hem, en tydlig vardag — en väg framåt.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"hero-sub"} -->
<p class="hero-sub">Noav AB driver HVB-hem för ungdomar i Hörby och Olofström. Vi arbetar lågaffektivt och kravanpassat, med hög personaltäthet, tydlig struktur och evidensbaserade metoder — alltid utifrån socialtjänstens uppdrag.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="hero-ctas"><a class="btn btn--accent" href="<?php echo esc_url( $vinkelviken ); ?>">Vinkelviken – Hörby</a><a class="btn btn--light" href="<?php echo esc_url( $kyrkhult ); ?>">Kyrkhult – Olofström</a></div>
<!-- /wp:html -->

<!-- wp:noav/availability-pill {"suffix":"just nu — båda enheterna"} /-->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:html -->
<a class="scroll-cue" href="#platser" aria-label="Bläddra ner till lediga platser"><span>Se platser</span><span class="cue-line" aria-hidden="true"></span></a>
<!-- /wp:html -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section","anchor":"platser","layout":{"type":"default"}} -->
<section class="wp-block-group section" id="platser">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Just nu</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lediga platser</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede">Aktuell tillgänglighet på våra två enheter. Ring oss gärna direkt vid en placeringsförfrågan — vi hjälper till att bedöma om vi är rätt insats för ungdomen.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:noav/availability-grid /-->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section section--tint","anchor":"enheter","layout":{"type":"default"}} -->
<section class="wp-block-group section section--tint" id="enheter">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Våra enheter</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Två hem, samma grundtrygghet</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede">Båda enheterna arbetar utifrån samma metodik och kvalitetsledningssystem, med små hemlika miljöer och nära till naturen.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:noav/unit-cards /-->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section","anchor":"behandling","layout":{"type":"default"}} -->
<section class="wp-block-group section" id="behandling">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Behandling</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Så arbetar vi</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede">Vår metodik bygger på att trygghet och goda relationer kommer först — det är då förändring blir möjlig. Arbetet är KBT-inriktat och vilar på evidensbaserade metoder.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"method-grid","layout":{"type":"default"}} -->
<div class="wp-block-group method-grid">
<?php
$card(
	'<path d="M12 19.5C7.6 16.8 5 14.1 5 11.1 5 8.8 6.7 7 8.9 7c1.2 0 2.3.6 3.1 1.5C12.8 7.6 13.9 7 15.1 7 17.3 7 19 8.8 19 11.1c0 3-2.6 5.7-7 8.4Z"/>',
	'Lågaffektivt & kravanpassat',
	'Vi möter varje ungdom med lugn och anpassar kraven efter dagsform och förmåga. Konflikter trappas ner — inte upp.'
);
$card(
	'<rect x="4.5" y="5.5" width="15" height="14" rx="2.5"/><path d="M4.5 9.5h15M8.5 3.5v3M15.5 3.5v3M8 13.5h3.5M8 16.5h5.5"/>',
	'Struktur & förutsägbarhet',
	'Tydliga rutiner, kända förväntningar och en vardag som ser likadan ut från dag till dag skapar den trygghet förändring kräver.'
);
$card(
	'<path d="M13.5 3.6a6.6 6.6 0 0 1 6.3 6.6c0 1.8-.7 3.3-1.8 4.4v5.6"/><path d="M13.5 3.6a6.6 6.6 0 0 0-6.6 6.5c0 .8-.3 1.6-.8 2.4l-.8 1.3c-.3.5-.1 1.1.5 1.3l1.6.4v2.1c0 .9.7 1.6 1.6 1.6h2.1v2"/><circle cx="13" cy="10.5" r="1.4"/>',
	'KBT-inriktat & evidensbaserat',
	'Behandlingen utgår från kognitiv beteendeterapi med metoder som rePulse/reManga och ART, anpassade efter varje ungdoms behov.'
);
$card(
	'<path d="M12 20.5v-8"/><path d="M12 12.5c0-3.6 2.6-6 6.5-6 0 3.6-2.6 6-6.5 6Z"/><path d="M12 15.5c0-2.9-2.1-4.9-5.3-4.9 0 2.9 2.1 4.9 5.3 4.9Z"/>',
	'Färdighetsträning',
	'Ungdomarna tränar sociala färdigheter, impulskontroll och vardagskompetens — både enskilt och i grupp.'
);
$card(
	'<circle cx="9" cy="8.5" r="3"/><path d="M3.5 19.5c.4-3.2 2.7-5 5.5-5s5.1 1.8 5.5 5"/><circle cx="16.8" cy="9.3" r="2.4"/><path d="M16.2 14.6c2.4.2 4 1.8 4.3 4.4"/>',
	'Hög personaltäthet',
	'Det finns alltid vuxna nära till hands. Vårt team har lång erfarenhet av svår beteendeproblematik samt neuropsykiatriska och psykiatriska svårigheter.'
);
$card(
	'<path d="M12 6.8C10.5 5.5 8.4 5 5.6 5c-.6 0-1.1.5-1.1 1.1v10.8c0 .6.5 1.1 1.1 1.1 2.8 0 4.9.5 6.4 1.8 1.5-1.3 3.6-1.8 6.4-1.8.6 0 1.1-.5 1.1-1.1V6.1c0-.6-.5-1.1-1.1-1.1-2.8 0-4.9.5-6.4 1.8Z"/><path d="M12 6.8v13"/>',
	'Skola & daglig sysselsättning',
	'Alla ungdomar har skolgång eller annan daglig sysselsättning. Vi stöttar med läxhjälp och håller tät kontakt med skolan.'
);
?>
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section section--tint","anchor":"malgrupp","layout":{"type":"default"}} -->
<section class="wp-block-group section section--tint" id="malgrupp">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">
<!-- wp:group {"className":"split","layout":{"type":"default"}} -->
<div class="wp-block-group split">

<!-- wp:group {"className":"prose","layout":{"type":"default"}} -->
<div class="wp-block-group prose">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Målgrupp</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Vilka vi tar emot</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vi tar emot flickor och pojkar i åldern 13–17 år vars hälsa och utveckling riskerar att skadas allvarligt. Många av ungdomarna har vistats i olämpliga miljöer och bär med sig erfarenheter som gör det svårt att lita på vuxna.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Det kan handla om svårigheter i det sociala samspelet, i relationer till familj och jämnåriga, eller en skolgång som inte fungerat. Vår personal har lång erfarenhet av att arbeta med svår beteendeproblematik, neuropsykiatriska funktionsnedsättningar och psykiatriska tillstånd.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Varje placering börjar med socialtjänstens uppdrag. Utifrån det formar vi en individuell planering — och är alltid ärliga i bedömningen av om vi är rätt insats för just den här ungdomen.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group">

<!-- wp:group {"className":"fact-card","layout":{"type":"default"}} -->
<div class="wp-block-group fact-card">
<!-- wp:html -->
<h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19.5C7.6 16.8 5 14.1 5 11.1 5 8.8 6.7 7 8.9 7c1.2 0 2.3.6 3.1 1.5C12.8 7.6 13.9 7 15.1 7 17.3 7 19 8.8 19 11.1c0 3-2.6 5.7-7 8.4Z"/></svg> Vi tar emot</h3>
<!-- /wp:html -->

<!-- wp:list {"className":"check-list"} -->
<ul class="wp-block-list check-list">
<!-- wp:list-item --><li>Flickor och pojkar 13–17 år</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Ungdomar vars hälsa och utveckling riskerar att skadas</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Erfarenhet av olämpliga miljöer eller otrygga sammanhang</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Svårigheter med socialt samspel, relationer eller skolgång</li><!-- /wp:list-item -->
<!-- wp:list-item --><li>Neuropsykiatrisk eller psykiatrisk problematik</li><!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"fact-card","layout":{"type":"default"}} -->
<div class="wp-block-group fact-card">
<!-- wp:html -->
<h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 8v4.5M12 15.8v.2"/></svg> Viktigt att veta</h3>
<!-- /wp:html -->

<!-- wp:paragraph -->
<p>För att kunna hålla en trygg och drogfri miljö för alla som bor hos oss tar vi inte emot ungdomar med ett aktivt missbruk eller en pågående kriminalitet. Vid osäkerhet — ring oss, så resonerar vi tillsammans om lämplig insats.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section","anchor":"kvalitet","layout":{"type":"default"}} -->
<section class="wp-block-group section" id="kvalitet">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Kvalitet &amp; trygghet</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Ordning och reda bakom omsorgen</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede">Trygg vård kräver en trygg organisation. Vårt kvalitetsarbete är systematiskt och följer Socialstyrelsens föreskrifter.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"method-grid","layout":{"type":"default"}} -->
<div class="wp-block-group method-grid">
<?php
$card(
	'<path d="M12 3.8 5.5 6.1v5.2c0 4.2 2.6 7.3 6.5 8.9 3.9-1.6 6.5-4.7 6.5-8.9V6.1L12 3.8Z"/><path d="m9.2 11.7 2 2 3.6-3.9"/>',
	'Kvalitetsledningssystem',
	'Vi arbetar enligt SOSFS 2011:9 med löpande uppföljning, utvärdering, avvikelsehantering och egenkontroll — så att kvaliteten kan visas, inte bara utlovas.'
);
$card(
	'<rect x="6" y="4" width="12" height="16.5" rx="2"/><path d="M9.5 8.5h5M9.5 12h5M9.5 15.5h3"/>',
	'Kollektivavtal',
	'Noav AB har kollektivavtal via Vårdföretagarna. Schyssta villkor ger en stabil personalgrupp — och stabila vuxna är grunden i vår behandling.'
);
$card(
	'<circle cx="12" cy="12" r="4"/><path d="M12 4v1.8M12 18.2V20M4 12h1.8M18.2 12H20M6.3 6.3l1.3 1.3M16.4 16.4l1.3 1.3M17.7 6.3l-1.3 1.3M7.6 16.4l-1.3 1.3"/>',
	'Systematiskt arbetsmiljöarbete',
	'Vi arbetar löpande och förebyggande med personalens arbetsmiljö. En trygg och frisk personalgrupp märks i ungdomarnas vardag.'
);
?>
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"section cta-band","anchor":"kontakt","layout":{"type":"default"}} -->
<section class="wp-block-group section cta-band" id="kontakt">
<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
<div class="wp-block-group container">

<!-- wp:group {"className":"section-head","layout":{"type":"default"}} -->
<div class="wp-block-group section-head">
<!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Platsförfrågan</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Har du en ungdom som behöver en plats?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lede"} -->
<p class="lede">Ring oss så berättar vi om aktuell tillgänglighet, svarar på frågor om målgrupp och metodik och resonerar öppet om vi är rätt insats. Vi återkopplar skyndsamt på alla förfrågningar från socialtjänsten.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:noav/contact-list /-->

<?php if ( '' !== $tel ) : ?>
<!-- wp:html -->
<a class="btn btn--accent" href="tel:<?php echo esc_attr( $tel ); ?>">Ring om platsförfrågan</a>
<!-- /wp:html -->
<?php endif; ?>

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
