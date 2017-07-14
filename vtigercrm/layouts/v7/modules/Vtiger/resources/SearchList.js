/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class("Vtiger_SearchList_Js", {
	intializeListInstances: function (container) {
		container.find('.listViewPageDiv').each(function (index, domEle) {
			var container = jQuery(domEle);
			var moduleListInstance = new Vtiger_ModuleList_Js();
			moduleListInstance.setModuleName(container.find('[name="search_module"]').val()).setListViewContainer(container);
			moduleListInstance.registerEvents();
		});
	}
},{});

Vtiger_List_Js("Vtiger_ModuleList_Js", {}, {
	searchModule: false,
	addComponents: function () {

	},
	getSearchValue: function () {
		return jQuery('#searchValue').val();
	},
	getDefaultParams: function () {
		var container = this.getListViewContainer();
		var searchParams = {
			'module': this.getModuleName(),
			'view'	: 'ListAjax',
			'mode'	: 'showSearchResultsWithValue',
			'value'	: this.getSearchValue,
			'recordsCount': container.find('[name="recordsCount"]').val()
		};
		var parentDefaultParams = this._super();
		var defaultParams = jQuery.extend(parentDefaultParams, searchParams);
		defaultParams.parent = '';
		return defaultParams;
	},
	registerPageNavigationEvents: function () {
		var self = this;
		var container = this.getListViewContainer();
		container.on('click', '.nextPageButton', function (e) {
			var pageNumber = container.find('[name="pageNumber"]').val();
			var nextPageNumber = parseInt(parseFloat(pageNumber)) + 1;
                        //SalesPlatform.ru begin
			var params = {
                            page: nextPageNumber,
                            module: 'Vtiger',
                            view: 'ListAjax',
                            mode: 'searchAll',
                        };
                        //var params = {};
			//params.page= nextPageNumber;
                        //SalesPlatform.ru end
                        
			self.loadListViewRecords(params);
		});
		container.on('click', '.previousPageButton', function (e) {
			var pageNumber = container.find('[name="pageNumber"]').val();
			var previousPageNumber = parseInt(parseFloat(pageNumber)) - 1;
			if (pageNumber > 1) {
                            //SalesPlatform.ru begin
                            var params = {
                                page: previousPageNumber,
                                module: 'Vtiger',
                                view: 'ListAjax',
                                mode: 'searchAll',
                            };
				//var params = {};
				//params.page= previousPageNumber;
                            //SalesPlatform.ru end
				self.loadListViewRecords(params);
			}
			
		});
	},
	registerRemoveListViewSort: function () {
		var listViewContainer = this.getListViewContainer();
		var thisInstance = this;
		listViewContainer.on('click', '.removeSorting', function (e) {
			listViewContainer.find('[name="sortOrder"]').val('');
			listViewContainer.find('[name="orderBy"]').val('');
			thisInstance.loadListViewRecords();
		});
	},
	loadListViewRecords: function (urlParams) {
		var self = this;
		var aDeferred = jQuery.Deferred();
		var defParams = self.getDefaultParams();
                //Salesplatform.ru begin
                defParams.showModule = defParams.module;
                //Salesplatform.ru end
		if (typeof urlParams == "undefined") {
			urlParams = {};
		}
		if (typeof urlParams.search_params == "undefined") {
			urlParams.search_params = JSON.stringify(self.getListSearchParams(false));
		}
		urlParams = jQuery.extend(defParams, urlParams);
		app.helper.showProgress();

		app.request.post({data: urlParams}).then(function (err, res) {
                    
                    //SalesPlatform.ru begin
                        res = $(res);
                        $(res).find('.clearfix').remove();
                    //SalesPlatform.ru end
			aDeferred.resolve(res);
			self.placeListContents(res);
			app.event.trigger('post.listViewFilter.click', jQuery('.searchRow'));
			app.helper.hideProgress();
			self.markSelectedIdsCheckboxes();
			self.registerDynamicListHeaders();
			self.registerPostLoadListViewActions();
		});
		return aDeferred.promise();
	},
	registerEditLink: function () {
		var container = this.getListViewContainer();
		container.on('click', '.editlink', function (e) {
			var element = jQuery(e.currentTarget);
			var url = element.find('a').data('url');
			var listInstance = Vtiger_List_Js.getInstance();
			var postData = listInstance.getDefaultParams();
			postData['view'] = app.view();
			var recordId = app.getRecordId();
			if (!recordId) {
				recordId = jQuery('[name="record"]').val();
			}
			if (recordId && typeof recordId != "undefined") {
				postData['record'] = recordId;
			}
			if (postData['module'] == 'Workflows' && postData['view'] == 'Edit') {
				postData['mode'] = 'V7Edit';
			}
			for (var key in postData) {
				if (postData[key]) {
					postData['return'+key] = postData[key];
					delete postData[key];
				} else {
					delete postData[key];
				}
			}
			e.preventDefault();
			e.stopPropagation();
			window.location.href = url+'&'+$.param(postData);
		});
	},
	registerDeleteRecordClickEvent: function () {
		var thisInstance = this;
		var container = this.getListViewContainer();
		container.on('click', '.deleteRecordButton', function (e) {
			var elem = jQuery(e.currentTarget);
			var parent = elem;
			var params = {};

			var originalDropDownMenu = elem.closest('.dropdown-menu').data('original-menu');
			if (originalDropDownMenu && typeof originalDropDownMenu != 'undefined') {
				parent = app.helper.getDropDownmenuParent(originalDropDownMenu);

				var moduleName = jQuery('#searchModuleList').val();
				if (moduleName && typeof moduleName != 'undefined') {
					params['module'] = moduleName;
				}
			}
			var recordId = parent.closest('tr').data('id');
			var module = parent.closest('.moduleSearchResults').find('[name="search_module"]').val();
			thisInstance.deleteRecord(recordId, {'module': module});
		});
	},
	loadResult: function (viewdEle) {
		var searchString = jQuery('#searchKey').val();
		var latestResultsBlockEle = viewdEle.prev('.moduleSearchResults.groupstartvalue');
		var groupStart = latestResultsBlockEle.find('.groupStart').val();
		var appendToEle = jQuery('.moduleResults-container');

		var url = {
			"value": searchString,
			"start": groupStart,
			"mode": "searchAll"
		};
		var basicSearch = new Vtiger_BasicSearch_Js();
		app.helper.showProgress();
		basicSearch.search(url).then(function (data, error) {
			var ele = jQuery(data);
			ele.appendTo(appendToEle);
			ele.each(function (index, domEle) {
				var container = jQuery(domEle);
				var moduleListInstance = new Vtiger_ModuleList_Js();
				moduleListInstance.setModuleName(container.find('[name="search_module"]').val()).setListViewContainer(container);
				moduleListInstance.registerEvents();
			});
			app.helper.hideProgress();
		});

	},
	registerDropdownPosition: function () {
		var container = this.getListViewContainer();
		jQuery('.table-actions').on('click', '.dropdown', function (e) {
			var containerTarget = jQuery(this).closest(container);
			var dropdown = jQuery(e.currentTarget);
			if (dropdown.find('[data-toggle]').length <= 0) {
				return;
			}
			var dropdown_menu = dropdown.find('.dropdown-menu');

			var dropdownStyle = dropdown_menu.find('li a');
			dropdownStyle.css('padding', "0 6px", 'important');

			var fixed_dropdown_menu = dropdown_menu.clone(true);
			fixed_dropdown_menu.data('original-menu', dropdown_menu);
			dropdown_menu.css('position', 'relative');
			dropdown_menu.css('display', 'none');
			var currtargetTop;
			var currtargetLeft;
			var ftop = 'auto';
			var fbottom = 'auto';

			var ctop = container.offset().top;
			currtargetTop = dropdown.offset().top-ctop+dropdown.height()+100;
			currtargetLeft = dropdown.offset().left-15;
			var dropdownftop = dropdown.position().top-dropdown_menu.height()+dropdown.height()+100;
			var windowBottom = jQuery(window).height()-dropdown.offset().top;
			if (windowBottom < 250) {
				ftop = dropdownftop+'px';
				fbottom = 'auto';
			} else {
				ftop = currtargetTop+'px';
				fbottom = "auto";
			}
			fixed_dropdown_menu.css({
				'display': 'block',
				'position': 'absolute',
				'top': ftop,
				'left': currtargetLeft+'px',
				'bottom': fbottom
			}).appendTo(containerTarget);

			dropdown.on('hidden.bs.getListViewContainerdropdown', function () {
				dropdown_menu.removeClass('invisible');
				fixed_dropdown_menu.remove();
			});
		});
	},
	registerEvents: function () {
		this.registerPageNavigationEvents();
		this.registerListViewSort();
		this.registerEventToShowQuickPreview();
		this.registerStarToggle();
		this.registerRemoveListViewSort();
		this.registerRowClickEvent();
		this.registerEditLink();
		this.registerDropdownPosition();
	}
});
