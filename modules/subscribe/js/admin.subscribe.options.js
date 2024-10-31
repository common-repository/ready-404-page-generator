var fhfSubersPerPage = 10
,	fhfSubersTable = null
,	fhfSubersListsPerPage = 20
,	fhfSubersListsTable = null
,	fhfSubersTotalSubscribers = 0
,	fhfSubersAllLists = []
,	fhfSubersAddForm = null;
jQuery(document).ready(function(){
	jQuery('#fhfFhfAdminOptsForm').submit(function(){
		jQuery(this).sendFormFhf({
			msgElID: 'fhfAdminFhfOptionsMsg'
		});
		return false;
	});
	jQuery('#fhfSubscribersListsAddButt').click(function(){
		fhfSubscrbShowEditListForm();
		return false;
	});
	jQuery('#fhfSubscribersAddButt').click(function(){
		fhfSubscrbShowEditForm();
		return false;
	});
	jQuery('#fhfSubscribersFilterByListSel').change(function(){
		getSubersListFhf();
	});
	getSubersListFhf();
	getSubersListListsFhf();
	fhfSubscrbBuildListFilter();
});
function fhfSubscrbCloseAddForm(button) {
	jQuery(button).parents('form:first').dialog('close');
	return false;
}
function getSubersListFhf(page) {
	this.page;	// Let's save page ID here, in static variable
	if(typeof(this.page) == 'undefined')
		this.page = 0;
	if(typeof(page) != 'undefined')
		this.page = page;
	
	var page = this.page;
	var filterListId = jQuery('#fhfSubscribersFilterByListSel').val();
	
	jQuery.sendFormFhf({
		msgElID: 'fhfAdminSubersMsg'
	,	data: {page: 'subscribe', action: 'getList', reqType: 'ajax', limitFrom: page * fhfSubersPerPage, limitTo: fhfSubersPerPage, filterListId: filterListId}
	,	onSuccess: function(res) {
			if(!res.error) {
				if(page > 0 && res.data.count > 0 && res.data.list.length == 0) {	// No results on this page - 
					// Let's load next page
					getSubersListFhf(page - 1);
				} else {
					fhfSubersTable = new toeListableFhf({
						table: '#fhfAdminSubersTable'
					,	paging: '#fhfAdminSubersPaging'
					,	list: res.data.list
					,	count: res.data.count
					,	perPage: fhfSubersPerPage
					,	page: page
					,	pagingCallback: getSubersListFhf
					,	emptyMsg: toeLangFhf('No Subscribers')
					});
				}
			}
		}
	});
}

