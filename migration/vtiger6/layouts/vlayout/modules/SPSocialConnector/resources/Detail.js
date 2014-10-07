/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("SPSocialConnector_Detail_Js",{

    detailInstance : false,
        
	getInstance: function(){
        if( SPSocialConnector_Detail_Js.detailInstance == false ){
            var module = app.getModuleName();
            var moduleClassName = module+"_Detail_Js";
            var fallbackClassName = SPSocialConnector_Detail_Js;
            if(typeof window[moduleClassName] != 'undefined'){
                var instance = new window[moduleClassName]();
            }else{
                var instance = new fallbackClassName();
            }
            SPSocialConnector_Detail_Js.detailInstance = instance;
        }
        return SPSocialConnector_Detail_Js.detailInstance;
	},
    
    /*
	 * function to trigger send Message to social nets
	 * @params: send message url
	 */
    triggerSendMessage : function(detailActionUrl) {
        
        SPSocialConnector_Detail_Js.triggerDetailViewActionSendMessage(detailActionUrl);	
        
    },
    
    /*
	 * function to trigger Detail view actions for SPSocialConnector module
	 * @params: Action url , callback function.
	 */
    triggerDetailViewActionSendMessage : function(detailActionUrl, callBackFunction){
		var detailInstance = SPSocialConnector_Detail_Js.getInstance();
        var selectedIds = new Array();
        selectedIds.push(detailInstance.getRecordId());
        var postData = {
           "selected_ids": JSON.stringify(selectedIds)
        };
        var actionParams = {
			"type":"POST",
			"url":detailActionUrl,
			"dataType":"html",
			"data" : postData
		};

        AppConnector.request(actionParams).then(
			function(data) {
				if(data) {
					app.showModalWindow(data, function(data){
                        SPSocialConnector_Detail_Js.registerURLFieldSelectionEvent();
					});			
				}
			},
			function(error,err){

			}
		);
    },
    
    /*
	 * function to call the register events of send message to the social nets
	 */
    registerURLFieldSelectionEvent : function() {
        var thisInstance = this;
		var selectEmailForm = jQuery("#massSave");
		selectEmailForm.on('submit',function(e){
			var form = jQuery(e.currentTarget);
			var params = JSON.stringify(form.serializeFormData());
            var obj = JSON.parse(params);
            var str = '&source_module='+obj.source_module+'&record_id='+obj.selected_ids+'&text='+obj.message+'&URL='+obj.fields;
            var win = window.open("index.php?module=SPSocialConnector&action=AuthWindow&popuptype=send_msg"+str,"test","top=100, left=100, width=1000, height=590, resizable=0, scrollbars=0");                
            app.hideModalWindow();
            var timer = setInterval(function() {   
                        if(win.closed) {  
                            clearInterval(timer);
                            location.reload(); 
                        }  
                        }, 500); 
			e.preventDefault();
		});
    }
	
},{
    registerEvents : function(){
        this._super();
    }
});


