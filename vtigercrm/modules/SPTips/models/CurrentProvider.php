<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_CurrentProvider_Model {
    
    public function getProviderInstance() {
        $currentProviderModel = Settings_SPTips_Provider_Model::getInstance();
        $providerName = $currentProviderModel->get('provider_name');
        $APIKey = $currentProviderModel->get('api_key');
        $secretKey = $currentProviderModel->get('token');
        
        if (stristr($providerName, 'DaData')) {
            return new SPTips_DaDataProvider_Model($providerName, $APIKey, $secretKey);
        }
        else if (stristr($providerName, 'Google')) {
            return new SPTips_GoogleProvider_Model($providerName, $APIKey, $secretKey);
        }
        // add new providers below
        
        return false;
    }
}