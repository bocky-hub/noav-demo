(function () {
	'use strict';
	document.querySelectorAll('[data-noav-image-setting]').forEach(function (container) {
		var input = container.querySelector('input');
		var preview = container.querySelector('img');
		var fallback = container.getAttribute('data-default-preview') || preview.src;
		container.querySelector('[data-noav-select-image]').addEventListener('click', function () {
			var frame = wp.media({title: 'Välj bild', library: {type: 'image'}, button: {text: 'Använd bilden'}, multiple: false});
			frame.on('select', function () {
				var image = frame.state().get('selection').first().toJSON();
				input.value = image.id;
				preview.src = image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url;
			});
			frame.open();
		});
		container.querySelector('[data-noav-clear-image]').addEventListener('click', function () {
			input.value = '0';
			preview.src = fallback;
		});
	});
	document.querySelectorAll('[data-noav-menu]').forEach(function (menu) {
		var list = menu.querySelector('ol');
		menu.addEventListener('click', function (event) {
			var button = event.target.closest('button');
			if (!button) return;
			var row = button.closest('li');
			if (button.hasAttribute('data-menu-up') && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
			if (button.hasAttribute('data-menu-down') && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
			if (button.hasAttribute('data-menu-remove')) row.remove();
			if (button.hasAttribute('data-menu-add')) {
				var select = menu.querySelector('select');
				if (!select || select.value === '0' || !select.value) return;
				if (Array.from(list.querySelectorAll('input')).some(function (input) { return input.value === select.value; })) return;
				row = document.createElement('li');
				row.style.marginBottom = '8px';
				var input = document.createElement('input');
				input.type = 'hidden'; input.name = menu.getAttribute('data-menu-name') + '[]'; input.value = select.value;
				row.appendChild(input);
				var label = document.createElement('span'); label.textContent = select.options[select.selectedIndex].text.trim(); row.appendChild(label);
				[['data-menu-up', '↑', 'Flytta upp'], ['data-menu-down', '↓', 'Flytta ned'], ['data-menu-remove', 'Ta bort ur menyn', 'Ta bort ur menyn']].forEach(function (action) {
					row.appendChild(document.createTextNode(' '));
					var control = document.createElement('button'); control.type = 'button'; control.className = 'button button-small'; control.setAttribute(action[0], ''); control.textContent = action[1]; control.setAttribute('aria-label', action[2]); row.appendChild(control);
				});
				list.appendChild(row);
				select.value = '0';
			}
		});
	});
})();
