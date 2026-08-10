# Noav AB — webbplatsdemo

En designdemo för en ny webbplats åt **Noav AB** (org.nr 559452-7045), som driver HVB-hem för ungdomar 13–17 år. Demon består av en huvudsida och två enhetssidor — **Vinkelvikens HVB** (Hörby) och **Kyrkhults HVB** (Olofströms kommun) — med sajtens huvudfunktion: en levande räknare för **lediga platser per enhet**.

Detta är en ren statisk sajt (HTML + CSS + vanilla JS) utan backend och utan byggsteg. Animationerna drivs av GSAP + ScrollTrigger och Lenis via CDN.

> **WordPress-version finns i [`wp/`](wp/README.md).** Samma design, men innehållet redigeras i wp-admin och lediga platser är riktiga fält som personalen ändrar själva — den adminpanel som beskrivs under "Nästa fas" längre ner. Den statiska demon ligger kvar här som referens.

## Öppna demon

Två sätt:

1. **Dubbelklicka på `index.html`** — sajten fungerar direkt via `file://`, inga moduler eller fetch-anrop används.
2. **Servera lokalt** (valfritt):

   ```bash
   npx serve
   # eller
   python3 -m http.server
   ```

   Öppna sedan adressen som skrivs ut (t.ex. `http://localhost:3000`).

Observera att typsnitt och animationsbibliotek laddas från CDN, så internetuppkoppling behövs för fullt utseende. Utan uppkoppling visas allt innehåll ändå, med systemtypsnitt och utan animationer.

## Ändra lediga platser

Källan för lediga platser är **`js/availability.js`** — redigera siffrorna där:

```js
window.NOAV_AVAILABILITY = {
  updatedAt: "2026-07-07",
  units: {
    vinkelviken: { name: "Vinkelvikens HVB", total: 7, available: 2 },
    kyrkhult:    { name: "Kyrkhults HVB",    total: 6, available: 1 }
  }
};
```

### Live-demo via URL-parametrar

Under en pitch kan siffrorna ändras direkt i adressfältet, utan att röra koden:

```
index.html?vinkelviken=3&kyrkhult=0
vinkelviken.html?vinkelviken=5
```

Parametrarna (heltal ≥ 0) skriver över `available` för respektive enhet innan sidan renderas. Värden över enhetens `total` begränsas till `total`.

Statuslogiken är: **3 eller fler** lediga platser → "God tillgänglighet" (grön), **1–2** → "Begränsat antal platser" (bärnsten), **0** → "Inga lediga platser just nu" (grå).

## Vad som är platshållare

- **All enhetsspecifik information om Kyrkhult** — adress, telefonnummer, personal, skol- och fritidssamarbeten är markerade "[Platshållare: kompletteras]" i sidan.
- **E-postadresser** — samtliga är markerade "(platshållare)".
- **Totalt antal platser** (`total` i `availability.js`) — placeholdersiffror som ska ersättas med verkliga tillståndsgivna platsantal.
- **Bildmaterial** — Vinkelvikens foton (i `img/`) är hämtade som lokala kopior från nuvarande noav.se, med ägarens godkännande. Kyrkhult saknar ännu fotografier och använder gradientillustrationer i CSS tills egna bilder finns. En stockbild (Getty via Durable) på gamla sajten har medvetet inte återanvänts, eftersom dess licens inte följer med till en egen webbplats.

## Nästa fas: adminpanel

Nästa steg är en liten backend, t.ex. en Supabase-tabell `units(id, name, total, available)` med en inloggningsskyddad redigeringssida där personalen själva uppdaterar platsantalet. Frontenden är redan förberedd: funktionen `getAvailability()` i `js/main.js` är den enda punkt där sidorna läser sin data, och byts då ut mot ett API-anrop — utan att något annat i sidorna behöver ändras.

## Filstruktur

```
index.html          # Noav AB — huvudsida
vinkelviken.html    # Enhetssida: Vinkelvikens HVB (Hörby, Skåne)
kyrkhult.html       # Enhetssida: Kyrkhults HVB (Olofströms kommun, Blekinge)
css/styles.css      # Gemensamt designsystem (CSS custom properties)
js/availability.js  # KÄLLAN för lediga platser
js/main.js          # Rendering, navigering och animationer
img/                # Lokala foton från noav.se (Vinkelviken)
```

---

*Demo — ej publicerad webbplats. Allt textinnehåll är originalskrivet för denna demo.*
