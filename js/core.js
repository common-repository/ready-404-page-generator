if(typeof(FHF_DATA) == 'undefined')
	var FHF_DATA = {};
if(isNumber(FHF_DATA.animationSpeed)) 
    FHF_DATA.animationSpeed = parseInt(FHF_DATA.animationSpeed);
else if(jQuery.inArray(FHF_DATA.animationSpeed, ['fast', 'slow']) == -1)
    FHF_DATA.animationSpeed = 'fast';
FHF_DATA.showSubscreenOnCenter = parseInt(FHF_DATA.showSubscreenOnCenter);
var sdLoaderImgFhf = '<img src="'+ FHF_DATA.loader+ '" />';

jQuery.fn.showLoaderFhf = function() {
    jQuery(this).html( sdLoaderImgFhf );
}
jQuery.fn.appendLoaderFhf = function() {
    jQuery(this).append( sdLoaderImgFhf );
}

jQuery.sendFormFhf = function(params) {
	// Any html element can be used here
	return jQuery('<br />').sendFormFhf(params);
}
/**
 * Send form or just data to server by ajax and route response
 * @param string params.fid form element ID, if empty - current element will be used
 * @param string params.msgElID element ID to store result messages, if empty - element with ID "msg" will be used. Can be "noMessages" to not use this feature
 * @param function params.onSuccess funstion to do after success receive response. Be advised - "success" means that ajax response will be success
 * @param array params.data data to send if You don't want to send Your form data, will be set instead of all form data
 * @param array params.appendData data to append to sending request. In contrast to params.data will not erase form data
 * @param string params.inputsWraper element ID for inputs wraper, will be used if it is not a form
 * @param string params.clearMsg clear msg element after receive data, if is number - will use it to set time for clearing, else - if true - will clear msg element after 5 seconds
 */
jQuery.fn.sendFormFhf = function(params) {
    var form = null;
    if(!params)
        params = {fid: false, msgElID: false, onSuccess: false};

    if(params.fid)
        form = jQuery('#'+ fid);
    else
        form = jQuery(this);
    
    /* This method can be used not only from form data sending, it can be used just to send some data and fill in response msg or errors*/
    var sentFromForm = (jQuery(form).tagName() == 'FORM');
    var data = new Array();
    if(params.data)
        data = params.data;
    else if(sentFromForm)
        data = jQuery(form).serialize();
    
    if(params.appendData) {
		var dataIsString = typeof(data) === 'string';
		if(dataIsString)
			data += '&'+ jQuery.param( params.appendData );
		else {
			for(var i in params.appendData) {
				data[i] = params.appendData[i];
			}
		}
    }
    var msgEl = null;
    if(params.msgElID) {
        if(params.msgElID == 'noMessages')
            msgEl = false;
        else if(typeof(params.msgElID) == 'object')
           msgEl = params.msgElID;
       else
            msgEl = jQuery('#'+ params.msgElID);
    } else
        msgEl = jQuery('#msg');
	if(typeof(params.inputsWraper) == 'string') {
		form = jQuery('#'+ params.inputsWraper);
		sentFromForm = true;
	}
	if(sentFromForm && form) {
        jQuery(form).find('*').removeClass('fhfInputError');
    }
	if(msgEl) {
		jQuery(msgEl).removeClass('fhfSuccessMsg')
			.removeClass('fhfErrorMsg')
			.showLoaderFhf();
	}
    var url = '';
	if(typeof(params.url) != 'undefined')
		url = params.url;
    else if(typeof(ajaxurl) == 'undefined')
        url = FHF_DATA.ajaxurl;
    else
        url = ajaxurl;
    
    jQuery('.fhfErrorForField').hide(FHF_DATA.animationSpeed);
	var dataType = params.dataType ? params.dataType : 'json';
	// Set plugin orientation
	if(typeof(data) == 'string')
		data += '&pl='+ FHF_DATA.FHF_CODE;
	else
		data['pl'] = FHF_DATA.FHF_CODE;

    jQuery.ajax({
        url: url,
        data: data,
        type: 'POST',
        dataType: dataType,
        success: function(res) {
            toeProcessAjaxResponseFhf(res, msgEl, form, sentFromForm, params);
			if(params.clearMsg) {
				setTimeout(function(){
					jQuery(msgEl).animateClear();
				}, typeof(params.clearMsg) == 'boolean' ? 5000 : params.clearMsg);
			}
        }
    });
}

