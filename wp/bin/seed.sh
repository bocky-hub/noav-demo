#!/usr/bin/env bash
#
# Seedar den lokala WordPress-installationen med Noavs innehåll:
# bilder, de två enheterna, startsidan, menyn och kontaktuppgifterna.
#
#   ./bin/seed.sh
#
# Skriptet är idempotent — kör det igen och innehållet uppdateras istället
# för att dubbleras. Det gör det användbart både första gången och som ett
# sätt att återställa demodata efter att ha klickat sönder något.

set -euo pipefail

cd "$(dirname "$0")/.."

THEME_IMG="/var/www/html/wp-content/themes/noav/assets/img"

wp() { docker compose run --rm -T cli "$@" 2>/dev/null; }

# Skriver ut ID:t för en post med given slug, eller tom sträng.
post_id() {
  wp post list --post_type="$1" --name="$2" --post_status=any --field=ID --posts_per_page=1 | tr -d '\r'
}

# Importerar en bild om den inte redan finns, och skriver ut bilagans ID.
import_image() {
  local file="$1" title="$2" alt="$3" slug existing id
  slug="${file%.jpg}"

  existing="$(wp post list --post_type=attachment --name="$slug" --field=ID --posts_per_page=1 | tr -d '\r')"
  if [[ -n "$existing" ]]; then
    echo "$existing"
    return
  fi

  id="$(wp media import "$THEME_IMG/$file" --title="$title" --porcelain | tr -d '\r')"
  wp post meta update "$id" _wp_attachment_image_alt "$alt" >/dev/null
  echo "$id"
}

echo "→ Importerar bilder"
HERO_ID="$(import_image hero-skymning.jpg 'Skymning över landskapet' '')"
ENTRE_ID="$(import_image vinkelviken-entre.jpg 'Vinkelvikens entré' 'Entrén till Vinkelvikens HVB')"
HUSET_ID="$(import_image vinkelviken-huset.jpg 'Vinkelvikens hus' 'Vinkelvikens HVB — vitt tegelhus med trädgård och trästaket')"
import_image vinkelviken-bullar.jpg 'Kanelbullar i köket' 'Nybakade kanelbullar på en plåt i Vinkelvikens kök' >/dev/null
import_image vinkelviken-sovrum.jpg 'Ungdomsrum' 'Ett av ungdomarnas rum — säng, skrivbord och fönster mot grönskan' >/dev/null
import_image vinkelviken-kok.jpg 'Köket' 'Ljust kök med utgång mot trädgården' >/dev/null
import_image vinkelviken-allrum.jpg 'Allrummet' 'Gemensamt allrum med matbord och sittgrupp' >/dev/null
import_image vinkelviken-odling.jpg 'Odlingslådor' 'Odlingslådor i trädgården på Vinkelviken' >/dev/null

# ---------------------------------------------------------------------------
# Enheter
# ---------------------------------------------------------------------------

# upsert_unit <slug> <titel> <utdrag> <bild-id eller tom>
upsert_unit() {
  local slug="$1" title="$2" excerpt="$3" thumb="$4" order="$5" id
  id="$(post_id noav_unit "$slug")"

  if [[ -z "$id" ]]; then
    id="$(wp post create --post_type=noav_unit --post_status=publish \
      --post_title="$title" --post_name="$slug" --post_excerpt="$excerpt" \
      --menu_order="$order" --porcelain | tr -d '\r')"
    echo "   skapade $slug (ID $id)" >&2
  else
    wp post update "$id" --post_title="$title" --post_excerpt="$excerpt" \
      --menu_order="$order" --post_status=publish >/dev/null
    echo "   uppdaterade $slug (ID $id)" >&2
  fi

  if [[ -n "$thumb" ]]; then
    wp post meta update "$id" _thumbnail_id "$thumb" >/dev/null
  fi
  echo "$id"
}

echo "→ Skapar enheter"
VINKEL_ID="$(upsert_unit vinkelviken 'Vinkelvikens HVB' \
  'Ett hemlikt behandlingshem för ungdomar 13–17 år, med tydlig struktur, hög personaltäthet och en lugn miljö nära naturen.' \
  "$ENTRE_ID" 1)"

KYRK_ID="$(upsert_unit kyrkhult 'Kyrkhults HVB' \
  'Noav AB:s enhet i Blekinge — ett hemlikt behandlingshem för ungdomar 13–17 år, byggt på samma metodik och kvalitetsarbete som Vinkelviken.' \
  '' 2)"

set_meta() { wp post meta update "$1" "$2" "$3" >/dev/null; }

set_meta "$VINKEL_ID" _noav_total 7
set_meta "$VINKEL_ID" _noav_available 2
set_meta "$VINKEL_ID" _noav_available_updated '2026-07-07'
set_meta "$VINKEL_ID" _noav_location 'Hörby, Skåne'
set_meta "$VINKEL_ID" _noav_address ''
set_meta "$VINKEL_ID" _noav_phone ''
set_meta "$VINKEL_ID" _noav_email ''
set_meta "$VINKEL_ID" _noav_art_class 'art-vinkelviken'
set_meta "$VINKEL_ID" _noav_short 'Ett hemlikt HVB med sju platser, psykolog knuten till verksamheten och en aktiv vardag med skola, friluftsliv och ridning. Här bor ungdomarna i en lugn miljö med tydliga rutiner och vuxna som alltid finns nära.'

