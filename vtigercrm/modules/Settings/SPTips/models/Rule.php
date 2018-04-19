<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_Rule_Model extends Settings_Vtiger_Record_Model {
    
    const rulesTable = 'sp_tips_module_rules';
    const dependendFieldsTable = 'sp_tips_dependent_fields';
    
    public function getName() {
        return '';
    }
    
    public function getId() {
        return '';
    }
    
    public function getAvailablePicklistModules() {
        $adb = PearDatabase::getInstance();
        $sql = 'SELECT distinct vtiger_field.tabid, vtiger_tab.tablabel, vtiger_tab.name as tabname FROM vtiger_field ';
        $sql .= 'INNER JOIN vtiger_tab ON vtiger_tab.tabid = vtiger_field.tabid ';
        $sql .= 'AND vtiger_field.tabid !=' . getTabid('Emails');
        $sql .= ' AND vtiger_field.tabid !=' . getTabid('ModComments');
        $sql .= ' AND vtiger_field.tabid !=' . getTabid('PBXManager');
        $sql .= ' AND vtiger_tab.isentitytype = 1 ';
        $sql .= 'AND vtiger_field.displaytype = 1 ';
        $sql .= 'AND vtiger_field.presence in ("0","2") ';
        $sql .= 'AND vtiger_field.block != "NULL" ';
        $sql .= 'AND vtiger_tab.presence != 1 ';
        $sql .= 'GROUP BY vtiger_field.tabid HAVING count(*) > 1';
        
        $result = $adb->pquery($sql, array());
        while($row = $adb->fetch_array($result)) {
            $modules[$row['tablabel']] = $row['tabname'];
        }
        ksort($modules);
        
        $modulesModelsList = array();
        foreach($modules as $moduleLabel => $moduleName) {
            $instance = new Vtiger_Module_Model();
            $instance->name = $moduleName;
            $instance->label = $moduleLabel;
            $modulesModelsList[] = $instance;
        }
        return $modulesModelsList;
    }
    
    public function getAvailableModuleFields($moduleName) {
        $adb = PearDatabase::getInstance();
        $sql = 'SELECT fieldname, fieldlabel FROM vtiger_field WHERE ';
        $sql .= '(tabid = (SELECT tabid FROM vtiger_tab WHERE name = ?)) ';
        $sql .= 'AND vtiger_field.uitype IN (1,2,7, 9,11,13,17,19,20,21,22,24,55,71,255) ';
        $sql .= 'AND vtiger_field.fieldname NOT IN ("tags", "source", "one_s_id") ';
        $sql .= 'AND vtiger_field.displaytype = 1 AND vtiger_field.readonly = 1 AND vtiger_field.presence != 1';
        $result = $adb->pquery($sql, array($moduleName));
        while($row = $adb->fetch_array($result)) {
            $fields[$row['fieldname']] = $row['fieldlabel'];
        }
        return $fields;
    }
    
    public static function getCleanInstance() {
        $self = new self();
        $self->set('ruleId', '')
                ->set('sourceModule', '')
                ->set('sourceField', '')
                ->set('sourceProviderField', '')
                ->set('targetFields', '')
                ->set('targetProviderFields', '')
                ->set('providerId');
        return $self;
    }
    
    public static function getInstance($ruleId = false, $module=false, 
            $sourceField=false, $sourceProviderField=false,$targetFields=false, $providerFields=false,$providerId=false) {
        $self = new self();
        $self->set('ruleId', $ruleId)
                ->set('sourceModule', $module)
                ->set('sourceField', $sourceField)
                ->set('sourceProviderField', $sourceProviderField)
                ->set('targetFields', $targetFields)
                ->set('targetProviderFields', $providerFields)
                ->set('providerId', $providerId);
        return $self;
    }
    
    public function getInstanceById($ruleId) {
        $adb = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Rule_Model::rulesTable;
        $sql = 'SELECT * FROM '. $tableName . ' WHERE rule_id = ?';
        $result = $adb->pquery($sql, array($ruleId));
        
        if ($result) {
            $moduleName = $adb->query_result($result, 0, 'module');
            $sourceField = $adb->query_result($result, 0, 'field');
            $sourceProviderField = $adb->query_result($result, 0, 'provider_field');
            $providerId = $adb->query_result($result, 0, 'provider_id');
            $dependentFields = Settings_SPTips_ListRules_View::getAllDependentFieldsForCurrentModule($ruleId, $moduleName);
            $providerFields = Settings_SPTips_ListRules_View::getProviderFieldsForSelectedRule($ruleId);
            return Settings_SPTips_Rule_Model::getInstance($ruleId, $moduleName, $sourceField, $sourceProviderField, $dependentFields, $providerFields, $providerId);
        }
        return Settings_SPTips_Rule_Model::getCleanInstance();
    }
    
    public function getInstanceByNameAndProvider($name, $id) {
        $adb = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Rule_Model::rulesTable;
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE module = ? AND provider_id=?';
        $result = $adb->pquery($sql, array($name, $id));
        if ($result) {
            $ruleId = $adb->query_result($result, 0, 'rule_id');
            $instance = Settings_SPTips_Rule_Model::getInstanceById($ruleId);
            return $instance;
        }
        return Settings_SPTips_Rule_Model::getCleanInstance();
    }
    
    public function getBindingFieldForModule($moduleName, $providerId) {
        $adb = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Rule_Model::rulesTable;
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE module = ? AND provider_id=?';
        $result = $adb->pquery($sql, array($moduleName, $providerId));
        return $adb->query_result($result, 0, 'field');
    }
    
    public function getProviderFieldForSourceField($moduleName, $providerId) {
        $adb = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Rule_Model::rulesTable;
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE module = ? AND provider_id=?';
        $result = $adb->pquery($sql, array($moduleName, $providerId));
        return $adb->query_result($result, 0, 'provider_field');
    }
    
}