/**
 * Hide content in element and then clear it
 */
jQuery.fn.animateClear = function() {
	var newContent = jQuery('<span>'+ jQuery(this).html()+ '</span>');
	jQuery(this).html( newContent );
	jQuery(newContent).hide(FHF_DATA.animationSpeed, function(){
		jQuery(newContent).remove();
	});
}
/**
 * Hide content in element and then remove it
 */
jQuery.fn.animateRemove = function(animationSpeed) {
	animationSpeed = animationSpeed == undefined ? FHF_DATA.animationSpeed : animationSpeed;
	jQuery(this).hide(animationSpeed, function(){
		jQuery(this).remove();
	});
}

function toeProcessAjaxResponseFhf(res, msgEl, form, sentFromForm, params) {
    if(typeof(params) == 'undefined')
        params = {};
    if(typeof(msgEl) == 'string')
        msgEl = jQuery('#'+ msgEl);
    if(msgEl)
        jQuery(msgEl).html('');
    /*if(sentFromForm) {
        jQuery(form).find('*').removeClass('fhfInputError');
    }*/
    if(typeof(res) == 'object') {
        if(res.error) {
            if(msgEl) {
                jQuery(msgEl).removeClass('fhfSuccessMsg')
					.addClass('fhfErrorMsg');
            }
            for(var name in res.errors) {
                if(sentFromForm) {
                    jQuery(form).find('[name*="'+ name+ '"]').addClass('fhfInputError');
                }
                if(jQuery('.fhfErrorForField.toe_'+ nameToClassId(name)+ '').exists())
                    jQuery('.fhfErrorForField.toe_'+ nameToClassId(name)+ '').show().html(res.errors[name]);
                else if(msgEl)
                    jQuery(msgEl).append(res.errors[name]).append('<br />');
            }
        } else if(res.messages.length) {
            if(msgEl) {
                jQuery(msgEl).removeClass('fhfErrorMsg')
					.addClass('fhfSuccessMsg');
                for(var i in res.messages) {
                    jQuery(msgEl).append(res.messages[i]).append('<br />');
                }
            }
        }
    }
    if(params.onSuccess && typeof(params.onSuccess) == 'function') {
        params.onSuccess(res);
    }
}

function getDialogElementFhf() {
	return jQuery('<div/>').appendTo(jQuery('body'));
}

function toeOptionFhf(key) {
	if(FHF_DATA.options && FHF_DATA.options[ key ] && FHF_DATA.options[ key ].value)
		return FHF_DATA.options[ key ].value;
	return false;
}
function toeSetOptionFhf(key, value) {
	if(FHF_DATA.options && FHF_DATA.options[ key ] && FHF_DATA.options[ key ].value)
		FHF_DATA.options[ key ].value = value;
}
function toeLangFhf(key) {
	if(FHF_DATA.siteLang && FHF_DATA.siteLang[key])
		return FHF_DATA.siteLang[key];
	return key;
}
function toePagesFhf(key) {
	if(typeof(FHF_DATA) != 'undefined' && FHF_DATA[key])
		return FHF_DATA[key];
	return false;;
}
/**
 * This function will help us not to hide desc right now, but wait - maybe user will want to select some text or click on some link in it.
 */
function toeOptTimeoutHideDescriptionFhf() {
	jQuery('#fhfOptDescription').removeAttr('toeFixTip');
	setTimeout(function(){
		if(!jQuery('#fhfOptDescription').attr('toeFixTip'))
			toeOptHideDescriptionFhf();
	}, 500);
}
/**
 * Show description for options
 */
