jQuery(document).ready(function ($) {
	// Открытие обычных изображений

	// Открытие модального окна для стилизации
	// Fancybox.show([{ src: '#status-success' }])

	// Все модальные окна
	Fancybox.bind('[data-modal]', {
		dragToClose: false,
		on: {
			init: function (fancybox) {
				const modal_id = fancybox.userSlides[0].src;
				const modal_success = $('#modal-success')

				if (modal_id == '#modal-review') {
					modal_success.find('.title').text('Thanks for the feedback');
					modal_success.find('.subtitle').text('it will appear on the site immediately after it passes moderation');
				} else if (modal_id == '#modal-question') {
					modal_success.find('.title').text('Thanks for the feedback');
					modal_success.find('.subtitle').text('it will appear on the site immediately after it passes moderation');
				} else {
					modal_success.find('.title').text('Thanks for the feedback');
					modal_success.find('.subtitle').text('it will appear on the site immediately after it passes moderation');
				}
			}
		}
	});

	// // Для товара в модальном окне
	// Fancybox.bind('[data-modal="product"]', {
	// 	dragToClose: false,
	// 	on: {
	// 		init: function (fancybox) {
	// 			const el = fancybox.userSlides[0].triggerEl;
	// 			const modal = $(fancybox.userSlides[0].src);
	// 			let title = el.getAttribute('title');
	//
	// 			if (title) {
	// 				modal.find('[name="subject"]').val(title);
	// 			}
	// 		},
	// 		destroy: (fancybox) => {
	// 			const modal = $(fancybox.userSlides[0].src);
	// 			let input = modal.find('[name="subject"]');
	// 			let title = input.attr('title') ? input.attr('title') : 'Оставить заявку';
	// 			input.val(title);
	// 		},
	// 	}
	// });

	// Для успешной отправки формы
	$(document).on('ajaxformsent', function (e, data) {
		triggerStatusModal('#modal-success');
	});

	// Для ошибки
	$(document).on('ajaxformerror', function (e) {
		triggerStatusModal('#modal-error');
	});

	// Удаление таймера при закрытии модального окна
	function triggerStatusModal(id) {
		Fancybox.close();

		Fancybox.show([{
			src: id,
			type: "inline"
		}], {
			dragToClose: false,
			on: {
				close: () => $(document).trigger('statusModalClosed'),
			}
		});

		let timerId = setTimeout(function () {
			Fancybox.close();
		}, 2500);

		$(document).on('statusModalClosed', () => {
			clearTimeout(timerId);
			$(document).off('statusModalClosed');
		})
	}

});
