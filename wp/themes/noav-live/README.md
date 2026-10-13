# noav-live — WordPress-kopia av noav.se, uppdelad i enheter

Det här temat är en **trogen kopia av den publika webbplatsen [noav.se](https://www.noav.se)**
(Vinkelvikens HVB), byggd som ett klassiskt WordPress-tema och sedan **uppdelad i
enheter** under en gemensam ingång. Originalet är gjort i Durable (Next.js); kopian
återanvänder originalets faktiska CSS, markup, typsnitt och bilder så att sidorna
renderas likadant — men serveras nu av WordPress.

## Så fungerar sajten

- **Startsida (`/`)** — Noavs logga, den gemensamma menyn och två enhetskort.
  Enheternas rubriker, texter och platsantal visas ovanför bilderna.
- **Gemensam meny:** Om oss (`/oss/`), Platsförfrågan (`/platsforfrogan/`)
  och Kontakt (`/kontakt/`). Kontaktuppgifter och platsförfrågan visar båda enheterna.
- **Enhetsmeny:** Målgrupp, Behandling, Aktuellt och Galleri, samt ”‹ Alla enheter”.
  Alla fyra länkar pekar på den aktuella enhetens sidor.
- Färger, typsnitt, bilder, rundningar och befintlig innehållsstil är bevarade.

### Sidträd

```
/                          enhetsväljare
/oss/                      gemensam organisationsinformation
/platsforfrogan/           båda enheternas platsförfrågan
/kontakt/                  båda enheternas kontaktuppgifter och kartor
/vinkelviken/              enhetens startsida
/vinkelviken/malgrupp/     Målgrupp
/vinkelviken/behandling/   Behandling
/vinkelviken/aktuellt/     Aktuellt
/vinkelviken/galleri/      Galleri
/kyrkhult/…                samma fyra sektioner
```

Sidträdet skapas vid aktivering. Vid uppdatering av ett redan aktivt tema skapas
saknade gemensamma sidor automatiskt, utan att befintligt sidinnehåll skrivs över.
De äldre länkarna `/vinkelviken/oss/`, `/kyrkhult/kontakt/` osv. omdirigeras till
den motsvarande gemensamma sidan.

### Lediga platser

Varje enhetskort på landningssidan visar antalet lediga platser (6 platser
totalt per enhet). Antalet redigeras av personalen i **wp-admin → Lediga
platser** — ett sifferfält per enhet, `0` visar "Fullbelagt just nu". Sparas som
alternativen `noav_lediga_vinkelviken` / `noav_lediga_kyrkhult`; totalsiffrorna
ändras i `noav_platser_totalt()` i `functions.php`.

## Uppbyggnad

- `header.php` — enhets-medveten: på landningen visas loggan och den gemensamma menyn; inne i en enhet
  byggs menyn dynamiskt och länkas till den enhetens sidor.
- `page.php` — väljer `parts/<enhet>/<sektion>.php` eller `parts/global/<sektion>.php` utifrån aktuell sida.
- `functions.php` — enhets-/sektionshjälpare + hierarkisk sidskapning.
- `parts/vinkelviken/*` — riktigt innehåll (från originalets renderade DOM).
- `parts/kyrkhult/*` — platshållarsidor som speglar Vinkelvikens layout.
- `parts/landing.php` — enhetsväljaren. `parts/_social.html`, `parts/_hamburger.html`,
  `parts/_footer.html` — delade delar.
- `assets/css/` — originalets två stilmallar + `noav-copy.css` (små justeringar).
  `assets/img/` — 35 lokala bildkopior. `assets/js/main.js` — mobilmeny + kartan.

## Kör lokalt (Docker)

```bash
cd wp
docker compose -f docker-compose.copy.yml up -d
./bin/setup-live.sh
```

Sajten: <http://localhost:8082> · wp-admin: <http://localhost:8082/wp-admin>
(`noav` / `noav`). Port 8082 — 8081 är Metros standardport och krockar med
React Native-utveckling.

## Kör i webbläsaren (WordPress Playground)

Blueprinten `wp/playground/blueprint-live.json` hämtar temat från `main` och
aktiverar det. Fungerar när temat är pushat till repot.

## Medvetna avvikelser från originalet

Tre saker som originalet löser med JavaScript vi inte skeppar hanteras här i
CSS/PHP istället — visuellt likvärdigt, men robustare i en statisk kopia:

1. **Hjältebildens diagonala "slant"-klippning** är borttagen (den fasta pixelbredden
   klippte annars innehåll på breda skärmar). Den tunna diagonala linjen är kvar.
2. **Normal sid-scroll** istället för originalets interna scroll-container.
3. **Kartan** på Kontakt ritas med Leaflet + OpenStreetMap istället för Mapbox
   (ingen API-nyckel), centrerad på respektive enhets adress.

Kontaktuppgifter för Vinkelviken är de som redan är publicerade på noav.se. För
Kyrkhult är endast adressen känd; resten är platshållare.
