function fhfEditTpl_tabActivate() {
	loadStplFhf({
		toElement: jQuery('#fhfAdminTplShell')
	,	id: toeOptionFhf('template')
	});
	jQuery('#fhfAdminTplSaveForm').find('[name=id]').val( toeOptionFhf('template') );
}
jQuery(document).ready(function(){
	jQuery('#fhfAdminTplSaveForm').submit(function(){
		var stplContent = stplCanvasGetCurrentContentFhf()
		,	saveData = {
			id:				jQuery(this).find('[name=id]').val()
		,	rows:			stplContent.rows
		,	style_params:	stplContent.style_params
		};
		jQuery(this).sendFormFhf({
			msgElID: fhfAdminTplSaveMsg
		,	appendData: {stpl: saveData}
		});
		return false;
	});
	selectTemplateImageFhf( fhfTplId );
});
function openTemplateEditFhf() {
	selectTabMainFhf('fhfEditTpl');
}
function openTemplateSelectFhf() {
	selectTabMainFhf('fhfSelectTpl');
}
function setTemplateOptionFhf(id) {
	if(id == toeOptionFhf('template')) {
		openTemplateEditFhf();
		return false;
	}
	jQuery('.fhfTemplatesList .fhfTemplatePrevShell-'+ id).css('opacity', 0.5);
	jQuery.sendFormFhf({
		data: {page: 'options', action: 'save', opt_values: {template: id}, code: 'template', reqType: 'ajax'}
	,	onSuccess: function(res) {
			jQuery('.fhfTemplatesList .fhfTemplatePrevShell-'+ id).css('opacity', 1);
			if(!res.error) {
				selectTemplateImageFhf(id);
				toeSetOptionFhf('template', res.data.real_stpl_id);
			}
		}
	});
	return false;
}
function selectTemplateImageFhf(id) {
	jQuery('.fhfTemplatesList .fhfTemplatePrevShell-existing .button')
			.val(toeLangFhf('Apply'))
			.removeClass('fhfTplSelected')
	if(id) {
		jQuery('.fhfTemplatesList .fhfTemplatePrevShell-'+ id+ ' .button')
			.val(toeLangFhf('Edit'))
			.addClass('fhfTplSelected');
	}
}