set_meta "$KYRK_ID" _noav_total 6
set_meta "$KYRK_ID" _noav_available 1
set_meta "$KYRK_ID" _noav_available_updated '2026-07-07'
set_meta "$KYRK_ID" _noav_location 'Olofströms kommun, Blekinge'
set_meta "$KYRK_ID" _noav_address ''
set_meta "$KYRK_ID" _noav_phone ''
set_meta "$KYRK_ID" _noav_email ''
set_meta "$KYRK_ID" _noav_art_class 'art-kyrkhult'
set_meta "$KYRK_ID" _noav_short 'Vår enhet i Blekinge tar emot ungdomar med samma målgrupp och metodik som Vinkelviken, i en naturnära miljö mitt i skogsbygden. Enhetssidan byggs ut i takt med att verksamheten etableras.'

# ---------------------------------------------------------------------------
# Startsida
# ---------------------------------------------------------------------------

echo "→ Skapar startsidan"
HOME_ID="$(post_id page start)"
if [[ -z "$HOME_ID" ]]; then
  HOME_ID="$(wp post create --post_type=page --post_status=publish \
    --post_title='Start' --post_name=start --porcelain | tr -d '\r')"
fi

# Mönstret expanderas till riktiga block i post_content istället för att
# sparas som en referens (<!-- wp:pattern ... -->). Skillnaden syns i
# redigeraren: expanderade block går att klicka i och skriva om, en referens
# gör det inte — och hela poängen var att texten skulle vara redigerbar.
set_content_from_pattern() {
  local id="$1" slug="$2"
  wp eval "
    \$p = WP_Block_Patterns_Registry::get_instance()->get_registered( '$slug' );
    if ( ! \$p ) { WP_CLI::error( 'Blockmönstret $slug är inte registrerat.' ); }
    wp_update_post( array( 'ID' => $id, 'post_content' => \$p['content'] ) );
  " >/dev/null
}

set_content_from_pattern "$HOME_ID" 'noav/startsida'
wp post meta update "$HOME_ID" _thumbnail_id "$HERO_ID" >/dev/null

wp option update show_on_front page >/dev/null
wp option update page_on_front "$HOME_ID" >/dev/null

echo "→ Fyller enhetssidorna"
set_content_from_pattern "$VINKEL_ID" 'noav/enhet-vinkelviken'
set_content_from_pattern "$KYRK_ID" 'noav/enhet-kyrkhult'

# ---------------------------------------------------------------------------
# Meny
# ---------------------------------------------------------------------------

echo "→ Bygger huvudmenyn"
if ! wp menu list --field=slug | tr -d '\r' | grep -qx huvudmeny; then
  wp menu create 'Huvudmeny' >/dev/null
  wp menu item add-custom huvudmeny 'Hem' '/' >/dev/null
  wp menu item add-post huvudmeny "$VINKEL_ID" --title='Vinkelviken' >/dev/null
  wp menu item add-post huvudmeny "$KYRK_ID" --title='Kyrkhult' >/dev/null
  wp menu item add-custom huvudmeny 'Behandling' '/#behandling' >/dev/null
  wp menu item add-custom huvudmeny 'Målgrupp' '/#malgrupp' >/dev/null
  wp menu item add-custom huvudmeny 'Kontakt' '/#kontakt' >/dev/null
fi
wp menu location assign huvudmeny primary >/dev/null 2>&1 || true

# ---------------------------------------------------------------------------
# Kontaktuppgifter
# ---------------------------------------------------------------------------

echo "→ Sparar kontaktuppgifter"
wp eval '
update_option( "noav_settings", array(
  "org_number"      => "559452-7045",
  "tagline"         => "HVB-hem för ungdomar 13–17 år, med lågaffektivt bemötande och evidensbaserade metoder.",
  "main_phone"      => "",
  "general_email"   => "",
  "copyright"       => "Alla rättigheter förbehållna.",
  "compliance"      => "Kvalitetsledningssystem enligt SOSFS 2011:9 · Kollektivavtal via Vårdföretagarna",
  "show_demo_badge" => true,
  "instagram"       => "",
  "instagram_label" => "Instagram — @noavab",
  "facebook"        => "",
  "linkedin"        => "",
  "contacts"        => array(
    array( "role" => "Verksamhetschef", "name" => "[Namn kompletteras]", "phone" => "", "email" => "" ),
    array( "role" => "Vinkelvikens HVB — Hörby", "name" => "[Nummer kompletteras]", "phone" => "", "email" => "" ),
    array( "role" => "Kyrkhults HVB — Olofström", "name" => "[Nummer kompletteras]", "phone" => "", "email" => "" ),
  ),
) );
' >/dev/null

wp option update blogdescription 'HVB-hem för ungdomar 13–17 år i Hörby och Olofström' >/dev/null
wp rewrite structure '/%postname%/' >/dev/null
wp rewrite flush >/dev/null

echo
echo "Klart. Sajten: http://localhost:8080"
echo "wp-admin:     http://localhost:8080/wp-admin  (noav / noav)"
