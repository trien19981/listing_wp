function selectBillingModel()
{
	(function($){
		let radioModel = $('input[name="ad_model"]:checked');
		let radioValues = $(".bsaProInputsValues");
		let radioValuesCPC = $(".bsaProInputsValuesCPC");
		let radioValuesCPM = $(".bsaProInputsValuesCPM");
		let radioValuesCPD = $(".bsaProInputsValuesCPD");
		$('input[name="ad_limit_cpc"]').prop('checked', false);
		$('input[name="ad_limit_cpm"]').prop('checked', false);
		$('input[name="ad_limit_cpd"]').prop('checked', false);
		$('input[name="ad_model"]').on('click', function() {
			$('.bsaInputInnerModel').removeClass('bsaSelected');
		});
		radioValues.slideUp();
		if ( radioModel.val() === 'cpc' ) {
			radioValuesCPC.slideDown();
			radioModel.parent(1).addClass('bsaSelected');
		} else if ( radioModel.val() === 'cpm' ) {
			radioValuesCPM.slideDown();
			radioModel.parent(1).addClass('bsaSelected');
		} else if ( radioModel.val() === 'cpd' ) {
			radioValuesCPD.slideDown();
			radioModel.parent(1).addClass('bsaSelected');
		}
	})(jQuery);
}
(function($){
	$(document).ready(function(){
		// order form redirection
		if ( $('#bsaSuccessProRedirect').length ) {
			let getRedirectUrl = $('#bsa_payment_url').val();
			setTimeout(function() {
				window.location.replace(getRedirectUrl);
			}, 2000);
		}
		// agency redirection
		if ( $('#bsaSuccessProAgencyRedirect').length ) {
			let getAgencyRedirectUrl = $('#bsa_payment_agency_url').val();
			setTimeout(function() {
				window.location.replace(getAgencyRedirectUrl);
			}, 2000);
		}
		// viewable layer
		let bsaPopupLayer = $('.bsaPopupLayer');
		bsaPopupLayer.each(function() {
			let child = $(this).first('.bsaProItem');
			let childId = $(this).first('.bsaProItem').data('item-id');
			if ( childId ) {
				child.removeClass('bsaProItem').hide();
				$(this).attr('data-item-id', childId).addClass('bsaProItem');
			}
		});
		// show ads
		setTimeout(function() {
			let bsaProItem = $('.bsaProItem');
			bsaProItem.each(function () {
				if ($(this).data('animation') != null && $(this).data('animation') !== 'none') {
					$(this).addClass('bsaToAnimate');
				} else {
					$(this).fadeIn(500).removeClass('bsaHidden');
				}
			});
		}, 250);
		setTimeout(function() {
			let bsaProItem = $('.bsaProItem');
			bsaProItem.each(function () {
				if ( $(this).hasClass('bsaToAnimate') && $(this).itemViewableScreen() === true ) {
					$(this).addClass('animated ' + $(this).data('animation')).removeClass('bsaHidden bsaToAnimate');
				}
			});
		}, 500);
		// viewable screen
		$.fn.itemViewableScreen = function(){
			let win = $(window);
			let viewport = {
				top : win.scrollTop(),
				left : win.scrollLeft()
			};
			viewport.right = viewport.left + win.width();
			viewport.bottom = viewport.top + win.height();

			let bounds = this.offset();
			bounds.right = bounds.left + this.outerWidth();
			bounds.bottom = bounds.top + this.outerHeight();
			return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
		};
		let bodyTag = $('body');
		if ( bodyTag.hasClass('bsa-pro-is-admin') === false && bodyTag.hasClass('viewable-enabled') === true ) {
			// set timeout
			let vis = (function(){
				let stateKey, eventKey, keys = {
					hidden: "visibilitychange",
					webkitHidden: "webkitvisibilitychange",
					mozHidden: "mozvisibilitychange",
					msHidden: "msvisibilitychange"
				};
				for (stateKey in keys) {
					if (stateKey in document) {
						eventKey = keys[stateKey];
						break;
					}
				}
				return function(c) {
					if (c) document.addEventListener(eventKey, c);
					return !document[stateKey];
				}
			})();
			let myTimeout = setInterval(itemViewableChecker, 1000);
			document.addEventListener('visibilitychange', function (event) {
				if (document.hidden) {
					clearInterval(myTimeout);
				} else {
					myTimeout = setInterval(itemViewableChecker, 1000);
				}
			});
			// viewable checker
			let ads = {};
			let ids = '';
			let counter = 0;
			let limit = 3;
			function itemViewableChecker() {
				counter++;
				let unique = {};
				let bsaProItem = $('.bsaProItem');
				bsaProItem.each(function() {
					if ( $(this).hasClass('inactiveItem') === false ) { // $(this).hasClass('bsaHidden') === false
						let $id = $(this).data('item-id');
						// $(this).offset().top
						// $(window).height()
						if ($id > 0 && $(this).itemViewableScreen() === true && vis() === true && $(this).height() > 0 && $(this).offset().top > 0 ||
							$id > 0 && $(this).attr('data-layer-id') > 0 && $(this).hasClass('animated') ||
							$id > 0 && $(this).attr('data-background-id') > 0 )
						{
							// set times
							ads['time' + $id] = (ads['time' + $id] > 0 ? ads['time' + $id] + 1 : 1);
							ads['timeOut' + $id] = (ads['timeOut' + $id] > 0 ? ads['timeOut' + $id] : 1);
							// timeout items
							if ( ads['time' + $id] > ads['timeOut' + $id] && ads['timeOut' + $id] < 20 ) {
								// unique items
								if ( !unique[$id] ) {
									// push visible ad id
									ids += $id + ',';
									unique[$id] = 1;
									ads['timeOut' + $id] = 1;
									ads['time' + $id] = 0;
									// console.log('ID: ' + $id);
									// console.log('TIME: ' + ads['time' + $id]);
									// console.log('TIMEOUT: ' + ads['timeOut' + $id]);
									// console.log('- - - -');
									// $.post(bsa_object.ajax_url, {action:'bsa_viewable_callback', a_id: $id, seconds: ads['timeOut' + $id]}, function(result) {
									// 	// correct post, reset counters
									// 	if ( result > 0 ) {
									// 		ads['timeOut' + $id] = (parseInt(ads['time' + $id]) + parseInt(result));
									// 		ads['time' + $id] = 0;
									// 	}
									// });
								}
							}
						}
					}
				});
				// console.log(counter);
				// console.log(ids);
				if ( ids !== '' && counter > limit ) {
					// sent request
					$.post(bsa_object.ajax_url, {action:'bsa_viewable_callback', a_id: ids, time: counter}, function(result) {
						// correct post, reset counters
						if ( result > 0 ) {
							limit = result;
							// console.log('sent ' + ids);
							// reset variables
							ids = '';
							counter = 0;
						}
					});
				}
			}
		}
		// animation trigger
		$(window).on('scroll', function () {
			let scrollTop = $(this).scrollTop();
			let windowSize = $(this).height();
			let bsaProItem = $('.bsaProItem');
			bsaProItem.each(function () {
				let prev = $(this).offset();
				// console.log($(this).attr('data-item-id'));
				// console.log(scrollTop + windowSize);
				// console.log(prev.top);
				if ( $(this).hasClass('bsaToAnimate') && (scrollTop + windowSize - prev.top) > 0 ) {
					$(this).addClass('animated ' + $(this).data('animation')).removeClass('bsaHidden bsaToAnimate');
				}
			});
		});
	});
})(jQuery);