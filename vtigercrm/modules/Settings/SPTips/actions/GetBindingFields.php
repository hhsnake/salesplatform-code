<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_GetBindingFields_Action extends Settings_Vtiger_Index_Action {
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        
        $moduleName = $request->get('sourceModule');
        if (isset($moduleName)) {
            $currentProvider = SPTips_CurrentProvider_Model::getProviderInstance();
            if (!$currentProvider) {
                $response->setError('Can\'t load provider');
                $response->emit();
                return;
            }
        }
        $providerId = Settings_SPTips_Provider_Model::getProviderIdByName($currentProvider->getName());
        $fieldForBinding = Settings_SPTips_Rule_Model::getBindingFieldForModule($moduleName, $providerId);
        
        if (empty($fieldForBinding)) {
            $response->setError('JS_FIELD_NOT_FOUND');
        }
        else {
            $response->setResult($fieldForBinding);
        }
        $response->emit();
    }
    
}