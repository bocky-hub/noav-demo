# Noav – redigerbar WordPress-webbplats

## Redigering för personalen

Sidorna använder WordPress vanliga blockredigerare. Alla 13 ordinarie sidor innehåller redigerbara rubriker, stycken, länkar, bilder, knappar, kolumner och gallerier. Aktuellt är en redigerbar sida där nyhetsgrupper kan läggas till, dupliceras, flyttas och tas bort. Bilderna finns i det vanliga mediebiblioteket.

Öppna **Noav – redigera** i administrationen för genvägar och instruktioner. **Logotyp och inställningar** hanterar webbplatsnamn, beskrivning, logotyp, ikon, sociala länkar, menyer och totalt platsantal. **Lediga platser** hanterar enheternas aktuella siffror. Rollen **Redaktör** räcker för det dagliga arbetet, inklusive Noav-inställningarna. Administratör behövs för drift, installationer och användarkonton.

Se även `wp/REDAKTORSGUIDE.md`.

## Innehåll och design

- `page.php` och `front-page.php` visar `the_content()`; mallfiler ersätter inte längre redigerat sidinnehåll.
- `theme.json` och `assets/css/editable-content.css` ger blocken Noavs färger, typsnitt och responsiva layout.
- `inc/editor-tools.php` tillhandahåller personalens översikt och inställningar, sidbaserade menyer samt blocken `noav/availability` och `noav/map`.
- `assets/js/editor-blocks.js` registrerar de två funktionella blockens redigeringskontroller utan byggsteg.
- Sidversioner, utkast, förhandsgranskning, papperskorg och mediehantering använder WordPress standardfunktioner.

Sidornas ursprungliga adresser behålls. Söderbacken använder fortsatt den befintliga sluggen `/kyrkhult/`. De gemensamma sidorna är `/oss/`, `/kontakt/`, `/platsforfrogan/` och `/aktuellt/`. Enheternas undersidor är Målgrupp, Behandling och Galleri.

## Första installation och uppgradering

Aktivering skapar sidträdet. `inc/content-migration.php` importerar sedan det godkända innehållet från `inc/seed-content.json` och kopierar bilder till WordPress uppladdningskatalog och mediebibliotek. Importen fyller endast tomma ursprungssidor eller den gamla platshållaren `<!-- Renderas av temat (parts/…). -->`. Befintligt författat innehåll lämnas kvar.

Varje migrerad sida markeras med `_noav_editable_migrated`. Den markeringen skyddar även en sida som personalen senare tömmer avsiktligt. Uppdatering eller återaktivering av temat återställer inte redigerat innehåll. Originalinnehållet före migrering sparas i `_noav_before_editable_content`. Bilder identifieras via `_noav_bundled_filename` så att en avbruten import kan återupptas utan dubbletter. Fel visas i administrationen.

`parts/` är kvar som historiskt underlag och för reproducering av startinnehållet. Det används inte för att rendera sidornas publicerade innehåll. Utvecklingsverktyget `python3 wp/tools/generate-editable-content.py` bygger seedfilen; det körs aldrig av WordPress och ändrar inte redaktörernas sparade sidor.

## Förhandsvisning och permanent drift

Playground-demot installerar temat från GitHub via `wp/playground/blueprint-live.json`. Varje Playground har sin egen databas. Personalens teständringar där påverkar inte noav.se eller andra personers Playground. Befintliga sparade Playground-installationer behöver uppdatera temat för att få nya funktioner.

För permanent drift installeras temat på ett WordPress-webbhotell. Säkerhetskopiera databas och uppladdningar, använd personliga konton och konfigurera HTTPS innan domänen kopplas om. Temats ZIP är ett installationspaket; redaktörernas framtida innehåll finns i databasen och mediebiblioteket och måste följa med vid en flytt.
