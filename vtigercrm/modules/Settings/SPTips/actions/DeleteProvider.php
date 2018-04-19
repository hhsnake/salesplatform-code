<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_DeleteProvider_Action extends Settings_Vtiger_Index_Action {
    
    const providersTable = 'sp_tips_providers';
    
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $record = $request->get('record');
        if ($record) {
            $result = $this->deleteProvider($record);
            if ($result) {
               $response->setResult(array('success' => true)); 
            }
            else {
                $response->setError('JS_UNSUCCESFULL');
            }
        } else {
            $response->setError('JS_NO_RECORD_IN_REQUEST');
        }
        $response->emit();
    }
    
    public function deleteProvider($record) {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_DeleteProvider_Action::providersTable;
        $sql = 'DELETE FROM ' . $tableName . ' WHERE provider_id = ?';
        $result = $db->pquery($sql, array($record));
        return $result;
    }
   
}