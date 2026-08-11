/**
 * Noav – Vinkelvikens HVB (kopia)
 * Minimal behaviour the static copy needs: a mobile menu toggle and the
 * Kontakt map. Everything else is pure CSS from the original stylesheets.
 */
(function () {
	'use strict';

	/* ---- Mobile menu -------------------------------------------------- */
	var header = document.getElementById('website-header');
	if (header) {
		var btn = header.querySelector('.lg\\:hidden button');
		var navLinks = Array.prototype.slice
			.call(header.querySelectorAll('ul a'))
			.filter(function (a) { return a.textContent.trim().length > 0; });

		if (btn && navLinks.length) {
			var panel = document.createElement('div');
			panel.className = 'noav-mobile-menu lg:hidden';
			panel.style.cssText = 'display:none;background:#ffffff;border-top:1px solid rgba(17,24,39,.08);';

			var list = document.createElement('ul');
			list.style.cssText = 'list-style:none;margin:0;padding:8px 24px 20px;display:flex;flex-direction:column;';

			navLinks.forEach(function (a) {
				var li = document.createElement('li');
				var link = a.cloneNode(true);
				link.className = 'block body-normal whitespace-nowrap';
				link.style.cssText = 'display:block;padding:12px 0;color:#111827;border-bottom:1px solid rgba(17,24,39,.05);';
				li.appendChild(link);
				list.appendChild(li);
			});

			panel.appendChild(list);
			header.appendChild(panel);

			btn.setAttribute('aria-expanded', 'false');
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var open = panel.style.display === 'block';
				panel.style.display = open ? 'none' : 'block';
				btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			});
		}
	}

	/* ---- Kontakt map (Leaflet + OpenStreetMap) ------------------------ */
	var mapEl = document.getElementById('noav-leaflet');
	if (mapEl && window.L) {
		var lat = parseFloat(mapEl.getAttribute('data-lat')) || 55.8575946;
		var lng = parseFloat(mapEl.getAttribute('data-lng')) || 13.6611013;
		var zoom = parseInt(mapEl.getAttribute('data-zoom'), 10) || 16;

		var map = L.map(mapEl, { scrollWheelZoom: false }).setView([lat, lng], zoom);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; OpenStreetMap-bidragsgivare'
		}).addTo(map);
		L.marker([lat, lng]).addTo(map).bindPopup('Vinkelvikens HVB<br>Vinkelgatan 11, Hörby');
		setTimeout(function () { map.invalidateSize(); }, 200);
	}
})();
