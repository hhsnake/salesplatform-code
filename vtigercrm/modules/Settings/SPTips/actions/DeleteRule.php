<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_DeleteRule_Action extends Settings_Vtiger_Index_Action {
    
    const rulesTable = 'sp_tips_module_rules';
    const dependentFieldsTable = 'sp_tips_dependent_fields';
    
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $record = $request->get('record');
        if ($record) {
            $result = $this->deleteRule($record);
            if ($result) {
                $response->setResult(array('success' => true));
            }
            else {
                $response->setError('JS_UNSUCCESSFULL');
            }
        } else {
            $response->setError('JS_NO_RECORD_IN_REQUEST');
        }
        $response->emit();
    }
    
    public function deleteRule($record) {
        if (!$this->ruleExists($record)) {
            return false;
        }
        
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_DeleteRule_Action::rulesTable;
        $sql = 'DELETE FROM ' . $tableName . ' WHERE rule_id = ?';
        $result = $db->pquery($sql, array($record));
        return $result;
    }
    
    public function ruleExists($ruleId) {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_DeleteRule_Action::rulesTable;
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE rule_id = ?';
        $result = $db->pquery($sql, array($ruleId));
        if ($result) {
            return $db->num_rows($result);
        }
        return false;
    }
   
}