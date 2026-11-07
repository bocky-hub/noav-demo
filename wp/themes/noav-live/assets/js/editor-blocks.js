(function (wp) {
	'use strict';
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var controls = wp.components;
	var editor = wp.blockEditor;
	var data = window.noavEditor || {units: {}, availabilityUrl: ''};

	wp.blocks.registerBlockType('noav/map', {
		apiVersion: 3,
		title: 'Noav – karta',
		description: 'En karta med redigerbar platsmarkör.',
		icon: 'location-alt',
		category: 'widgets',
		attributes: {
			latitude: {type: 'number', default: 55.8575946},
			longitude: {type: 'number', default: 13.6611013},
			label: {type: 'string', default: 'Noav'},
			zoom: {type: 'number', default: 16}
		},
		supports: {html: false},
		edit: function (props) {
			var attrs = props.attributes;
			var settings = el(controls.PanelBody, {title: 'Kartans plats', initialOpen: true},
				el(controls.TextControl, {label: 'Platsens namn', value: attrs.label, onChange: function (value) { props.setAttributes({label: value}); }}),
				el(controls.TextControl, {label: 'Latitud', help: 'Till exempel 55.8575946. Använd punkt som decimaltecken.', type: 'number', step: 'any', min: -90, max: 90, value: attrs.latitude, onChange: function (value) { var n = Number(value); if (value !== '' && Number.isFinite(n)) props.setAttributes({latitude: Math.max(-90, Math.min(90, n))}); }}),
				el(controls.TextControl, {label: 'Longitud', type: 'number', step: 'any', min: -180, max: 180, value: attrs.longitude, onChange: function (value) { var n = Number(value); if (value !== '' && Number.isFinite(n)) props.setAttributes({longitude: Math.max(-180, Math.min(180, n))}); }}),
				el(controls.RangeControl, {label: 'Inzoomning', min: 1, max: 19, value: attrs.zoom, onChange: function (value) { props.setAttributes({zoom: value || 16}); }})
			);
			return el(Fragment, null,
				el(editor.InspectorControls, null, settings),
				el('div', editor.useBlockProps({style: {padding: '28px', border: '1px solid #d6d0c5', borderRadius: '16px', background: '#faf7f0'}}),
					el('p', {style: {fontWeight: 700, marginTop: 0}}, '📍 ' + attrs.label),
					el('p', null, 'Karta: ' + attrs.latitude + ', ' + attrs.longitude),
					el('p', null, 'Ändra platsens namn och koordinater i blockets inställningar. Den interaktiva kartan visas på hemsidan.'),
					el('a', {href: 'https://www.openstreetmap.org/?mlat=' + attrs.latitude + '&mlon=' + attrs.longitude + '#map=' + attrs.zoom + '/' + attrs.latitude + '/' + attrs.longitude, target: '_blank', rel: 'noopener noreferrer'}, 'Kontrollera platsen på kartan')
				)
			);
		},
		save: function () { return null; }
	});

	wp.blocks.registerBlockType('noav/availability', {
		apiVersion: 3,
		title: 'Noav – lediga platser',
		description: 'Visar det aktuella platsantalet från Lediga platser.',
		icon: 'groups',
		category: 'widgets',
		attributes: {unit: {type: 'string', enum: ['vinkelviken', 'kyrkhult'], default: 'vinkelviken'}},
		supports: {html: false},
		edit: function (props) {
			var unit = props.attributes.unit;
			var info = data.units[unit] || {name: unit, available: 0, total: 6};
			var text = info.available > 0 ? info.available + (info.available === 1 ? ' ledig plats av ' : ' lediga platser av ') + info.total : 'Fullbelagt just nu';
			return el('div', editor.useBlockProps({style: {padding: '16px', border: '1px solid #d6d0c5', borderRadius: '12px', textAlign: 'center'}}),
				el(controls.SelectControl, {label: 'Enhet för platsantalet', value: unit, options: [{label: 'Vinkelviken', value: 'vinkelviken'}, {label: 'Söderbacken', value: 'kyrkhult'}], onChange: function (value) { props.setAttributes({unit: value}); }}),
				el('p', {style: {fontWeight: 600}}, info.name + ': ' + text),
				el('a', {href: data.availabilityUrl, target: '_blank', rel: 'noopener noreferrer'}, 'Ändra antal lediga platser'),
				el('p', {style: {fontSize: '12px'}}, 'Platsantalet uppdateras automatiskt på hemsidan. Ladda om redigeraren efter en ändring för aktuell förhandsvisning.')
			);
		},
		save: function () { return null; }
	});
})(window.wp);
