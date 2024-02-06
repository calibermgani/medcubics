<script type="text/javascript"> 
	/*** Starts - Select2, ICheck and Input Mask Classes assigned here ***/ 
	$(function () {     
		$(".select2").select2(); /* Select 2 Starts Trigger */
		/* iCheck for checkbox and radio inputs */
		$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass: 'iradio_minimal-blue'
		});
		
		//Red color scheme for iCheck
		$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
			checkboxClass: 'icheckbox_minimal-red',
			radioClass: 'iradio_minimal-red'
		});

		//Flat red color scheme for iCheck
		$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});

		// Dropdown Slidedown Effect
		$('.dropdown').on('show.bs.dropdown', function(e){
			$(this).find('.dropdown-menu').first().stop(true, true).slideDown();
		});

		$('.dropdown').on('hide.bs.dropdown', function(e){
			$(this).find('.dropdown-menu').first().stop(true, true).slideUp();
		});

		/* List and Grid View */		
		$('#list').click(function(event){event.preventDefault();$('#products .item').addClass('list-group-item');});
		$('#grid').click(function(event){event.preventDefault();$('#products .item').removeClass('list-group-item');$('#products .item').addClass('grid-group-item');});

		/* Input Mask */
		$('.dm-tax-id').mask('000000000', {placeholder: ""});
		$('.dm-npi').mask('0000000000', {placeholder: ""});
		$('.dm-clia-no').mask('00S0000000', {placeholder: ""});
		$('.dm-unit').mask('ZZZZZZ', {translation: {'Z': {pattern: /[0-9.]/, optional: true}}});
		$('.dm-medicare').mask('ZZZZZZZZZZZZZZZ', {translation: {'Z': {pattern: /[a-zA-Z0-9]/, optional: true}}});
		$('.dm-bcbsid').mask('ZZZZZZZZZZZZZZZ', {translation: {'Z': {pattern: /[a-zA-Z0-9]/, optional: true}}});
		$('.dm-fda').mask('ZZZZZZZZZZZZZZZ', {translation: {'Z': {pattern: /[a-zA-Z0-9]/, optional: true}}});
		$('.dm-checkno').mask('000000000000000', {translation: {'Z': {pattern: /[0-9]/, optional: true}}});
		$('.dm-medicaid').mask('ZZZZZZZZZZZZZZZ', {translation: {'Z': {pattern: /[a-zA-Z0-9]/, optional: true}}});
		$('.dm-careplan').mask('ZZZZZZZZZZZZZZZZZZZZZZZZZ', {translation: {'Z': {pattern: /[a-zA-Z0-9]/, optional: true}}});
		$('.dm-address').mask('ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ', {translation: {'Z': {pattern: /[@a-zA-Z0-9 -!#$%^&*()_\-+|~=`{}\[\]:";'<>?,.\/]/, optional: true}}}); /* add spl char #- */
		$('.dm-mi').mask('S');
		$('.dm-time').mask('00:00 SS');
		$('.dm-zip5').mask('00000');
		$('.dm-accno').mask('00000');
		$('.dm-copay-amount').mask('00000000');
		$('.dm-money').mask('000');
		$('.dm-agelimit').mask('00');
		$('.dm-state').mask('SS');
		$('.dm-zip4').mask('0000');
		$('.dm-filing-days').mask('000');
		$('.dm-phone,.dm-fax').mask('(SSS) SSS-SSSS', {translation: {'S': {pattern: /[a-zA-Z0-9]/, optional: true}}});
		$('.dm-phone-ext').mask('0000');
		$('.dm-ssn').mask('000000000');
		$('.dm-etin_type_no').mask('00-0000000');
		$('.dm-date').mask('99/99/9999');
		$('.js-date-range').mask('99/99/9999 - 99/99/9999');
		$('.dm-year').mask('0000');
		$('.dm-time').mask('00:00 SS');
		$('.dm-title').mask('SSSS');
		$('.dm-shortname').mask('SSS');
		$('.dm-auth-visits').mask('000');
		$('.dm-per-week').mask('00');
		$('.dm-pat-accno').mask('SSS00000');
		$('.dele').mask('00000-000', {
			onComplete: function () {
			},
			onKeyPress: function (event, currentField, options) {
			},
			onInvalid: function (val, e, field, invalid, options) {
				var error = invalid[0];
			}
		});
		var SPMaskBehavior = function (val) {
			return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
		},
		spOptions = {
			onKeyPress: function (val, e, field, options) {
				field.mask(SPMaskBehavior.apply({}, arguments), options);
			}
		};
	});
	/*** Ends - Input Mask Classes assigned here ***/  
</script>    