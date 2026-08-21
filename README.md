# Noav — webbplats i WordPress

En **trogen kopia av [noav.se](https://www.noav.se)** (Vinkelvikens HVB), byggd som
ett WordPress-tema och uppdelad i enheter under en gemensam ingång. Samma design,
CSS, typsnitt, bilder och innehåll som originalet — men nu redigerbart i WordPress.

Temat ligger i [`wp/themes/noav-live`](wp/themes/noav-live/README.md).

## Så fungerar sajten

- **Landning (`/`)** — ingen meny, bara Noavs logga + två enhetskort. Varje kort
  visar antal **lediga platser** (av 6), redigerbart i wp-admin → Lediga platser.
- **Klick på ett kort → in i enheten**, då dyker menyn upp (scopad till enheten).
- **`/vinkelviken/…`** — hela kopian av noav.se.
- **`/kyrkhult/…`** — platshållarenhet (framtida). Känt: adress Vilshultsvägen 15;
  övrigt är tydligt märkt "[Kompletteras]".

## Titta på den (dela en länk)

WordPress körs direkt i webbläsaren via **WordPress Playground** — mottagaren
behöver bara klicka, inget installeras:

<https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/bocky-hub/noav-demo/main/wp/playground/blueprint-live.json>

(Egen sandlåda per person, ingen inloggning, ändringar sparas inte.)

## Köra lokalt (för att redigera)

Kräver Docker.

```bash
cd wp
docker compose -f docker-compose.copy.yml up -d
./bin/setup-live.sh
```

Sajten: <http://localhost:8082> · wp-admin: <http://localhost:8082/wp-admin>
(`noav` / `noav`). Temat skapar hela sidträdet automatiskt vid aktivering.

Mer detaljer i [temats README](wp/themes/noav-live/README.md) — inklusive de få
medvetna avvikelserna från originalet.
