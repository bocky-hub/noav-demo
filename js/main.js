/* ============================================================
   NOAV AB — demo-webbplats
   Navigering, rendering av lediga platser samt animationer
   (GSAP + ScrollTrigger + Lenis).
   ============================================================ */

(function () {
  "use strict";

  var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ============================================================
     1. DATALAGER — LEDIGA PLATSER
     ============================================================
     getAvailability() är den enda punkt där sidorna hämtar sin
     tillgänglighetsdata. I dag läser den window.NOAV_AVAILABILITY
     (js/availability.js) och applicerar ev. URL-överstyrning.

     FRAMTIDA ADMINPANEL/BACKEND: byt ut innehållet i denna funktion
     mot ett API-anrop (t.ex. fetch("/api/availability")) som
     returnerar samma struktur — resten av koden behöver inte ändras.
     ============================================================ */
  function getAvailability() {
    var source = window.NOAV_AVAILABILITY || { updatedAt: "", units: {} };

    // Djupkopia så att överstyrningar aldrig muterar källobjektet.
    var data = {
      updatedAt: source.updatedAt,
      units: {}
    };
    Object.keys(source.units).forEach(function (id) {
      var u = source.units[id];
      data.units[id] = { name: u.name, total: u.total, available: u.available };
    });

    // DEMO-ÖVERSTYRNING via URL-parametrar, t.ex. ?vinkelviken=3&kyrkhult=0
    // Gör att siffrorna kan ändras live under en pitch utan kodändring.
    try {
      var params = new URLSearchParams(window.location.search);
      Object.keys(data.units).forEach(function (id) {
        if (!params.has(id)) return;
        var value = parseInt(params.get(id), 10);
        if (!isNaN(value) && value >= 0) {
          data.units[id].available = Math.min(value, data.units[id].total);
        }
      });
    } catch (e) {
      /* Ingen URLSearchParams-support — använd konfigvärdena rakt av. */
    }

    return data;
  }

  /* ---------- Statuslogik ---------- */
  function getStatus(available) {
    if (available >= 3) return { key: "ok", label: "God tillgänglighet" };
    if (available >= 1) return { key: "warn", label: "Begränsat antal platser" };
    return { key: "none", label: "Inga lediga platser just nu" };
  }

  function getUnitView(data, id) {
    // "all" = summerad tillgänglighet över samtliga enheter (hero-pillen).
    if (id === "all") {
      var sumAvail = 0;
      var sumTotal = 0;
      Object.keys(data.units).forEach(function (key) {
        sumAvail += data.units[key].available;
        sumTotal += data.units[key].total;
      });
      return { name: "Alla enheter", available: sumAvail, total: sumTotal };
    }
    return data.units[id] || null;
  }

  /* ---------- Rendering ----------
     Markup deklarerar sitt behov med attribut:
       data-avail="vinkelviken|kyrkhult|all" + data-avail-field="..."
       data-avail-dot="vinkelviken|kyrkhult|all" (statusprick)
     Renderaren fyller allt vid sidladdning. */
  function renderAvailability() {
    var data = getAvailability();

    document.querySelectorAll("[data-avail]").forEach(function (el) {
      var unit = getUnitView(data, el.getAttribute("data-avail"));
      if (!unit) return;

      var field = el.getAttribute("data-avail-field");
      var status = getStatus(unit.available);

      switch (field) {
        case "available":
          el.textContent = String(unit.available);
          if (el.hasAttribute("data-count")) {
            el.setAttribute("data-count-target", String(unit.available));
          }
          break;
        case "total":
          el.textContent = String(unit.total);
          break;
        case "status":
          el.textContent = status.label;
          el.setAttribute("data-status", status.key);
          break;
        case "platser": // böjning: "1 ledig plats" / "2 lediga platser"
          el.textContent = unit.available === 1 ? "ledig plats" : "lediga platser";
          break;
        case "updatedAt":
          el.textContent = data.updatedAt;
          break;
      }
    });

    document.querySelectorAll("[data-avail-dot]").forEach(function (dot) {
      var unit = getUnitView(data, dot.getAttribute("data-avail-dot"));
      if (!unit) return;

      var status = getStatus(unit.available);
      dot.setAttribute("data-status", status.key);
      dot.setAttribute("role", "img");
      dot.setAttribute("aria-label", "Status: " + status.label);
      dot.classList.toggle("is-pulsing", unit.available > 0 && !prefersReducedMotion);
    });
  }

  /* ============================================================
     2. NAVIGERING
     ============================================================ */
  function initHeader() {
    var header = document.querySelector(".site-header");
    if (!header) return;

    function onScroll() {
      header.classList.toggle("is-scrolled", window.scrollY > 40);
    }
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  function initMobileMenu(lenis) {
    var toggle = document.querySelector(".nav-toggle");
    var menu = document.getElementById("mobileMenu");
    if (!toggle || !menu) return;

    var items = menu.querySelectorAll(".mobile-menu-item");

    function setOpen(open) {
      document.body.classList.toggle("menu-open", open);
      toggle.setAttribute("aria-expanded", String(open));
      toggle.setAttribute("aria-label", open ? "Stäng meny" : "Öppna meny");

      if (lenis) {
        open ? lenis.stop() : lenis.start();
      }
      document.documentElement.style.overflow = open ? "hidden" : "";

      if (open && window.gsap && !prefersReducedMotion) {
        window.gsap.fromTo(
          items,
          { y: 28, autoAlpha: 0 },
          { y: 0, autoAlpha: 1, duration: 0.6, stagger: 0.06, ease: "power3.out", delay: 0.1 }
        );
      }
    }

    toggle.addEventListener("click", function () {
      setOpen(!document.body.classList.contains("menu-open"));
    });

    menu.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        setOpen(false);
      });
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && document.body.classList.contains("menu-open")) {
        setOpen(false);
        toggle.focus();
      }
    });
  }

  function initAnchorScroll(lenis) {
    if (!lenis) return; // utan Lenis sköter webbläsaren ankarlänkar själv

    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
      link.addEventListener("click", function (e) {
        var id = link.getAttribute("href");
        if (id.length < 2) return;
        var target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        lenis.scrollTo(target, { offset: -90 });
        history.pushState(null, "", id);
      });
    });
  }

  /* ============================================================
     3. RÖRELSE — Lenis + GSAP + ScrollTrigger
     ============================================================ */
  function initLenis() {
    if (prefersReducedMotion || !window.Lenis || !window.gsap) return null;

    var lenis = new window.Lenis({ lerp: 0.1, smoothWheel: true });

    lenis.on("scroll", window.ScrollTrigger.update);
    window.gsap.ticker.add(function (time) {
      lenis.raf(time * 1000);
    });
    window.gsap.ticker.lagSmoothing(0);

    return lenis;
  }

  function initCounters() {
    var counters = document.querySelectorAll("[data-count]");

    counters.forEach(function (el) {
      var target = parseInt(el.getAttribute("data-count-target") || el.textContent, 10) || 0;

      // Reducerad rörelse / saknad GSAP: slutvärdet är redan renderat.
      if (prefersReducedMotion || !window.gsap || !window.ScrollTrigger) {
        el.textContent = String(target);
        return;
      }

      var proxy = { value: 0 };
      window.gsap.to(proxy, {
        value: target,
        duration: 1.2,
        ease: "power2.out",
        snap: { value: 1 },
        scrollTrigger: { trigger: el, start: "top 75%", once: true },
        onUpdate: function () {
          el.textContent = String(Math.round(proxy.value));
        },
        onComplete: function () {
          el.textContent = String(target);
        }
      });
    });
  }

  function initHero(gsap) {
    var hero = document.querySelector(".hero");
    if (!hero) return;

    var tl = gsap.timeline({ defaults: { ease: "power3.out" } });
    var bg = hero.querySelector(".hero-bg");
    var eyebrow = hero.querySelector(".hero-eyebrow");
    var lines = hero.querySelectorAll(".hero-title .line-inner");
    var sub = hero.querySelector(".hero-sub");
    var ctas = hero.querySelectorAll(".hero-ctas > *");
    var pill = hero.querySelector(".hero-pill, .unit-avail");

    if (bg) {
      tl.fromTo(bg, { scale: 1.06 }, { scale: 1, duration: 1.2, ease: "power2.out" }, 0);
    }
    if (eyebrow) {
      tl.fromTo(eyebrow, { y: 22, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.6 }, 0.15);
    }
    if (lines.length) {
      tl.fromTo(
        lines,
        { yPercent: 112 },
        { yPercent: 0, duration: 0.9, stagger: 0.08 },
        0.3
      );
    }
    if (sub) {
      tl.fromTo(sub, { y: 26, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.7 }, 0.62);
    }
    if (ctas.length) {
      tl.fromTo(
        ctas,
        { y: 24, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: 0.6, stagger: 0.08 },
        0.76
      );
    }
    if (pill) {
      tl.fromTo(pill, { y: 24, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.7 }, 0.9);
    }

    // Långsam parallax på herons bakgrundslager.
    if (bg) {
      gsap.to(bg, {
        yPercent: -8,
        ease: "none",
        scrollTrigger: {
          trigger: hero,
          start: "top top",
          end: "bottom top",
          scrub: true
        }
      });
    }
  }

  function initReveals(gsap) {
    document.querySelectorAll("[data-reveal]").forEach(function (el) {
      gsap.fromTo(
        el,
        { y: 32, autoAlpha: 0 },
        {
          y: 0,
          autoAlpha: 1,
          duration: 0.8,
          ease: "power3.out",
          scrollTrigger: { trigger: el, start: "top 80%", once: true }
        }
      );
    });

    document.querySelectorAll("[data-reveal-group]").forEach(function (group) {
      var children = Array.prototype.slice.call(group.children);
      if (!children.length) return;
      gsap.fromTo(
        children,
        { y: 32, autoAlpha: 0 },
        {
          y: 0,
          autoAlpha: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: "power3.out",
          scrollTrigger: { trigger: group, start: "top 80%", once: true }
        }
      );
    });

    // Metod-chips: mjuk skala + tona in.
    document.querySelectorAll("[data-chips]").forEach(function (group) {
      var chips = Array.prototype.slice.call(group.children);
      if (!chips.length) return;
      gsap.fromTo(
        chips,
        { scale: 0.94, autoAlpha: 0 },
        {
          scale: 1,
          autoAlpha: 1,
          duration: 0.6,
          stagger: 0.06,
          ease: "power2.out",
          scrollTrigger: { trigger: group, start: "top 80%", once: true }
        }
      );
    });
  }

  function initMotion(lenis) {
    var gsap = window.gsap;

    // Reducerad rörelse eller CDN otillgängligt: allt innehåll är
    // synligt via CSS-grundläget och siffrorna är redan renderade.
    if (prefersReducedMotion || !gsap || !window.ScrollTrigger) {
      return;
    }

    gsap.registerPlugin(window.ScrollTrigger);

    initHero(gsap);
    initReveals(gsap);
    initCounters();
    initAnchorScroll(lenis);
  }

  /* ============================================================
     4. START
     ============================================================ */
  document.addEventListener("DOMContentLoaded", function () {
    renderAvailability();
    initHeader();

    var lenis = initLenis();
    initMobileMenu(lenis);
    initMotion(lenis);
  });
})();
