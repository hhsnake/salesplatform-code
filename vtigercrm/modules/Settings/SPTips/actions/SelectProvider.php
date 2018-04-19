<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_SelectProvider_Action extends Settings_Vtiger_Index_Action {
    
    const currentProviderTable = 'sp_tips_cur_provider';
    
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        
        $record = $request->get('record');
        $successOperation = false;
        if ($record) {
            $successOperation = $this->selectProvider(intval($record));
            if ($successOperation) {
                $response->setResult(array('success' => true));
            }
            else {
                $response->setError('JS_UNSUCCESSFULL'); 
            }
        }
        else {
            $response->setError('JS_NO_RECORD_IN_REQUEST');
        }
        $response->emit();
    }
    
    public function selectProvider($providerId) {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_SelectProvider_Action::currentProviderTable;
        $selectedProviderId = Settings_SPTips_SelectProvider_Action::getSelectedProviderId();
        if (empty($selectedProviderId)) {
            $sql = 'INSERT INTO ' . $tableName . ' (cur_provider) VALUES (?)';
            $result = $db->pquery($sql, array($providerId));
        }
        else {
            $sql = 'UPDATE ' . $tableName . ' set cur_provider=? WHERE cur_provider=?';
            $result = $db->pquery($sql, array($providerId, $selectedProviderId));
        }
        return $result; 
    }
    
    public function getSelectedProviderId() {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_SelectProvider_Action::currentProviderTable;
        $sql = 'SELECT * FROM ' . $tableName;
        $result = $db->pquery($sql, array());
        $selectedProviderId = null;
        if ($result) {
            $selectedProviderId = $db->query_result($result, 0, 'cur_provider');
        }
        return $selectedProviderId;
    }
}