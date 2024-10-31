function fhfSubscribeFormSend(form) {
	jQuery(form).sendFormFhf({
		msgElID: jQuery(form).find('.fhfSubscribeFormMsg:first')
	,	onSuccess: function(res) {
			if(!res.error) {
				jQuery(form).find('*:not(.fhfSubscribeFormSuccess)').remove();
				jQuery(form).find('.fhfSubscribeFormSuccess').show();
			}
		}
	});
}