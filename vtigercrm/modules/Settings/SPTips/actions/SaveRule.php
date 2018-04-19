<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_SaveRule_Action extends Settings_Vtiger_Index_Action {
    
    const rulesTable = 'sp_tips_module_rules';
    const dependentFieldsTable = 'sp_tips_dependent_fields';
    
    public function process (Vtiger_Request $request) {
        
        $record = $request->get('record');
        if ($record) {
            $this->editRule($request, $record);
        } else {
            $this->addRule($request);
        }
        
        header("Location: index.php?module=SPTips&view=Index&parent=Settings");
    }
    
    public function addRule(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();
        
        $sourceModule = $request->get('sourceModule');
        $sourceField = $request->get('sourceField');
        $sourceProviderField = $request->get('sourceProviderField');
        
        // create rule without selected provider
        if (empty($sourceProviderField) || empty($sourceField)) {
            return;
        }
        
        $dependentFields = $request->get('dependentFields');
        $providerFields = $request->get('providerFields');
        
        $providerId = $request->get('providerId');
        if (empty($providerId)) {
            return;
        }
        
        $sql = 'INSERT INTO ' . Settings_SPTips_SaveRule_Action::rulesTable . ' (module, field, provider_field, provider_id) VALUES (?,?,?,?)';
        $result = $db->pquery($sql, array($sourceModule, $sourceField, $sourceProviderField, $providerId));
        
        if ($result && $dependentFields && $providerFields) {
            $newRuleId = Settings_SPTips_SaveRule_Action::getLatestRuleId();
            foreach ($dependentFields as $key => $field) {
                $sql = 'INSERT INTO ' . Settings_SPTips_SaveRule_Action::dependentFieldsTable . ' (vtiger_fieldname, provider_fieldname, rule_id) VALUES (?,?,?)';
                $result = $db->pquery($sql, array($field, $providerFields[$key], $newRuleId));
            }
        }
    }
    
    public function editRule(Vtiger_Request $request, $record) {
        $db = PearDatabase::getInstance();
        
        $sourceModule = $request->get('sourceModule');
        $sourceField = $request->get('sourceField');
        $sourceProviderField = $request->get('sourceProviderField');
        $dependentFields = $request->get('dependentFields');
        $providerFields = $request->get('providerFields');
        $providerId = $request->get('providerId');
        
        $sql = 'UPDATE ' . Settings_SPTips_SaveRule_Action::rulesTable . ' SET module=?, field=?, provider_field=?, provider_id=? WHERE rule_id=?';
        $result = $db->pquery($sql, array($sourceModule, $sourceField, $sourceProviderField, $providerId, $record));
        
        
        if ($result) {
            // delete old rows for this rule_id
            $sql = 'DELETE FROM ' . Settings_SPTips_SaveRule_Action::dependentFieldsTable;
            $sql .= ' WHERE rule_id = ?';
            $deleteOldResult = $db->pquery($sql, array($record));
            
            // create new rows for this rule_id
            foreach ($dependentFields as $key => $field) {
                $sql = 'INSERT INTO ' . Settings_SPTips_SaveRule_Action::dependentFieldsTable . ' (vtiger_fieldname,provider_fieldname,rule_id) VALUES (?,?,?)';
                $result = $db->pquery($sql, array($field, $providerFields[$key], $record));
            }
        }
    }
    
    public function getLatestRuleId() {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_SaveRule_Action::rulesTable;
        $result = $db->query('SELECT MAX(rule_id) FROM ' . $tableName);
        return $db->query_result($result, 'max(id)');
    }
}