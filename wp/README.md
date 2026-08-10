# Noav AB — WordPress

Migrering av den statiska demon (i repots rot) till WordPress. Designen är
densamma; skillnaden är att innehållet nu redigeras i wp-admin istället för i
kod, och att **lediga platser** är riktiga fält som personalen ändrar själva.

## Demo i webbläsaren

**[Öppna demon →](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2Fbocky-hub%2Fnoav-demo%2Fmain%2Fwp%2Fplayground%2Fblueprint.json)**

Kör på WordPress Playground: hela WordPress kompilerat till WebAssembly, i
besökarens egen webbläsare. Ingen hosting, ingen inloggning — den som öppnar
länken är redan administratör och kan redigera allt.

Två saker att veta innan du delar den:

- **Ändringar sparas inte.** Sidan laddas om och allt är tillbaka som det var.
  Det är en sandlåda, inte en webbplats. Var och en som öppnar länken får sin
  egen kopia, så en kollega kan klicka runt utan att påverka någon annan.
- **Första bootningen tar en stund** — WordPress, PHP och temat laddas ner
  innan sajten visas.

Blueprinten som beskriver demon ligger i [`playground/blueprint.json`](playground/blueprint.json).
Den hämtar temat direkt från `main` i det här repot, så en push slår igenom i
demon (raw-innehållet cachas i fem minuter).

Vill du köra demon med sparade ändringar: i Playground, välj **Site Settings →
Storage → Browser storage**. Då lever kopian kvar i din webbläsare.

## Kom igång lokalt

Kräver Docker.

```bash
cd wp && docker compose up -d && ./bin/seed.sh
```

Sajten: <http://localhost:8080> · wp-admin: <http://localhost:8080/wp-admin>
(`noav` / `noav`).

`seed.sh` är idempotent — kör den igen för att återställa demoinnehållet efter
att ha klickat sönder något. Stoppa med `docker compose down`; lägg till `-v`
för att även slänga databasen.

## Så ändrar personalen lediga platser

**Enheter → välj enhet → rutan "Lediga platser & enhetsfakta"** i högerkanten.
Skriv in antalet, tryck Uppdatera. Datumet "Platsantalet uppdaterat" sätts
automatiskt när siffran ändras — ingen behöver komma ihåg det.

Siffran syns direkt på fem ställen: statusraden i startsidans hjältebild,
platskorten, enhetskortens märken, enhetssidans stora siffra och statustexten.
Alla läser samma fält, så de kan inte glida ifrån varandra.

Anger man fler lediga än totala platser kapas värdet till totalen. "9 av 7
platser" är alltid ett skrivfel, och en HVB-sajt som visar fel platsantal är
värre än en som visar inget.

Statuslogiken: **3+** → God tillgänglighet (grön) · **1–2** → Begränsat antal
platser (bärnsten) · **0** → Inga lediga platser just nu (grå).

Översikt över båda enheterna finns i listan under **Enheter** — platsantal,
status och datum som egna kolumner.

## Var innehållet bor

| Vad | Var det redigeras |
| --- | --- |
| Lediga platser, totalt antal, ort, adress, telefon, e-post, kort beskrivning | Enheter → enhetens metabox |
| Enhetens hjältebild | Enheter → Utvald bild |
| Enhetens ingress (texten i hjältebilden) | Enheter → Utdrag |
| All brödtext, rubriker, kort och listor | Blockredigeraren på respektive sida |
| Org.nr, sidfotstext, huvudnummer, sociala länkar, kontaktpersoner | Inställningar → Noav |
| Menyn | Utseende → Menyer |

Kontaktpersonerna under Inställningar → Noav används både i startsidans
kontaktsektion och i kontaktkorten på enhetssidorna.

## Temats uppbyggnad

