(function () {
	'use strict';

	if (typeof tripdeshData === 'undefined') {
		return;
	}

	var state = { lang: tripdeshData.lang || 'en', history: [] };

	function initChat() {
		var form = document.getElementById('tripdesh-ai-chat-form');
		var messages = document.getElementById('tripdesh-ai-chat-messages');
		var widget = document.getElementById('tripdesh-ai-chat');
		if (!form || !messages || !widget) {
			return;
		}

		var langButtons = widget.querySelectorAll('.tripdesh-ai-chat__lang button');
		langButtons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				langButtons.forEach(function (b) { b.classList.remove('active'); });
				btn.classList.add('active');
				state.lang = btn.getAttribute('data-lang');
			});
		});

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var input = form.querySelector('input[name="message"]');
			var text = input.value.trim();
			if (!text) {
				return;
			}

			appendMessage(messages, text, 'user');
			state.history.push({ role: 'user', content: text });
			input.value = '';

			var thinking = appendMessage(messages, tripdeshData.i18n.thinking, 'bot');

			fetch(tripdeshData.restUrl + '/concierge', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': tripdeshData.nonce },
				body: JSON.stringify({ message: text, language: state.lang, history: state.history })
			})
				.then(function (res) { return res.json(); })
				.then(function (data) {
					var reply = (data && data.reply) ? data.reply : tripdeshData.i18n.error;
					thinking.textContent = reply;
					state.history.push({ role: 'assistant', content: reply });
				})
				.catch(function () {
					thinking.textContent = tripdeshData.i18n.error;
				});
		});
	}

	function appendMessage(container, text, role) {
		var el = document.createElement('div');
		el.className = 'tripdesh-ai-chat__message tripdesh-ai-chat__message--' + (role === 'user' ? 'user' : 'bot');
		el.textContent = text;
		container.appendChild(el);
		container.scrollTop = container.scrollHeight;
		return el;
	}

	function initBookingForm() {
		var form = document.getElementById('tripdesh-booking-form');
		if (!form) {
			return;
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var resultEl = document.getElementById('tripdesh-booking-result');
			var submitBtn = form.querySelector('button[type="submit"]');
			submitBtn.disabled = true;

			var payload = {
				product_id: form.getAttribute('data-product-id'),
				name: form.name.value,
				phone: form.phone.value,
				email: form.email.value,
				travel_date: form.travel_date.value,
				travelers: form.travelers.value,
				notes: form.notes.value
			};

			fetch(tripdeshData.restUrl + '/booking', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': tripdeshData.nonce },
				body: JSON.stringify(payload)
			})
				.then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
				.then(function (result) {
					resultEl.hidden = false;
					submitBtn.disabled = false;
					if (result.ok) {
						resultEl.className = 'tripdesh-booking-form__result tripdesh-booking-form__result--success';
						resultEl.textContent = result.data.message + ' (' + result.data.reference + ')';
						form.reset();
					} else {
						resultEl.className = 'tripdesh-booking-form__result tripdesh-booking-form__result--error';
						resultEl.textContent = (result.data && result.data.message) ? result.data.message : tripdeshData.i18n.bookingFailed;
					}
				})
				.catch(function () {
					resultEl.hidden = false;
					resultEl.className = 'tripdesh-booking-form__result tripdesh-booking-form__result--error';
					resultEl.textContent = tripdeshData.i18n.bookingFailed;
					submitBtn.disabled = false;
				});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		initChat();
		initBookingForm();
	});
})();
