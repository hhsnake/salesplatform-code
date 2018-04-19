/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * Copyright (C) 2011-2015 SalesPlatform Ltd
 * All Rights Reserved.                                                              
 * Source code may not be redistributed unless expressly permitted by SalesPlatform Ltd.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

jQuery.Class("SPTips_Js", {}, {
    
    registerEventForEditProvider : function() {
        var thisInstance = this;
        jQuery('#editProvider').on('click', function() {
            var selectedProviderId = thisInstance.getSelectedProvderId();
            if (selectedProviderId) {
                location.href = "index.php?module=SPTips&view=EditProvider&parent=Settings&providerId=" + selectedProviderId;
            }
        });
    },
    
    registerEventForAddNewRule : function() {
        var thisInstance = this;
        jQuery('#addRule').on('click', function() {
            var selectedProviderId = thisInstance.getSelectedProvderId();
            if (selectedProviderId) {
                location.href = "index.php?module=SPTips&view=EditRules&parent=Settings&providerId=" + selectedProviderId;
            }
        });
    },
    
    getSelectedProvderId : function() {
        var selectedProviderOption = jQuery('[name="existingProviders"]').find(':selected');
        var selectedProviderId = selectedProviderOption.attr('data-provider_id');
        return selectedProviderId;
    },
    
    registerEventForLoadRulesTable : function() {
        var thisInstance = this;
        jQuery('#assignedToRoleTab').on('click', function() {
            thisInstance.loadRulesTable();
        });
    },
    
    loadRulesTable : function(selectedProvider) {
        var thisInstance = this;
        var params = {
            module: 'Settings:SPTips',
            view: 'ListRules'
        };
        
        if (typeof(selectedProvider) !== 'undefined') {
            params['selectedProvider'] = selectedProvider;
        }
        
        var aDeferred = jQuery.Deferred();
        app.request.post({data:params}).then(
            function(err, response){
                if (empty(err) && !empty(response)) {
                    jQuery('.rulesTable').html(response);
                    // register another events
                    thisInstance.registerEventForChangingProviderInListRules();
                    thisInstance.registerEventForDeleteRule();
                }
                aDeferred.resolve(response);
            },
            function(error){
                aDeferred.reject();
            }
        );
        return aDeferred.promise();
    },
    
    getCurrentModuleName : function() {
        return jQuery('[name="module"]').val();
    },
    
    makeFieldBinding : function(fieldName) {
        var thisInstance = this;
        var jsonFromServer;
        var aDeferred = jQuery.Deferred();
        var fieldElem = jQuery('[name="' + fieldName + '"]');
        fieldElem.autocomplete({
            'minLength' : '4',
            'wordCount' : '1', // need for textarea elements
            'source' : function(request, response){
                var searchParam = request.term;
                thisInstance.fillInFields(searchParam, fieldName).then(function(dataFromServer) {
                    var reponseDataList = new Array();
                    for (var item in dataFromServer) {
                        reponseDataList.push(item);
                    }
                    jsonFromServer = dataFromServer;
                    response(reponseDataList);
                    
                });
            },
            'select' : function(event, ui) {
                var selectedValue = ui.item.value;
                var addressObject = jsonFromServer[selectedValue];
                if (addressObject.length === 0) {
                    return;
                }
                for (var item in addressObject) {
                    if (addressObject[item] !== undefined) {
                        jQuery('[name="' + item +'"]').val(addressObject[item]);
                    }
                }
                return false;
            },
        });
        return aDeferred;
    },
    
    fillInFields : function(searchParam, fieldName) {
        var checkResult = jQuery.Deferred();
        var sourceModuleName = this.getCurrentModuleName();
        
        var data = {
            searchParam: searchParam,
            module: 'Settings:SPTips',
            action: 'SearchAddress',
            currentModule : sourceModuleName,
            currentField : fieldName
        };
        app.request.post({'data': data}).then(
            function (error, responseObj) {
                if (empty(error) && !empty(responseObj)) {
                    checkResult.resolve(responseObj);
                }
                else {
                    checkResult.reject();  
                }
            },
            function (error) {
                checkResult.reject();
        });
        return checkResult.promise();
    },
    
    checkModuleForTipsRule : function() {
        var thisInstace = this;
        if (app.getViewName() === 'Edit') {
            var aDeferred = jQuery.Deferred();
            var moduleName = this.getCurrentModuleName();
            
            var params = {
                sourceModule : moduleName,
                action : 'GetBindingFields',
                module : 'Settings:SPTips'
            };
            
            app.request.post({data:params}).then(
                function(err, response){
                    if (empty(err) && !empty(response)) {
                        thisInstace.makeFieldBinding(response);
                    }
                    aDeferred.resolve(response);
                },
                function(error){
                    aDeferred.reject();
                }
            );
        }
    },
    
    registerEventForSelectProvider : function() {
        jQuery("#selectProvider").on('click', function(e) {
            var selectedProviderElem = jQuery("#existingProviders").find(":selected");
            var data = {
                module: 'SPTips',
                action: 'SelectProvider',
                parent: 'Settings',
                record: selectedProviderElem.attr("data-provider_id")
            };
            
            app.request.post({'data': data}).then(
                function (error, responseObj) {
                    if (empty(error) && !empty(responseObj)) {
                        if (responseObj['success']) {
                            app.helper.showSuccessNotification({
                                message: app.vtranslate('JS_SUCCESSFULL')
                            });
                            location.reload();
                        }
                    }
                    else {
                        app.helper.showErrorNotification({
                            message: app.vtranslate('JS_UNSUCCESSFULL')
                        });
                    }
                },
                function error(error) {
                    console.log(error);
                }
            );
            
        });
    },
    
    registerEventForChangingModule : function() {
        var thisInstance = this;
        var form = jQuery("#rulesForm");
        form.find('[name="sourceModule"]').on('change', function() {
            var selectedModule = form.find('[name="sourceModule"]').val();
            var currentModule = thisInstance.getCurrentModuleName();
            thisInstance.loadFieldsForNewModule(selectedModule, currentModule).then(
                function(data) {
                    thisInstance.registerEventForChangingModule();
                    thisInstance.registerEventForAddingNewField();
                }
            );
        });
    },
    
    loadFieldsForNewModule : function(selectedModule, currentModule) {
        var aDeferred = jQuery.Deferred();
        var recordId = jQuery('[name="record"]').val();
        var providerId = jQuery('[name="providerId"]').val();
        app.helper.showProgress();
        var data = {
            module: currentModule,
            parent: app.getParentModuleName(),
            view: 'EditRules',
            operation: 'loadFieldsForNewModule',
            editRuleId: recordId,
            sourceModule: selectedModule,
            providerId: providerId
        };
        
        app.request.pjax({data: data}).then(
            function(error, data) {
                app.helper.hideProgress();
                var container = jQuery('.settingsPageDiv div');
                container.html(data);
                //register all select2 Elements
                vtUtils.showSelect2ElementView(container.find('select.select2'));
                aDeferred.resolve(data);
            },
            function(error) {
                app.helper.hideProgress();
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
        
    },
    
    registerEventForDeletingProvider : function() {
        var thisInstance = this;
        jQuery('#deleteProviderBtn').on('click', function() {
            var recordId = jQuery('[name="record"]').val();
            if (recordId !== undefined) {
                var data = {
                    module: thisInstance.getCurrentModuleName(),
                    parent: 'Settings',
                    action: 'DeleteProvider',
                    record: recordId
                };
                
                app.request.post({'data': data}).then(
                    function (error, responseObj) {
                        if (empty(error) && !empty(responseObj)) {
                            if (responseObj['success']) {
                                var refererUrl = window.location.protocol + window.location.pathname;
                                refererUrl += "?module=SPTips&view=Index&parent=Settings";
                                location.href = refererUrl;
                            }
                        }
                        else {
                            app.helper.showErrorNotification({
                                message: app.vtranslate('JS_UNSUCCESSFULL')
                            });
                        }
                    },
                    function error(error) {
                    }
                );
            }
            else {
                app.helper.showErrorNotification({
                    message: app.vtranslate('JS_UNSUCCESSFULL')
                });  
            }
        });
    },
    
    registerEventForAddingNewField : function() {
        var thisInstance = this;
        var lineItemCopy = jQuery('.lineItemCopy');
        // before copy we need to disable select2 on the select element
        lineItemCopy.find('select').each(function(index, elem) {
            jQuery(elem).select2('destroy');
            jQuery(elem).attr('disabled','disabled');
        });
        
        jQuery("#addDependendField").on('click', function(e) {
            // restore select2 after copy
            var newElement = lineItemCopy.clone().appendTo('.editViewContents');
            newElement.find('select').each(function(index, elem) {
                jQuery(elem).select2();
                jQuery(elem).removeAttr('disabled');
            });
            
            newElement.removeClass('lineItemCopy');
            newElement.removeClass('hide');
            
            thisInstance.registerEventForDeleteFieldsItemLine();
        });
    },
    
    registerEventForDeleteFieldsItemLine : function() {
        jQuery('.deleteFieldsItemLine').on('click', function(e) {
            var lineWithItems = jQuery(e.currentTarget).closest('div.form-group');
            lineWithItems.remove();
        });
    },
    
    registerEventForDeleteRule : function() {
        jQuery('.deleteRule').on('click', function(e) {
            var ruleId = jQuery(e.currentTarget).attr('data-rule-id');
            if (ruleId !== undefined) {
                var data = {
                    module: 'SPTips',
                    parent: 'Settings',
                    action: 'DeleteRule',
                    record: ruleId
                };
            }
            
            app.request.post({'data': data}).then(
                function(error, responseObj) {
                    if (empty(error) && !empty(responseObj)) {
                        if (responseObj['success']) {
                            var refererUrl = window.location.protocol + window.location.pathname;
                            refererUrl += "?module=SPTips&view=Index&parent=Settings";
                            location.href = refererUrl;
                        }
                    }
                    else {
                        app.helper.showErrorNotification({
                            message: app.vtranslate('JS_UNSUCCESSFULL')
                        });
                    }
                }, 
                function error(error) {
                    
                });
        });
    },
    
    loadFieldsForNewProvider : function(selectedProvider) {
        var aDeferred = jQuery.Deferred();
        app.helper.showProgress();
        var sourceModule = jQuery('[name="sourceModule"]').val();
        var data = {
            module: this.getCurrentModuleName(),
            parent: app.getParentModuleName(),
            view: 'EditRules',
            selectedProvider: selectedProvider,
            sourceModule: sourceModule
        };
        
        app.request.pjax({data: data}).then(
            function(error, data) {
                app.helper.hideProgress();
                var container = jQuery('.settingsPageDiv div');
                container.html(data);
                //register all select2 Elements
                vtUtils.showSelect2ElementView(container.find('select.select2'));
                aDeferred.resolve(data);
            },
            function(error) {
                app.helper.hideProgress();
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
    },
    
    registerEventForChangingProviderInListRules : function() {
        var thisInstance = this;
        jQuery('[name="existingProviders"]').on('change', function() {
           var selectedProvider = jQuery('[name="existingProviders"] option:selected').text();
           thisInstance.loadRulesTable(selectedProvider).then(
                function() {
                    //thisInstance.registerEventForChangingProviderInListRules();
                    //thisInstance.registerEventForDeleteRule();
                }
            );
        });
    },
    
    loadRulesForNewProvider : function(selectedProvider) {
        var aDeferred = jQuery.Deferred();
        app.helper.showProgress();
        var data = {
            module: 'SPTips',
            parent: app.getParentModuleName(),
            view: 'ListRules',
            selectedProvider: selectedProvider
        };
        
        app.request.pjax({data: data}).then(
            function(error, data) {
                app.helper.hideProgress();
                var container = jQuery('.settingsPageDiv div');
                container.html(data);
                aDeferred.resolve(data);
            },
            function(error) {
                app.helper.hideProgress();
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
    },
    
    registerEvents: function () {
        this.registerEventForSelectProvider();
        this.registerEventForChangingModule();
        this.checkModuleForTipsRule();
        this.registerEventForDeletingProvider();
        this.registerEventForAddingNewField();
        this.registerEventForLoadRulesTable();
        this.registerEventForEditProvider();
        this.registerEventForAddNewRule();
        this.registerEventForDeleteFieldsItemLine();
    }
});


$(document).ready(function () {
    var tipsController = new SPTips_Js();
    tipsController.registerEvents();
});



