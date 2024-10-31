var fhfAdminFormChanged = [];
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(fhfAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	jQuery('#fhfAdminOptionsTabs').tabs().addClass('ui-tabs-vertical-res ui-helper-clearfix');
    jQuery('#fhfAdminOptionsTabs li').removeClass('ui-corner-top').addClass('ui-corner-left');
	jQuery('#fhfAdminOptionsTabs').on('tabsactivate', function(event, ui) {
		if(ui.newPanel) {
			var tabId = ui.newPanel.attr('id')
			,	funcName = tabId+ '_tabActivate';
			if(tabId && function_exists(funcName))  {
				window[funcName](event, ui);
			}
		}
	});
	
	jQuery('form input[type=submit]').click(function() {
		jQuery('input[type=submit]', jQuery(this).parents('form')).removeAttr('clicked');
		jQuery(this).attr('clicked', 'true');
	});
	
	jQuery('#fhfAdminOptionsForm').submit(function(){
		jQuery(this).sendFormFhf({
			msgElID: 'fhfAdminMainOptsMsg'
		});
		return false;
	});
	// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
	var formsPreventLeave = ['fhfAdminOptionsForm', 'fhfFhfAdminOptsForm', 'fhfAdminNewslettersSaveTplForm', 'fhfAdminNewslettersEditForm'];
	jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
		var formId = jQuery(this).parents('form:first').attr('id');
		changeAdminFormFhf(formId);
	});
	jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
		var formId = jQuery(this).parents('form:first').attr('id');
		changeAdminFormFhf(formId);
	});
	jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
		if(fhfAdminFormChanged.length) {
			var id = jQuery(this).attr('id');
			for(var i in fhfAdminFormChanged) {
				if(fhfAdminFormChanged[i] == id) {
					fhfAdminFormChanged.pop(i);
				}
			}
		}
	});
});
function toeShowModuleActivationPopupFhf(plugName, action, goto) {
	action = action ? action : 'activatePlugin';
	goto = goto ? goto : '';
	jQuery('#toeModActivationPopupFormFhf').find('input[name=plugName]').val(plugName);
	jQuery('#toeModActivationPopupFormFhf').find('input[name=action]').val(action);
	jQuery('#toeModActivationPopupFormFhf').find('input[name=goto]').val(goto);
	
	tb_show(toeLangFhf('Activate plugin'), '#TB_inline?width=710&height=220&inlineId=toeModActivationPopupShellFhf', false);
	var popupWidth = jQuery('#TB_ajaxContent').width()
	,	docWidth = jQuery(document).width();
	// Here I tried to fix usual wordpress popup displace to right side
	jQuery('#TB_window').css({'left': Math.round((docWidth - popupWidth)/2)+ 'px', 'margin-left': '0'});
}
function changeAdminFormFhf(formId) {
	if(jQuery.inArray(formId, fhfAdminFormChanged) == -1)
		fhfAdminFormChanged.push(formId);
}

function toeShowDialogCustomized(element, options) {
	options = jQuery.extend({
		resizable: false
	,	width: 500
	,	height: 300
	,	closeOnEscape: true
	,	open: function(event, ui) {
			jQuery('.ui-dialog-titlebar').css({
				'background-color': '#222222'
			,	'background-image': 'none'
			,	'border': 'none'
			,	'margin': '0'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'color': '#CFCFCF'
			,	'height': '27px'
			});
			jQuery('.ui-dialog-titlebar-close').css({
				'background': 'url("'+ FHF_DATA.cssPath+ 'img/tb-close.png") no-repeat scroll 0 0 transparent'
			,	'border': '0'
			,	'width': '15px'
			,	'height': '15px'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'margin': '6px 6px 0'
			,	'float': 'right'
			}).html('');
			jQuery('.ui-dialog').css({
				'border-radius': '3px'
			,	'background-color': '#FFFFFF'
			,	'background-image': 'none'
			,	'padding': '1px'
			,	'z-index': '300000'
			});
			jQuery('.ui-dialog-buttonpane').css({
				'background-color': '#FFFFFF'
			});
			jQuery('.ui-dialog-title').css({
				'color': '#CFCFCF'
			,	'font': '12px sans-serif'
			,	'padding': '6px 10px 0'
			});
			jQuery('.ui-widget-overlay').css({
				'z-index': jQuery( event.target ).parents('.ui-dialog:first').css('z-index') - 1
			,	'background-image': 'none'
			,	'position': 'fixed'
			});
			if(options.openCallback && typeof(options.openCallback) == 'function') {
				options.openCallback(event, ui);
			}
			if(options.modal && options.closeOnBg) {
				jQuery('.ui-widget-overlay').unbind('click').bind('click', function() {
					jQuery( element ).dialog('close');
				});
			}
		}
	}, options);
	return jQuery(element).dialog(options);
}
function selectTabMainFhf(id) {
	var index = jQuery('#fhfAdminOptionsTabs a[href="#'+ id+ '"]').parent().index();
	jQuery('#fhfAdminOptionsTabs').tabs('option', 'active', index);
}
