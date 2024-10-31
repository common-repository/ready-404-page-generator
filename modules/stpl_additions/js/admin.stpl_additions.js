// Search worpdress form
function stplCanvasElementSearch(item, options) {
	stplCanvasElementSearch.superclass.constructor.apply(this, arguments);
}
extendFhf(stplCanvasElementSearch, stplCanvasElement);
stplCanvasElementSearch.prototype.init = function() {
	stplCanvasElementSearch.superclass.init.apply(this, arguments);
	var shortcode = '[ready_stpl_search_form]';
	this.applyAjax(shortcode);
};
stplCanvasElementSearch.prototype.close = function() {
	stplCanvasElementSearch.superclass.close.apply(this, arguments);
};
stplCanvasElementSearch.prototype.applyAjax = function(content) {
	this.makeAjaxContent({
		mod: 'stpl'
	,	action: 'getShortcodeHtml'
	,	shortcode: content
	}, content);
};
// Search worpdress form
function stplCanvasElementMenu(item, options) {
	stplCanvasElementMenu.superclass.constructor.apply(this, arguments);
}
extendFhf(stplCanvasElementMenu, stplCanvasElement);
stplCanvasElementMenu.prototype.init = function() {
	stplCanvasElementMenu.superclass.init.apply(this, arguments);
	this.editorDialogClosed = false;
	var self = this
	,	menuContentShell = jQuery('.fhfStplCanvasMenuShell:first').show()
	,	doneBtn = jQuery('<input type="button" value="Done" class="button-primary stplCanvasMenuDoneBtn" />').click(function(){
		self.editorDialog.dialog('close');
		return false;
	})
	,	shortcodeData = stplCanvasParseShortcode(this.item.find('.stplCanvasCellAjaxHideData').html());

	// First init
	if(!menuContentShell.find('.stplCanvasMenuDoneBtn').size()) {
		var menuSelect = menuContentShell.find('[name=menu]');
		menuContentShell.append('<br />').append(doneBtn);
		buildAjaxSelectFhf(menuSelect, {page: 'stpl_additions', action: 'getMenusListForSelect'}, {
			selectTxt: 'Select Menu'
		,	itemsKey: 'menus'
		,	idNameKeys: {id: 'term_id', name: 'name'}
		,	selectedValue: shortcodeData ? shortcodeData.menu : 0
		,	titlePrepareCallback: function(title, item) {
				title += ' ('+ item.count+')';
				return title;
			}
		});
	}
	this.editorDialog = toeShowDialogCustomized(menuContentShell, {
		height:		'auto'
	,	modal:		true
	,	closeOnBg:	true
	,	close: function() {
			self.editorDialogClosed = true;
			self.close();
		}
	});
	// If there was before set of static cntent - let's restore it in new edit window
	if(shortcodeData) {
		for(var key in shortcodeData) {
			this.editorDialog.find('[name="'+ key+ '"]').val( shortcodeData[key] );
		}
	}
};
stplCanvasElementMenu.prototype.close = function() {
	if(this.editorDialog && this.editorDialogClosed) {
		var shortcode = ''
		,	keysForCode = ['menu', 'add_classes', 'add_styles'];
		shortcode = '[ready_stpl_menu';
		for(var i in keysForCode) {
			shortcode += ' '+ keysForCode[i]+ '="'+ this.editorDialog.find('[name="'+ keysForCode[i]+ '"]').val()+ '"';
		}
		shortcode += ']';
		this.applyAjax( shortcode );
		if(!this.editorDialogClosed) {
			this.editorDialog.dialog('close');
		}
		this.editorDialogClosed = false;
	}
	stplCanvasElementMenu.superclass.close.apply(this, arguments);
};
stplCanvasElementMenu.prototype.applyAjax = function(content) {
	this.makeAjaxContent({
		mod: 'stpl'
	,	action: 'getShortcodeHtml'
	,	shortcode: content
	}, content);
};
// Subscribe form
function stplCanvasSubscribeForm(item, options) {
	stplCanvasSubscribeForm.superclass.constructor.apply(this, arguments);
}
extendFhf(stplCanvasSubscribeForm, stplCanvasElement);
stplCanvasSubscribeForm.prototype.init = function() {
	stplCanvasSubscribeForm.superclass.init.apply(this, arguments);
	this.editorDialogClosed = false;
	var self = this
	,	subscribeContentShell = jQuery('.fhfStplCanvasSubscribeFormShell:first').show()
	,	doneBtn = jQuery('<input type="button" value="Done" class="button-primary stplCanvasSubscribeFormDoneBtn" />').click(function(){
		self.editorDialog.dialog('close');
		return false;
	})
	,	shortcodeData = stplCanvasParseShortcode(this.item.find('.stplCanvasCellAjaxHideData').html());

	// First init
	if(!subscribeContentShell.find('.stplCanvasSubscribeFormDoneBtn').size()) {
		var subscribeListSelect = subscribeContentShell.find('[name=list]');
		subscribeContentShell.append('<br />').append(doneBtn);
		buildAjaxSelectFhf(subscribeListSelect, {page: 'subscribe', action: 'getListLists'}, {
			selectTxt: 'Select List'
		,	itemsKey: 'list'
		,	idNameKeys: {id: 'id', name: 'label'}
		,	selectedValue: shortcodeData ? shortcodeData.list : 0
		,	titlePrepareCallback: function(title, item) {
				title += ' ('+ item.subscribers_count+')';
				return title;
			}
		});
	}
	this.editorDialog = toeShowDialogCustomized(subscribeContentShell, {
		height:		'auto'
	,	modal:		true
	,	closeOnBg:	true
	,	close: function() {
			self.editorDialogClosed = true;
			self.close();
		}
	});
	// If there was before set of static cntent - let's restore it in new edit window
	if(shortcodeData) {
		for(var key in shortcodeData) {
			this.editorDialog.find('[name="'+ key+ '"]').val( shortcodeData[key] );
		}
	}
};
stplCanvasSubscribeForm.prototype.close = function() {
	if(this.editorDialog && this.editorDialogClosed) {
		var shortcode = ''
		,	keysForCode = ['list', 'subscr_form_title', 'subscr_enter_email_msg', 'subscr_success_msg'];
		shortcode = '[ready_subscribe_form';
		for(var i in keysForCode) {
			shortcode += ' '+ keysForCode[i]+ '="'+ this.editorDialog.find('[name="'+ keysForCode[i]+ '"]').val()+ '"';
		}
		shortcode += ']';
		this.applyAjax( shortcode );
		if(!this.editorDialogClosed) {
			this.editorDialog.dialog('close');
		}
		this.editorDialogClosed = false;
	}
	stplCanvasSubscribeForm.superclass.close.apply(this, arguments);
};
stplCanvasSubscribeForm.prototype.applyAjax = function(content) {
	this.makeAjaxContent({
		mod: 'stpl'
	,	action: 'getShortcodeHtml'
	,	shortcode: content
	}, content);
};