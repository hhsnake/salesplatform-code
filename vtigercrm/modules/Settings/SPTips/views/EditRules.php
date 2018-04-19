<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_EditRules_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);        
        $availableModules = Settings_SPTips_Rule_Model::getAvailablePicklistModules();
        
        $record = $request->get('record');
        if(!empty($record)) {
            $recordModel = Settings_SPTips_Rule_Model::getInstanceById($record);
            $selectedModule = $recordModel->get('sourceModule');
            $providerId = $recordModel->get('providerId');
        } else {
            $sourceField = $request->get('sourcefield');
            $selectedModule = $request->get('sourceModule');
            if (empty($selectedModule)) {
                $selectedModule = $availableModules[0]->name;
            }
            
            $targetField = $request->get('targetfield');
            $providerFields = $request->get('providerField');
            $providerId = $request->get('providerId');
            $recordModel = Settings_SPTips_Rule_Model::getInstance(null, $selectedModule, $sourceField, $targetField, $providerFields, $providerId);
        }
        
        $availableFields = Settings_SPTips_Rule_Model::getAvailableModuleFields($selectedModule);
        $existingProviders = Settings_SPTips_Provider_Model::getAllProviders();
        
        $ruleId = false;
        // first condition: edit rule without changing module
        if ($record) {
            $ruleId = $record;
        }
        // second condition: edit rule and changed source module
        else if ($request->get('operation') == 'loadFieldsForNewModule') {
            $ruleId = $request->get('editRuleId');
        }
        
        $viewer = $this->getViewer($request);
        // include file contains provider class
        $providerInstance = Settings_SPTips_Provider_Model::getInstanceById($providerId);
        $providerName = $providerInstance->get('provider_name');
        if (!$providerName) {
            $viewer->view('Error.tpl',$qualifiedModuleName);
            return;
        }
        $providerClassName = Settings_SPTips_Provider_Model::getProviderClassName($providerName);
        $viewer->assign('PROVIDER_PICKLIST_FIELDS', $providerClassName::getProviderFields());
        
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('AVAILABLE_MODULES', $availableModules);
        $viewer->assign('SELECTED_MODULE',$selectedModule);
        $viewer->assign('PICKLIST_FIELDS',$availableFields);
        
        $viewer->assign('EXISTING_PROVIDERS', $existingProviders);
        $viewer->assign('CURRENT_PROVIDER_ID', $providerId);
        $viewer->assign('PROVIDER_NAME', $providerName);
        $viewer->assign('RULE_ID', $ruleId);
        $viewer->assign('RECORD_MODEL',$recordModel);
        $viewer->view('EditRules.tpl',$qualifiedModuleName);
    }
}
