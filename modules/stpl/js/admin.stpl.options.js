var fhfSortGlobalClone = null;
var stplEditorSettings = {};
var stplColPadding = 3;	// 2 for margin and 1 - for border
var stplColMinWidth = 15;	// Min width for cols
var stplElements = new stplCanvasElementsFabric();
var stplFontStyleKeys = ['font-family', 'font-size', 'color'];

function loadStplFhf(options) {
	options = options || {};
	if(options.toElement) {
		options.toElement = jQuery(options.toElement);
		options.id = parseInt(options.id);
		jQuery.sendFormFhf({
			msgElID: options.toElement
		,	data: {page: 'stpl', action: 'load', reqType: 'ajax', id: options.id}
		,	onSuccess: function(res) {
				if(!res.error) {
					console.time('tpl load');
					options.toElement.html( res.html );
					stplCanvasInitFhf( options.toElement, res.data.stpl );
					
					if(res.data.stpl)
						stplCanvasFillWithContent(res.data.stpl, options);
					console.timeEnd('tpl load');
				}
			}
		});
	} else
		console.log('loadStplFhf error - no element found!');
}

function stplCanvasAddRowFhf(butt, options) {
	options = options || {};
	
	var canvasShell = jQuery(butt).hasClass('fhfStplCanvasShell') ? jQuery(butt) : jQuery(butt).parents('.fhfStplCanvasShell:first')
	,	canvas = canvasShell.find('.fhfStplCanvas:first')
	,	row = jQuery('<div class="fhfStplCanvasRow" />')
	,	rowContent = jQuery('<div class="fhfStplCanvasRowContent" />')
	,	rowSettings = canvasShell.find('.fhfStplCanvasRowSettings.fhfExample').clone().removeClass('fhfExample')
	,	colsNumBox = canvasShell.find('.fhfStplCanvasRowColumnsNumShell.fhfExample').clone().removeClass('fhfExample')
	,	rowBgColorPicker = rowSettings.find('.fhfStplCanvasRowIconBgColor');

	colsNumBox.hide();
	// Compose full row data
	row.append( rowContent.append(rowSettings).append(colsNumBox) );
	if(options.insertBefore) {
		row.insertBefore( options.insertBefore );
	} else if(options.insertAfter) {
		row.insertAfter( options.insertAfter );
	} else
		canvas.append( row );

	if(options.background_color && options.background_color !== '') {
		rowBgColorPicker.val( options.background_color );
		stplCanvasSetBgRowColorChange(rowContent, options.background_color);
	}
	rowBgColorPicker.wpColorPicker({
		change: function(event) {
			stplCanvasSetBgRowColorChange(rowContent, jQuery(event.target).val());
		}
	});
	rowBgColorPicker.change();

	// Cols num input manipulations
	rowSettings.find('.fhfStplCanvasRowIconColumns').click(function(){
		colsNumBox.toggle();
		// Recalc columns num if we toggled box on
		if(colsNumBox.is(':visible')) {
			colsNumBox.find('.fhfStplCanvasRowColumnsNumText').val( rowContent.find('.fhfStplCanvasCol').size() );
		}
		return false;
	});
	// TODO: make hide of colsNumBox on out click, bellow code will not work - it will hide this item when we click on it inner elements
	/*jQuery('html').click(function(){
		colsNumBox.hide();
	});*/
	colsNumBox.find('.fhfStplCanvasRowColumnsNumButt').click(function(){
		var newColsNum = parseInt(colsNumBox.find('.fhfStplCanvasRowColumnsNumText').val())
		,	oldColsNum = parseInt(rowContent.find('.fhfStplCanvasCol').size());
		if(newColsNum && newColsNum !== oldColsNum) {
			if(newColsNum > oldColsNum) {
				for(var i = oldColsNum; i < newColsNum; i++) {
					stplCanvasAddColFhf(rowContent);
				}
			} else {
				for(var i = oldColsNum; i > newColsNum; i--) {
					stplCanvasRemoveColFhf(rowContent);
				}
			}
		}
		colsNumBox.hide();
		dragMaster.recalcDropObjects();
		return false;
	});
	// Remove row setting
	rowSettings.find('.fhfStplCanvasRowIconRemove').click(function(){
		stplCanvasRemoveRowFhf(row);
		return false;
	});
	
	rowContent.resizable({
		handles: 's'
	,	stop: function() {
			dragMaster.recalcDropObjects();
		}
	}).sortable({
		items:			'.fhfStplCanvasCol'
	,	handle:			'.fhfStplCanvasCellIconMove'
	,	placeholder: {
            element: function(item, ui) {
                return jQuery('<div class="fhfStplCanvasColSortHelper" />').css({
					'height': item.css('height')
				,	'width': item.css('width')
				}).html( item.html() );
            },
            update: function() {
                return;
            }
        }
	,	sort: function(event, ui) {
			var inserted = false;
			var i = 0;
			rowContent.find('.fhfStplCanvasCol:not(.ui-sortable-helper, .fhfStplCanvasColSortHelper)').each(function(){
				if(!inserted && ui.position.left < jQuery(this).position().left) {
					ui.placeholder.insertBefore( this );
					inserted = true;
				}
				i++;
			});
			if(!inserted) {
				ui.placeholder.insertAfter( rowContent.find('.fhfStplCanvasCol:not(.ui-sortable-helper, .fhfStplCanvasColSortHelper):last') );
			}
		}
	});
	stplCanvasAddColFhf(rowContent);
	
	if(options.height) {
		rowContent.height( options.height );
	}
	return rowContent;
}
function stplCanvasExcludeFromSortingFhf() {
	return ':not(.fhfStplCanvasCellSettings, .fhfStplCanvasCellSettings *, .fhfStplCanvasRowSettings, .fhfStplCanvasRowSettings *)';
}
function stplCanvasColsStopResize(element, ui, rowContent) {
	var parentOriginalWidth = rowContent.attr('original_width')
	,	innerColsSize = 0
	,	innerColsWidth = 0;

	rowContent.find('.fhfStplCanvasCol').each(function(){
		innerColsWidth += jQuery(this).width();
		innerColsSize++;
	});

	var colsWithPaddingWidth = innerColsWidth + 2 * innerColsSize * stplColPadding;
	if(colsWithPaddingWidth > parentOriginalWidth) {
		rowContent.width( parentOriginalWidth )
		jQuery(element)
			.width( jQuery(element).width() - (colsWithPaddingWidth - parentOriginalWidth) )
			.resizable('widget').trigger('mouseup');
		return true;
	}
	return false;
}
function stplCanvasAddColFhf(rowContent, options) {
	options = options || {};
	var col = jQuery('<div class="fhfStplCanvasCol" />')
	,	colContent = jQuery('<div class="fhfStplCanvasColContent">')
	,	colSettings = rowContent.parents('.fhfStplCanvasShell:first').find('.fhfStplCanvasCellSettings.fhfExample').clone().removeClass('fhfExample')
	//,	canvasContentSettings = rowContent.parents('.fhfStplCanvasShell').next('.fhfStplCanvasSettings').find('.fhfStplCanvasSettingsContent')
	,	randContentId = getRandElIdFhf('stplColContent_')
	,	randColId = getRandElIdFhf('stplCol_')
	,	elementClass = options.element_class ? options.element_class : 'stplCanvasElementText';	// Text element by default
	// Conpose full cell data
	col.append(colSettings).append(colContent);
	if(options.insertBefore) {
		col.insertBefore( options.insertBefore );
	} else if(options.insertAfter) {
		col.insertAfter( options.insertAfter );
	} else
		rowContent.append( col );
	
	
	colContent.attr('id', randContentId).attr('data-element', elementClass);
	col.attr('id', randColId);
	
	rowContent.find('.fhfStplCanvasRowEnd').remove();
	rowContent.append( jQuery('<div class="fhfStplCanvasRowEnd" />') );
	
	col.resizable({
		handles: 'e'
	,	resize: function(event, ui) {
			var nextElement = ui.element.next();
			if(nextElement.size() && nextElement.hasClass('fhfStplCanvasCol')) {
				var originalWidth = parseInt(nextElement.attr('original_width'));
				if(!originalWidth) {
					originalWidth = nextElement.width();
					nextElement.attr('original_width', originalWidth);
				}
				var newWidth = (originalWidth - (ui.size.width - ui.originalSize.width));
				if(newWidth > stplColMinWidth)
					nextElement.width( newWidth+ 'px' );
				else {
					jQuery(this).resizable('widget').trigger('mouseup');
				}
			} else {
				var prevElement = ui.element.prev();
				if(prevElement.size()) {
					var originalWidth = parseInt(prevElement.attr('original_width'));
					if(!originalWidth) {
						originalWidth = prevElement.width();
						prevElement.attr('original_width', originalWidth);
					}
					prevElement.width( (originalWidth - (ui.position.left - ui.originalPosition.left))+ 'px' );
				}
			}
			stplCanvasColsStopResize(this, ui, rowContent);
		}
	,	stop: function() {
			dragMaster.recalcDropObjects();
		}
	,	start: function(event, ui) {
			if(!rowContent.attr('original_width'))
				rowContent.attr('original_width', rowContent.width());
			stplCanvasColsStopResize(this, ui, rowContent);
		}
	});
	// Edit setting
	colSettings.find('.fhfStplCanvasCellIconEdit').click(function(){
		var editClass = colContent.data('element');
		stplCanvasStartEdit( editClass, colContent );
		return false;
	});
	// Remove setting
	colSettings.find('.fhfStplCanvasCellIconRemove').click(function(){
		stplCanvasRemoveColFhf(rowContent, col);
		return false;
	});
	
	if(options.width) {
		col.width( options.width );
	} else {
		stplCanvasUpdateColsWidth( rowContent );
	}
	if(options.content) {
		colContent.html( options.content );
	}
	var dropElement = new DropTarget( colContent.get(0) );
	colContent.attr('data-dropid', dropElement.getDropId());
	
	return colContent;
}
function stplCanvasRemoveRowFhf(row) {
	row.remove();
}
function stplCanvasRemoveColFhf(rowContent, col) {
	if(!col) {
		col = rowContent.find('.fhfStplCanvasCol:last');
	}
	dragMaster.removeDropObject( col.find('.fhfStplCanvasColContent:first').data('dropid') );
	col.remove();
	stplCanvasUpdateColsWidth(rowContent);
}
function stplCanvasCalcAvarageColsWidth(rowContent, colsNum) {
	colsNum = colsNum ? colsNum : rowContent.find('.fhfStplCanvasCol').size();
	return colsNum 
		? (rowContent.width() - 2 * stplColPadding - 2 * (colsNum - 1) * stplColPadding - 2 * 3 /*2*3 - don't know why for now*/) / colsNum 
		: 0;
}
function stplCanvasUpdateColsWidth(rowContent) {
	var newWidth = stplCanvasCalcAvarageColsWidth(rowContent);
	rowContent.find('.fhfStplCanvasCol').width(newWidth+ 'px');
	dragMaster.recalcDropObjects();
}
function stplCanvasGetCanvasFromSetElement(settingElement) {
	return jQuery(settingElement).parents('.fhfStplCanvasSettings:first').parent().find('.fhfStplCanvas:first');
}
function stplCanvasGetCanvasFromElement(element) {
	return jQuery(element).parents('.fhfStplCanvasShell:first').find('.fhfStplCanvas:first');
}
function stplCanvasGetSettingsFromSetElement(element) {
	return jQuery(element).parents('.fhfStplCanvasSettings:first');
}
function stplCanvasInitFhf(toElement) {
	var canvas = jQuery( toElement ).find('.fhfStplCanvas')
	,	canvasSettings = jQuery( toElement ).find('.fhfStplCanvasSettings');
	// Generate unique ID for each canvas element
	canvas.attr('id', getRandElIdFhf('stplCanvas_'));
	canvas.sortable({
		items: '.fhfStplCanvasRow'
	,	handle:	'.fhfStplCanvasRowIconMove'
	,	placeholder: {
            element: function(item, ui) {
                return jQuery('<div class="fhfStplCanvasRowSortHelper" />').css({
					'height': item.css('height')
				});
            },
            update: function() {
                return;
            }
        }
	});
	canvasSettings.tabs();
	canvasSettings.find('.fhfStplCanvasSettingBgTypeRadio input[type=radio]').change(function(){
		var shell = jQuery(this).parents('.fhfStplCanvasSettingBgTypeShell:first');
		shell.find('.fhfStplCanvasSettingBgTypeContainer').hide();
		shell.find('#'+ jQuery(this).attr('id')+ 'Container').show();
		switch(jQuery(this).val()) {
			case 'none':
				var canvas = stplCanvasGetCanvasFromSetElement(this);
				canvas.css({
					'background-image': 'none'
				,	'background-color': 'inherit'
				});
				break;
			case 'color':
				stplCanvasSetBgColorChange({
					target: canvasSettings.find('input[name=background_color]')
				});
				break;
			case 'image':
				canvasSettings.find('input[name=background_image]').trigger('change');
				canvasSettings.find('input[name=background_img_pos]:checked').trigger('change');
				break;
		}
	});
	canvasSettings.find('.fhfStplCanvasSettingBgTypeRadio, .fhfStplCanvasSettingBgImgPosRadio').buttonset();
	
	var _custom_media = true
	,	_orig_send_attachment = wp.media.editor.send.attachment;
	canvasSettings.find('.fhfStplCanvasSettingImageUploaderContainer .button').click(function(){
		var button = jQuery(this);
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				button.parents('.fhfStplCanvasSettingImageUploaderContainer:first').find('input[type=hidden]').val( attachment.url ).trigger('change');
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		};
		wp.media.editor.open(button);
		return false;
	});
	canvasSettings.find('.fhfStplCanvasSettingImageUploaderContainer .fhfStplCanvasSettingImageUploaderValue').change(function(){
		var imgUrl = jQuery(this).val();
		jQuery(this)
			.parents('.fhfStplCanvasSettingImageUploaderContainer:first')
			.find('.fhfStplCanvasSettingImageUploaderExample')
			.attr('src', imgUrl)
			.show();
		if(jQuery(this).parents('.fhfStplCanvasSettingBgTypeShell:first').find('input[name=background_type]:checked').val() === 'image') {	// If bg type - image - let's set it
			var canvas = stplCanvasGetCanvasFromSetElement(this);
			canvas.css({
				'background-image': 'url('+ imgUrl+ ')'
			});
		}
	});
	canvasSettings.find('.fhfStplCanvasSettingBgImgPosRadio input[type=radio]').change(function(){
		var canvas = stplCanvasGetCanvasFromSetElement(this);
		canvas
			.removeClass('fhfStplCanvasBgImgStretch')
			.removeClass('fhfStplCanvasBgImgCenter')
			.removeClass('fhfStplCanvasBgImgTile')
			.css('background-color', 'inherit');
		switch(jQuery(this).val()) {
			case 'stretch':
				canvas.addClass('fhfStplCanvasBgImgStretch');
				break;
			case 'tile':
				canvas.addClass('fhfStplCanvasBgImgTile');
				break;
			case 'center':
				canvas.addClass('fhfStplCanvasBgImgCenter');
				break;
		}
	});
	jQuery('body').on('click', '.add_media', function(){
		_custom_media = false;
	});
	
	jQuery('.fhfStplCanvasContentElementOriginal').each(function(){
		new DragObject(this);
	});
	stplCanvasSettingsPosition( canvas, canvasSettings, toElement );
	stplCanvasApplyAllFontStyles( canvas, canvasSettings );
	stplCanvasShowGrid( canvas );
}
function stplCanvasOnFontStyleChange(element) {
	if(typeof(element) === 'object') {
		// element.target is for changed colorpicker input - it return event, all other - just html object
		var changedElement = jQuery(element.target ? element.target : element)
		,	canvas = stplCanvasGetCanvasFromSetElement(changedElement)
		,	canvasSettings = stplCanvasGetSettingsFromSetElement(changedElement);
		stplCanvasApplyAllFontStyles(canvas, canvasSettings);
	}
}
function stplCanvasGetFontStyles(canvasSettings) {
	var stylesParsed = parseStr(canvasSettings.find('.fhfStplCanvasSettingStylesShell').serializeAnything());
	return stylesParsed && stylesParsed.font_style ? stylesParsed.font_style : false;
}
function stplCanvasApplyAllFontStyles(canvas, canvasSettings) {
	var styles = stplCanvasGetFontStyles(canvasSettings);
	if(styles) {
		var canvasId = canvas.attr('id')
		,	styleSheetId = canvasId+ '_styles'
		,	styleSheetDataArr = [];
		for(var key in styles) {
			switch(key) {
				default:
					var elStyleArr = []
					,	elSelector = '#'+ canvasId+ (styles[ key ].selector === '*' ? '' : ' '+ styles[ key ].selector);
					for(var i in stplFontStyleKeys) {
						elStyleArr.push(stplFontStyleKeys[i]+ ':'+ styles[ key ][ stplFontStyleKeys[i] ]+ ';');
					}
					if(inArray(styles[ key ].selector, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
						elStyleArr.push('font-weight:bold !important;');
					}
					
					styleSheetDataArr.push(elSelector+ ':not([class^="mce-"]):not([class^="wp"]):not([id^="mce"]):not(.button):not(.ed_button){'+ elStyleArr.join('')+ '}');
					break;
			}
		}
		jQuery('head').find('#'+ styleSheetId).remove();
		jQuery('head').append(
			jQuery('<style/>', {
				id:		styleSheetId
			,	html:	styleSheetDataArr.join(' ')
			})
		);
	}
}

function stplCanvasSettingsPosition(canvas, canvasSettings, toElement) {
	var toElementPosition = toElement.position();
	canvasSettings.css({
		'top': toElementPosition.top+ 'px'
	,	'left': (toElementPosition.left + canvas.width() + 80)+ 'px'
	});
}
function stplGetEditorOuterElement() {
	if(jQuery('#fhfStplCanvasEditorOuter').size())
		return jQuery('#fhfStplCanvasEditorOuter');
	return jQuery('<div id="fhfStplCanvasEditorOuter" >').appendTo('body');
}
function stplCanvasGetCurrentContentFhf() {
	stplElements.clear();
	var result	= {}
	,	rows	= []
	,	row_i	= 0
	,	col_i	= 0
	,	styleParams = {};
	if(jQuery('.fhfStplCanvas').size()) {
		jQuery('.fhfStplCanvas').find('.fhfStplCanvasRow').each(function(){
			var rowBackgroundColor = jQuery(this).find('.fhfStplCanvasRowContent:first').css('background-color');
			if(rowBackgroundColor === 'transparent')
				rowBackgroundColor = '';
			rows[ row_i ] = {
				cols:	[]
			,	height: jQuery(this).height()
			,	background_color: rowBackgroundColor
			};
			col_i = 0;
			jQuery(this).find('.fhfStplCanvasCol').each(function(){
				var clonedContent = jQuery(this).find('.fhfStplCanvasColContent').clone()
				,	saveHtml = ''
				,	elementClass = clonedContent.data('element');
				clonedContent.find('img').show();
				
				if(stplElements.isAjaxType(elementClass)) {
					saveHtml = clonedContent.find('.stplCanvasCellAjaxHideData').html();
				} else {
					saveHtml = clonedContent.html();
				}
				rows[ row_i ].cols[ col_i ] = {
					width:			jQuery(this).width()
				,	content:		saveHtml
				,	element_class:	elementClass
				};
				col_i++;
			});
			row_i++;
		});
		if(jQuery('#fhfStplCanvasSettings').size()) {
			jQuery('#fhfStplCanvasSettings').find('input, select, textarea').each(function(){
				if(jQuery(this).attr('type') === 'radio' && !jQuery(this).attr('checked')) return;
				var inputName = jQuery(this).attr('name');
				if(!inputName) return;
				// Collect font styles in other way
				if(strpos(inputName, 'font_style[') === 0) return;
				styleParams[ inputName ] = jQuery(this).val();
			});
			styleParams['font_style'] = stplCanvasGetFontStyles( jQuery('#fhfStplCanvasSettings') );
		}
	}
	result = {
		rows:			rows
	,	style_params:	styleParams
	};
	return result;
}
function stplCanvasFillWithContent(stpl, options) {
	options = options || {};
	if(stpl.rows) {
		var canvasShell = jQuery( options.toElement ).find('.fhfStplCanvasShell');
		for(var i in stpl.rows) {
			var rowContent = stplCanvasAddRowFhf(canvasShell, {
				height: stpl.rows[i].height
			,	background_color: stpl.rows[i].background_color
			});
			if(stpl.rows[i].cols) {
				stplCanvasRemoveColFhf( rowContent );
				for(var j in stpl.rows[i].cols) {
					var colContent = stplCanvasAddColFhf(rowContent, {
						width:			stpl.rows[i].cols[j].width
					,	content:		stpl.rows[i].cols[j].content
					,	element_class:	stpl.rows[i].cols[j].element_class
					});
					if(stplElements.isAjaxType(stpl.rows[i].cols[j].element_class)) {
						var newEditElement = new window[ stpl.rows[i].cols[j].element_class ]( jQuery(colContent), {
							elementClassName: stpl.rows[i].cols[j].element_class
						});
						newEditElement.applyAjax( stpl.rows[i].cols[j].content );
					}
				}
			}
		}
		
		// Try to fill in subject line
		var subjectInput = jQuery( options.toElement ).parents('form:first').find('[name=subject]');
		if(subjectInput && subjectInput.size()) {
			subjectInput.val( options.subject ? options.subject : '' );
		}
	}
	if(stpl.style_params) {
		var backgroundImgPos = stpl.style_params.background_img_pos ? stpl.style_params.background_img_pos : 'stretch'
		,	backgroundType = stpl.style_params.background_type ? stpl.style_params.background_type : 'none'
		,	canvasSettings = jQuery( options.toElement ).find('.fhfStplCanvasSettings');

		canvasSettings.find('input[name=background_type]').removeAttr('checked');
		canvasSettings.find('input[name=background_type][value="'+ backgroundType+ '"]').attr('checked', 'checked').trigger('change');
		canvasSettings.find('input[name=background_color]').wpColorPicker( 'color', stpl.style_params.background_color );
		canvasSettings.find('input[name=background_image]').val( stpl.style_params.background_image ).trigger('change');
		canvasSettings.find('input[name=background_img_pos]').removeAttr('checked');
		canvasSettings.find('input[name=background_img_pos][value="'+ backgroundImgPos+ '"]').attr('checked', 'checked').trigger('change');

		switch(backgroundType) {
			case 'color':
				stplCanvasSetBgColorChange({
					target: canvasSettings.find('input[name=background_color]')
				});
				break;
		}
		if(stpl.style_params.font_style) {
			for(var key in stpl.style_params.font_style) {
				for(var i in stplFontStyleKeys) {
					var fontStyleSetElement = canvasSettings.find('[name="font_style['+ key+ ']['+ stplFontStyleKeys[i]+ ']"]')
					,	fontStyleSetValue = stpl.style_params.font_style[ key ][ stplFontStyleKeys[i] ];
					if(stplFontStyleKeys[i] === 'color') {	// Special for colorpicker
						fontStyleSetElement.wpColorPicker( 'color', fontStyleSetValue );
					} else {
						fontStyleSetElement.val( fontStyleSetValue );
					}
				}
			}
		}
	}
	dragMaster.recalcDropObjects();
}
function stplCanvasPreviewInBrowserLinkClick(link) {
	var parentForm = jQuery(link).parents('form:first')
	,	idElement = parentForm ? parentForm.find('input[name=id]') : false
	,	id = idElement && idElement.size() ? idElement.val() : 0
	,	subject = parentForm ? parentForm.find('input[name=subject]').val() : ''
	,	msgEl = '';
	
	if(jQuery(link).parent().find('.stplCanvasTmpPrevMsgEl').size()) {
		msgEl = jQuery(link).parent().find('.stplCanvasTmpPrevMsgEl');
	} else {
		msgEl = jQuery('<div class="stplCanvasTmpPrevMsgEl"/>');
		jQuery(msgEl).insertAfter(link);
	}
	stplCanvasPreviewInBrowserWidthSave({
		idElement:	idElement
	,	id:			id
	,	msgEl:		msgEl
	,	subject:	subject
	});
}
function stplCanvasPreviewInBrowserWidthSave(options) {
	options = options || {};
	var stplContent = stplCanvasGetCurrentContentFhf()
	,	saveData = {
		id:				options.id
	,	rows:			stplContent.rows
	,	style_params:	stplContent.style_params
	};
	// Let's save it from first
	jQuery.sendFormFhf({
		msgElID: options.msgEl ? options.msgEl : ''
	,	data: {page: 'stpl', action: 'save', reqType: 'ajax', stpl: saveData}
	,	onSuccess: function(res) {
			if(!res.error) {
				if(options.idElement) {
					options.idElement.val( res.data.stpl.id );
				}
				stplCanvasPreviewInBrowser(res.data.stpl.id, options);
			}
		}
	});
	
}
function stplCanvasPreviewInBrowser(id, options) {
	options = options || {};
	window.open(FHF_DATA.siteUrl+ '/ready-404-preview-'+ Math.round(Math.random() * 1000));	// Just random page - to access 404 template
}

function stplCanvasSwitchImagesButtClick(butt) {
	var canvas = jQuery(butt).parents('.fhfStplCanvasShell:first').find('.fhfStplCanvas:first');
	jQuery(butt).attr('checked')
		? stplCanvasShowImages(canvas)
		: stplCanvasHideImages(canvas);
}
function stplCanvasSwitchGridButtClick(butt) {
	var canvas = stplCanvasGetCanvasFromElement(butt);
	jQuery(butt).attr('checked')
		? stplCanvasShowGrid(canvas)
		: stplCanvasHideGrid(canvas);
}
function stplCanvasHideImages(canvas) {
	jQuery(canvas).find('img').hide();
}
function stplCanvasShowImages(canvas) {
	jQuery(canvas).find('img').show();
}
function stplCanvasShowGrid(canvas) {
	jQuery('head').append(
		jQuery('<style/>', {
			id: 'stplCanvasGridStyles'
		,	html: '.fhfStplCanvasCol { border: 1px grey dashed; margin: 2px; border-radius: 5px; } .fhfStplCanvasRowContent { border: 1px dashed #000000; margin: 0px; border-radius: 5px; }'
		})
	);
}
function stplCanvasHideGrid(canvas) {
	jQuery('#stplCanvasGridStyles').remove();
}
function stplCanvasSetBgColorChange(event) {
	var canvas = stplCanvasGetCanvasFromSetElement(event.target);
	canvas.css({
		'background-color': jQuery(event.target).val()
	,	'background-image': 'none'
	});
}
function stplCanvasSetBgRowColorChange(rowContent, color) {
	rowContent.css({
		'background-color': color
	});	
}
jQuery(document).ready(function(){
	jQuery('html').mousedown(function(event){
		stplElements.outClick(event);
	});
});
function stplCanvasStartEdit(editElementClass, cellElement) {
	stplElements.clear();
	var newEditElement = new window[ editElementClass ]( jQuery(cellElement), {
		elementClassName: editElementClass
	});
	newEditElement.init();
	stplElements.push(newEditElement);
}
function stplCanvasGetTotalHeight(content) {
	var	heightCalcElement = jQuery('<div />').html( content )	// We will use this tmp dummy element to calc height of new content
	,	newContentTotalHeight = 0;
	jQuery('body').append( heightCalcElement );
	// Calc new total height included all children height and margins
	heightCalcElement.children().each(function(){
		newContentTotalHeight += jQuery(this).outerHeight(true); // true = include margins
	});
	heightCalcElement.remove();
	return newContentTotalHeight;
}
function stplCanvasParseShortcode(shortcode, codename) {
	if(!shortcode)
		return false;
	var res = {}
	,	parseExpression = /[\w-]+="[^"]*"/
	,	nameValStr = null;
	if((nameValStr = shortcode.match(parseExpression))) {
		while(nameValStr) {
			nameValStr = nameValStr[0];
			shortcode = str_replace(shortcode, nameValStr, '');
			var nameValArr = str_replace(nameValStr, '"', '').split('=');
			if(typeof(nameValArr[0]) !== 'undefined' && typeof(nameValArr[1]) !== 'undefined') {
				res[ nameValArr[0] ] = nameValArr[1];
			}
			nameValStr = shortcode.match(parseExpression)
		}
		return res;
	}
	return false;
}
