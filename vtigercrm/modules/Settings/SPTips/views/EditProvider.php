<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_EditProvider_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        $providerId = $request->get('providerId');
        
        if ($providerId && Settings_SPTips_Provider_Model::isProviderExists($providerId)) {
            $recordInstance = Settings_SPTips_Provider_Model::getInstanceById($providerId); 
        } else {
            // returns provider instance, selected in system
            $recordInstance = Settings_SPTips_Provider_Model::getInstance();
        }
        
        $qualifiedModuleName = $request->getModule(false);
        $availableForAddingProviders = SPTips_AvailableProviders_Model::getAvailvableProviders();
        
        $viewer = $this->getViewer($request);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('RECORD_STRUCTURE', $recordInstance);
        $viewer->assign('AVAILABLE_PROVIDERS', $availableForAddingProviders);
        $viewer->view('EditProvider.tpl',$qualifiedModuleName);
    }
}