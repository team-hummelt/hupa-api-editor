let collapseElementList = [].slice.call(document.querySelectorAll('.collapse'));
let resetMsgAlert = document.getElementById("reset-msg-alert");
//Ajax Spinner
let ajaxFormSpinner = document.querySelectorAll(".ajax-status-spinner");

let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
	return new bootstrap.Popover(popoverTriggerEl)
});

/**======================================
 ========== AJAX SPINNER SHOW  ==========
 ========================================
 */
function show_ajax_spinner(data) {
	let msg = '';
	if (data.status) {
		msg = '<i class="text-success fa fa-check"></i>&nbsp; Saved! Last: ' + data.msg;
	} else {
		msg = '<i class="text-danger fa fa-exclamation-triangle"></i>&nbsp; ' + data.msg;
	}
	let spinner = Array.prototype.slice.call(ajaxFormSpinner, 0);
	spinner.forEach(function (spinner) {
		spinner.innerHTML = msg;
	});
}

(function( $ ) {
	'use strict';

	/**================================================
	 ========== TOGGLE FORMULAR COLLAPSE BTN  ==========
	 ===================================================
	 */
	let formularColBtn = document.querySelectorAll("button.btn-formular-collapse");
	if (formularColBtn) {
		let formCollapseEvent = Array.prototype.slice.call(formularColBtn, 0);
		formCollapseEvent.forEach(function (formCollapseEvent) {
			formCollapseEvent.addEventListener("click", function () {
				//Spinner hide
				if (resetMsgAlert) {
					resetMsgAlert.classList.remove('show');
				}

				if (ajaxFormSpinner) {
					let spinnerNodes = Array.prototype.slice.call(ajaxFormSpinner, 0);
					spinnerNodes.forEach(function (spinnerNodes) {
						spinnerNodes.innerHTML = '';
					});
				}

				this.blur();
				if (this.classList.contains("active")) return false;
				let siteTitle = document.getElementById("currentSideTitle");
				siteTitle.innerText = this.getAttribute('data-site');
				let btnType = this.getAttribute('data-type');
				switch (btnType) {
					case 'settings':

						break;
					case'formular':

						break;
					case'posteingang':

						break;
				}
				remove_active_btn();
				this.classList.add('active');
				this.setAttribute('disabled', true);
			});
		});

		function remove_active_btn() {
			for (let i = 0; i < formCollapseEvent.length; i++) {
				formCollapseEvent[i].classList.remove('active');
				formCollapseEvent[i].removeAttribute('disabled');
			}
		}
	}

	$(document).on('click', '.form-check-input, .btn', function () {
		$(this).trigger('blur')
	});


	function warning_message(msg) {
		let x = document.getElementById("snackbar-warning");
		$("#snackbar-warning").html(msg);
		x.className = "show";
		setTimeout(function () {
			x.className = x.className.replace("show", "");
		}, 5000);
	}

	function success_message(msg) {
		let x = document.getElementById("snackbar-success");
		x.innerHTML = msg;
		x.className = "show";
		setTimeout(function () {
			x.className = x.className.replace("show", "");
		}, 3000);
	}


	$.fn.serializeObject = function () {
		let o = {};
		let a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

})( jQuery );