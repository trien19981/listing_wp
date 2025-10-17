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

	$( document ).on( 'click', '.removeConfirm', function( e ) {
		e.preventDefault();
		let ask = window.confirm("This action cannot be undone. Confirm remove action.");
		if ( ask && $(this).attr('href') !== undefined ) {
			// window.alert('Redirect to: ' + $(this).attr('href'));
			window.location = $(this).attr('href');
		} else {
			// window.alert('No redirection');
			// window.location = document.location.href;
		}
	});

})(jQuery);