```
themes/noav/
  functions.php              # laddar inc/
  inc/setup.php              # theme supports, menyer, köade filer
  inc/units.php              # enheter (CPT) + platsdata — DATAKÄLLAN
  inc/settings.php           # Inställningar → Noav
  inc/blocks.php             # sex serverrenderade block
  inc/patterns.php           # mönsterkategorier + delade mönsterdelar
  inc/class-noav-nav-walker.php
  patterns/                  # blockmönster: startsida + två enhetssidor
  assets/css/styles.css      # designsystemet, oförändrat från demon
  assets/js/main.js          # animationer + rendering
  assets/js/blocks.js        # redigerarsidan för blocken (ingen byggkedja)
  header.php footer.php index.php page.php front-page.php
  single-noav_unit.php
```

Två sorters innehåll, av olika skäl:

- **Vanliga Gutenberg-block** för all text. Därför kan varje rubrik och stycke
  ändras utan att någon rör kod.
- **Sex serverrenderade block** (`noav/…`) för allt som läser platsdata. De
  måste renderas vid varje sidvisning — en siffra som frusits fast när sidan
  senast redigerades vore precis det problem migreringen skulle lösa.

Siffrorna renderas både på servern och av `main.js`. Serverrenderingen gör att
de finns i HTML-källan, vilket sökmotorer läser och som gör att besökaren
aldrig ser en nolla blinka förbi innan JavaScript hunnit igång.

### Om du behöver ändra statuslogiken

Den finns på två ställen som måste hållas i takt: `noav_status()` i
`inc/units.php` och `getStatus()` i `assets/js/main.js`. Ändrar du bara den ena
kan servern och webbläsaren visa olika text för samma siffra.

## Vad som inte följde med

- **URL-överstyrning** (`?vinkelviken=3`) fungerade i demon för att kunna ändra
  siffrorna live under en pitch. Den är avstängd som standard här: på en
  publicerad sajt kan vem som helst annars skärmdumpa en sida som visar ett
  platsantal som inte stämmer. Slå på med filtret `noav_allow_url_override`.
- **Hjältebildens radvisa textanimation** krävde `<span class="line">` runt
  varje rad, vilket blockredigeraren inte kan skapa. Rubriken tonar nu in som
  helhet istället. `main.js` behåller radlogiken, så mönster som har
  radstrukturen animeras fortfarande radvis.
- **Kyrkhults platshållartexter i kontaktkortet** är nu tomma fält på enheten i
  stället för `[Platshållare: …]`-text. Fyll i dem när uppgifterna finns.

## Innan sajten publiceras

1. **Hosting.** WordPress kräver PHP och MySQL — Vercel kan inte köra det.
   Sajten behöver flyttas till PHP-hosting (Oderland, Loopia, Binero, One.com
   eller en WP-specialist). Temat är portabelt: zippa `themes/noav` och ladda
   upp under Utseende → Teman → Lägg till.
2. **Stäng av demo-märkningen** under Inställningar → Noav.
3. **Ersätt platshållarna** — e-postadresser, Kyrkhults adress och
   direktnummer, och de verkliga tillståndsgivna platsantalen.
4. **Byt lösenordet.** `noav`/`noav` är utvecklingsuppgifter och får inte följa
   med till en publik server.
5. **Överväg att självhosta typsnitten.** De laddas nu från Google Fonts, vilket
   innebär att besökarnas IP-adresser skickas till Google. Tyska domstolar har
   funnit det oförenligt med GDPR, och för en verksamhet som behandlar
   uppgifter om placerade ungdomar är det värt att undvika — även om det bara
   gäller besökare på webbplatsen. Lägg `.woff2`-filerna i `assets/fonts/` och
   peka om `noav-fonts` i `inc/setup.php`.
6. **Överväg att självhosta GSAP och Lenis** av samma skäl, och för att sajten
   ska fungera oberoende av jsDelivr. `main.js` klarar redan att de uteblir:
   utan dem visas allt innehåll, utan animationer.
7. **Säkerhetskopiering och uppdateringar.** WordPress behöver löpande
   uppdateringar på ett sätt en statisk sajt inte gör. Fråga hostingleverantören
   om automatiska säkerhetskopior.
