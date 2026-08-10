/* ============================================================
   NOAV — redigerarsida för de serverrenderade platsblocken.

   Medvetet skriven i vanlig JavaScript utan JSX, så att temat kan
   installeras och ändras utan npm-byggsteg. Förhandsvisningen kommer
   från ServerSideRender, som anropar samma PHP-funktion som besökaren
   får — det som syns i redigeraren är alltså det som publiceras.
   ============================================================ */

(function (wp) {
  "use strict";

  var el = wp.element.createElement;
  var __ = wp.i18n.__;
  var ServerSideRender = wp.serverSideRender;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;

  /**
   * Bygger en edit-funktion som visar serverrenderad förhandsvisning,
   * med valfri inställningspanel i sidokolumnen.
   *
   * @param {string}   name    Blockets namn.
   * @param {Function} [panel] Returnerar innehållet till inställningspanelen.
   */
  function makeEdit(name, panel) {
    return function (props) {
      var preview = el(ServerSideRender, {
        block: name,
        attributes: props.attributes,
        // Utan detta visas "Läser in…" som ett blockcitat mitt i layouten.
        LoadingResponsePlaceholder: function () {
          return el("p", { style: { opacity: 0.6 } }, __("Läser in platsdata…", "noav"));
        }
      });

      var children = [preview];

      if (panel) {
        children.unshift(
          el(
            InspectorControls,
            { key: "inspector" },
            el(PanelBody, { title: __("Inställningar", "noav") }, panel(props))
          )
        );
      }

      return el("div", useBlockProps(), children);
    };
  }

  /** Dynamiska block sparar ingen markup — allt renderas av PHP. */
  function save() {
    return null;
  }

  wp.blocks.registerBlockType("noav/availability-grid", {
    edit: makeEdit("noav/availability-grid"),
    save: save
  });

  wp.blocks.registerBlockType("noav/unit-cards", {
    edit: makeEdit("noav/unit-cards"),
    save: save
  });

  wp.blocks.registerBlockType("noav/contact-list", {
    edit: makeEdit("noav/contact-list"),
    save: save
  });

  wp.blocks.registerBlockType("noav/availability-pill", {
    edit: makeEdit("noav/availability-pill", function (props) {
      return el(TextControl, {
        label: __("Text efter siffran", "noav"),
        value: props.attributes.suffix || "",
        onChange: function (value) {
          props.setAttributes({ suffix: value });
        },
        help: __('Till exempel "just nu — båda enheterna".', "noav")
      });
    }),
    save: save
  });

  /** Panelen för de block som kan pekas mot en särskild enhet. */
  function unitPanel(props) {
    return el(TextControl, {
      label: __("Enhetens slug", "noav"),
      value: props.attributes.unit || "",
      onChange: function (value) {
        props.setAttributes({ unit: value.trim() });
      },
      help: __(
        "Lämna tomt när blocket står på en enhetssida — då används den enhet sidan visar.",
        "noav"
      )
    });
  }

  wp.blocks.registerBlockType("noav/unit-availability", {
    edit: makeEdit("noav/unit-availability", unitPanel),
    save: save
  });

  wp.blocks.registerBlockType("noav/unit-contact", {
    edit: makeEdit("noav/unit-contact", unitPanel),
    save: save
  });
})(window.wp);
