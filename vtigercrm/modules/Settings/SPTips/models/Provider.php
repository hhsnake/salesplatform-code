<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_Provider_Model extends Settings_Vtiger_Record_Model {
    
    const currentProviderTable = 'sp_tips_cur_provider';
    const providersTable = 'sp_tips_providers';
    
    public function getId() {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Provider_Model::currentProviderTable;
        $sql = 'SELECT * FROM ' . $tableName;
        $result = $db->pquery($sql, array());
        if ($result) {
            return $db->query_result($result, 0, 'cur_provider');
        }
        return null;
    }
    
    public function getName() {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Provider_Model::providersTable;
        $id =  $this->get('id');
        if ($id) {
            $sql = 'SELECT * FROM ' . $tableName .' WHERE provider_id =?';
            $result = $db->pquery($sql, array($id));
            return $db->query_result($result, 0, 'provider_name');
        }
        return null;
    }
    
    public function getProviderIdByName($name) {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Provider_Model::providersTable;
        $sql = 'SELECT provider_id FROM ' . $tableName . ' WHERE provider_name=?';
        $result = $db->pquery($sql, array($name));
        if ($result) {
            return $db->query_result($result, 0, 'provider_id');
        }
        else {
            return null;
        }
    }

    /*
     * returns instance of current provider in system
     */
    public static function getInstance() {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Provider_Model::providersTable;
        
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE provider_id = ?';
        $curProviderId = Settings_SPTips_Provider_Model::getId();
        $curProviderResult = $db->pquery($sql, array($curProviderId));
        
        $instance = new Settings_SPTips_Provider_Model();
        
        if($db->num_rows($curProviderResult) > 0) {
            $row = $db->query_result_rowdata($curProviderResult,0);
            $instance->setData($row);
        }
        return $instance;
    }
    
    /*
     * returns providers instance by id
     */
    public function getInstanceById($providerId) {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Provider_Model::providersTable;
        
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE provider_id = ?';
        $curProviderResult = $db->pquery($sql, array($providerId));
        
        $instance = new Settings_SPTips_Provider_Model();
        
        if($db->num_rows($curProviderResult) > 0) {
            $row = $db->query_result_rowdata($curProviderResult,0);
            $instance->setData($row);
        }
        return $instance;
    }
    
    public function getCleanInstance() {
        $self = new self();
        $self->set('provider_id', '')
                ->set('provider_name', '')
                ->set('api_key', '')
                ->set('token', '');
        return $self;
    }
    
    public function getAllProviders() {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM ' . Settings_SPTips_Provider_Model::providersTable;
        $result = $db->query($sql);
        
        $existingProviders = array();
        if($db->getRowCount($result) > 0){
            while ($row = $db->fetchByAssoc($result)) {
                $existingProviders[$row['provider_id']] = $row['provider_name']; 
            }
        }
        return $existingProviders;
    }
    
    public function getProviderClassName($providerName) {
        return 'SPTips_' . $providerName . 'Provider_Model';
    }
    
    public function isProviderExists($providerId) {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPTips_Provider_Model::providersTable;
        
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE provider_id = ?';
        $result = $db->pquery($sql, array($providerId));
        if ($db->num_rows($result)) {
            return true;
        }
        return false;
    }
    
}

