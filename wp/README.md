# Noav — WordPress

Den här mappen innehåller WordPress-versionen av noav.se: temat
[`themes/noav-live`](themes/noav-live/README.md) — en trogen kopia av den publika
sajten, uppdelad i enheterna **Vinkelviken** (riktigt innehåll) och **Kyrkhult**
(platshållare) under en gemensam landningssida.

## Titta på den (dela en länk)

WordPress Playground kör allt i webbläsaren — bara klicka:

<https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/bocky-hub/noav-demo/main/wp/playground/blueprint-live.json>

Blueprinten: [`playground/blueprint-live.json`](playground/blueprint-live.json).
Den hämtar temat från `main` i det här repot, så en push slår igenom i länken.

## Köra lokalt (Docker)

```bash
docker compose -f docker-compose.copy.yml up -d && ./bin/setup-live.sh
```

Sajten: <http://localhost:8082> · wp-admin: <http://localhost:8082/wp-admin>
(`noav` / `noav`). `setup-live.sh` installerar WordPress och aktiverar temat, som
i sin tur skapar landningssidan + de två enheterna × sju sidor.

Allt om temat, sidträdet och de medvetna avvikelserna finns i
[`themes/noav-live/README.md`](themes/noav-live/README.md).