function getSubersListListsFhf(page) {
	this.page;	// Let's save page ID here, in static variable
	if(typeof(this.page) == 'undefined')
		this.page = 0;
	if(typeof(page) != 'undefined')
		this.page = page;
	
	var page = this.page;
	
	jQuery.sendFormFhf({
		msgElID: 'fhfAdminSubersListsMsg'
	,	data: {page: 'subscribe', action: 'getListLists', reqType: 'ajax', limitFrom: page * fhfSubersListsPerPage, limitTo: fhfSubersListsPerPage}
	,	onSuccess: function(res) {
			if(!res.error) {
				if(page > 0 && res.data.count > 0 && res.data.list.length == 0) {	// No results on this page - 
					// Let's load next page
					getSubersListListsFhf(page - 1);
				} else {
					fhfSubersListsTable = new toeListableFhf({
						table: '#fhfAdminSubersListsTable'
					,	paging: '#fhfAdminSubersListsPaging'
					,	list: res.data.list
					,	count: res.data.count
					,	perPage: fhfSubersListsPerPage
					,	page: page
					,	pagingCallback: getSubersListListsFhf
					});
					if(res.data.list) {
						for(var i in res.data.list) {
							if(parseInt(res.data.list[i].protected)) {
								fhfSubersListsTable.makeRowUneditable( res.data.list[i].id );
							}
						}
					}
				}
			}
		}
	});
}
function fhfSubscrbChangeStatus(link) {
	var id = parseInt(jQuery(link).parents('tr').find('.id').val());
	if(id) {
		jQuery.sendFormFhf({
			msgElID: 'fhfAdminSubersMsg'
		,	data: {page: 'subscribe', action: 'changeStatus', reqType: 'ajax', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					if(jQuery(link).hasClass('active')) {
						jQuery(link).removeClass('active').addClass('disabled');
					} else {
						jQuery(link).removeClass('disabled').addClass('active');
					}
				}
			}
		});
	}
}
function fhfSubscrbRemove(link) {
	if(confirm(toeLangFhf('Are you sure?'))) {
		var id = parseInt(jQuery(link).parents('tr').find('.id').val());
		if(id) {
			jQuery.sendFormFhf({
				msgElID: 'fhfAdminSubersMsg'
			,	data: {page: 'subscribe', action: 'remove', reqType: 'ajax', id: id}
			,	onSuccess: function(res) {
					if(!res.error) {
						getSubersListFhf();
					}
				}
			});
		}
	}
}
function fhfSubscrbListRemove(link) {
	var id = parseInt(jQuery(link).parents('tr').find('.id').val());
	if(confirm(toeLangFhf('Are you sure want to delete list?'))) {
		if(id) {
			var msgEl = jQuery('<div />');
			jQuery(link).parents('td:first').append( msgEl );
			jQuery.sendFormFhf({
				msgElID: msgEl
			,	data: {page: 'subscribe', action: 'removeList', reqType: 'ajax', id: id}
			,	onSuccess: function(res) {
					if(!res.error) {
						fhfSubscrbRemoveListFromAll(id);
						fhfSubersListsTable.removeRowById(id);
						getSubersListListsFhf();
					}
				}
			});
		}
	}
}
function fhfSubscrbFillInListForm(data, form) {
	form.find('[name=id]').val( data.id );
	form.find('[name=label]').val( data.label );
	form.find('[name=description]').val( data.description );
}
function fhfSubscrbShowEditListForm(element) {
	var form = jQuery('#fhfAdminSubersListsForm').clone().removeAttr('id').show()
	,	msgEl = form.find('.fhfAdminSubersListsFormMsg:first');
	toeShowDialogCustomized(form, {
		height: 'auto'
	,	modal: true
	,	closeOnBg: true
	});
	if(element) {
		var id = parseInt(jQuery(element).parents('tr:first').find('.id').val());
		if(id) {
			var listData = fhfSubersListsTable.getRowById(id);
			if(listData) {
				fhfSubscrbFillInListForm(listData, form);
			}
		}			
	}
	form.submit(function(){
		jQuery(this).sendFormFhf({
			msgElID: msgEl
		,	onSuccess: function(res) {
				if(!res.error) {
					getSubersListListsFhf();
					if(res.data.list) {
						if(!element)	// Add action - add it to list filter
							fhfSubscrbAddListToAll( res.data.list );
						fhfSubscrbFillInListForm(res.data.list, form);
					}
				}
			}
		});
		return false;
	});
	fhfSubersAddForm = form;
}
function fhfSubscrbFillInForm(data, form) {
	form.find('[name=id]').val( data.id );
	form.find('[name=email]').val( data.email );
}
function fhfSubersListsFulList(options) {
	this.loaded;
	options = options || {};
	if(options.reset) {
		this.loaded = false;
		return false;
	}
	if(options.loadTo) {
		options.loadTo = jQuery( options.loadTo );
	}
	var loadToHtmlCallback = function(listsData){
		if(options.loadTo) {
			options.loadTo
				.html('')						// Clear it from prev. html
				.removeClass('fhfSuccessMsg');	// It can stay from ajax request as here was ajax responce
			for(var i in listsData) {
				var checkedStr = '';
				if(options.selectedLists && inArray(listsData[i].id, options.selectedLists))
					checkedStr = 'checked="checked"';
				options.loadTo.append('<label><input type="checkbox" name="list[]" value="'+ listsData[i].id+ '" '+ checkedStr+ ' />&nbsp;'+ listsData[i].label+ '</label><br />');
			}
		}
	};

	loadToHtmlCallback( fhfSubersAllLists );

	return fhfSubersAllLists;
}
function fhfSubscrbShowEditForm(element) {
	var form = jQuery('#fhfAdminSubersForm').clone().removeAttr('id').show()
	,	msgEl = form.find('.fhfAdminSubersFormMsg:first')
	,	selectedLists = [];
	toeShowDialogCustomized(form, {
		height: 'auto'
	,	modal: true
	,	closeOnBg: true
	});
	
	if(element) {
		var id = parseInt(jQuery(element).parents('tr:first').find('.id').val());
		if(id) {
			var subscriberData = fhfSubersTable.getRowById(id);
			if(subscriberData) {
				fhfSubscrbFillInForm(subscriberData, form);
				if(subscriberData.list) {
					for(var i in subscriberData.list) {
						selectedLists.push( subscriberData.list[i] );
					}
				}
			}
		}
	}
	fhfSubersListsFulList({
		loadTo: form.find('.fhfAdminSubersFormListsShell:first')
	,	selectedLists: selectedLists
	});
	form.submit(function(){
		jQuery(this).sendFormFhf({
			msgElID: msgEl
		,	onSuccess: function(res) {
				if(!res.error) {
					getSubersListFhf();
					if(res.data.subscriber) {
						fhfSubscrbFillInForm(res.data.subscriber, form);
					}
				}
			}
		});
		return false;
	});
}
function fhfSubscrbBuildListFilter() {
	var currentSelectedOption = parseInt(jQuery('#fhfSubscribersFilterByListSel').val());
	if(!currentSelectedOption)
		currentSelectedOption = 0;

	jQuery('#fhfSubscribersFilterByListSel').find('option').remove();
	if(fhfSubersAllLists && fhfSubersAllLists.length) {
		for(var i in fhfSubersAllLists) {
			var subscribersCount = parseInt(fhfSubersAllLists[i].subscribers_count);
			if(!subscribersCount)
				subscribersCount = 0;
			jQuery('#fhfSubscribersFilterByListSel').append('<option value="'+ fhfSubersAllLists[i].id+ '">'+ fhfSubersAllLists[i].label+ ' ('+ subscribersCount+ ')</option>');
		}
	}
	jQuery('#fhfSubscribersFilterByListSel')
		.prepend('<option value="0">'+ toeLangFhf('All')+' ('+ fhfSubersTotalSubscribers+ ')</option>')
		.find('option[value='+ currentSelectedOption+ ']')
		.attr('selected', 'selected');
	
}
function fhfSubscrbRemoveListFromAll(listId) {
	// For some case if we do not find this ID in current set - don't reload select box
	var found = false;
	for(var i in fhfSubersAllLists) {
		if(fhfSubersAllLists[i].id == listId) {
			fhfSubersAllLists.splice(i, 1);
			found = true;
			break;
		}
	}
	if(found)
		fhfSubscrbBuildListFilter();
}
function fhfSubscrbAddListToAll(list) {
	fhfSubersAllLists.push( list );
	fhfSubscrbBuildListFilter();
}