function toeOptShowDescriptionFhf(description, x, y, moveToLeft) {
    if(typeof(description) != 'undefined' && description != '') {
        if(!jQuery('#fhfOptDescription').size()) {
            jQuery('body').append('<div id="fhfOptDescription"></div>');
        }
		if(moveToLeft)
			jQuery('#fhfOptDescription').css('right', jQuery(window).width() - (x - 10));	// Show it on left side of target
		else
			jQuery('#fhfOptDescription').css('left', x + 10);
        jQuery('#fhfOptDescription').css('top', y);
        jQuery('#fhfOptDescription').show(200);
        jQuery('#fhfOptDescription').html(description);
    }
}
/**
 * Hide description for options
 */
function toeOptHideDescriptionFhf() {
	jQuery('#fhfOptDescription').removeAttr('toeFixTip');
    jQuery('#fhfOptDescription').hide(200);
}
function toeInArrayFhf(needle, haystack) {
	if(haystack) {
		for(var i in haystack) {
			if(haystack[i] == needle)
				return true;
		}
	}
	return false;
}
function getRandElIdFhf(pref) {
	if(!pref)
		pref = '';
	return pref+ (Math.floor(Math.random() * (99999999999 - 1 + 1)) + 1);
}
function getRowIdValFhf(element) {
	var id = parseInt(jQuery(element).parents('tr:first').find('[name=id]').val());
	if(id) {
		return id;
	} else
		console.log('Can not find ID!');
	return false;	
}
function extendFhf(Child, Parent) {
	var F = function() { }
	F.prototype = Parent.prototype
	Child.prototype = new F()
	Child.prototype.constructor = Child
	Child.superclass = Parent.prototype
}
function paramFhf(param) {
	var param = jQuery.extend({}, param);
	param['pl'] = FHF_DATA.FHF_CODE;
	return jQuery.param( param );
}
function createAjaxLinkFhf(param) {
	return FHF_DATA.ajaxurl+ '?'+ paramFhf(param);
}
function updateDataTableRow(datatable, rowId, rowData) {
	var tblRowId = getDataTableRowId(datatable, rowId);
	if(tblRowId !== false) {
		datatable.fnUpdate(rowData, tblRowId);
	}
}
function removeDataTableRow(datatable, rowId) {
	var tblRowId = getDataTableRowId(datatable, rowId);
	if(tblRowId !== false) {
		datatable.fnDeleteRow(tblRowId);
	}
}
function getDataTableRow(datatable, rowId) {
	var tblRowId = getDataTableRowId(datatable, rowId);
	if(tblRowId !== false) {
		return datatable.fnGetData(tblRowId);
	}
	return false;
}
function getDataTableRowId(datatable, rowId) {
	var cells = []
	,	rows = datatable.fnGetNodes()
	,	tblRowId = false;
	for(var i = 0; i < rows.length; i++){
		// Get HTML of 3rd column (for example)
		cells.push(jQuery(rows[i]).find('td:eq(0)').html()); 
	}
	if(cells.length) {
		for(var i = 0; i < cells.length; i++) {
			if(cells[i] == rowId) {
				tblRowId = i;
				break;
			}
		}
	}
	return tblRowId;
}
function buildAjaxSelectFhf(select, sendData, params) {
	var contMsg = jQuery('<span />').insertAfter( select );
	sendData.reqType = 'ajax';
	jQuery.sendFormFhf({
		msgElID: contMsg
	,	data: sendData
	,	onSuccess: function(res) {
			if(!res.error) {
				select.html('');
				if(params.selectTxt)
					select.append('<option value="0">'+ params.selectTxt+ '</option>');
				if(res.data[ params.itemsKey ]) {
					for(var i in res.data[ params.itemsKey ]) {
						var title = res.data[ params.itemsKey ][i][ params.idNameKeys.name ];//.post_title;
						if(params.titlePrepareCallback && typeof(params.titlePrepareCallback) === 'function') {
							title = params.titlePrepareCallback(title, res.data[ params.itemsKey ][i]);
						}
						select.append('<option value="'+ res.data[ params.itemsKey ][i][ params.idNameKeys.id ]+ '">'+ title+ '</option>');
					}
					if(typeof(params.selectedValue) !== 'undefined' && params.selectedValue !== null) {
						select.val( params.selectedValue );
					}
				}
			}
		}
	});
}
