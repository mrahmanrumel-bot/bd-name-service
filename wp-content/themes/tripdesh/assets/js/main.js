(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var toggle = document.getElementById('tripdesh-nav-toggle');
		var nav = document.getElementById('tripdesh-nav');
		if (!toggle || !nav) {
			return;
		}

		toggle.addEventListener('click', function () {
			var expanded = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', String(!expanded));
			nav.classList.toggle('is-open');
		});
	});
})();
