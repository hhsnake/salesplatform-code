<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/VTEntityDelta.php';

class VTWorkflowEntity{
	function __construct($user, $id){
		$this->moduleName = null;
		$this->id = $id;
		$this->user = $user;
		$data = vtws_retrieve($id, $user);
		foreach($data as $key => $value){
			if(is_string($value)){
				$data[$key] = html_entity_decode($value, ENT_QUOTES, 'utf-8');
			}
		}
		$this->data = $data;
	}
	
        // SalesPlatform.ru begin Replace related record ids (12x765) with Entity names
	function processReferenceFields() {
            require_once 'include/Webservices/DescribeObject.php';
            // Describe object
            $meta_info = vtws_describe($this->getModuleName(), $this->user);
            foreach($meta_info['fields'] as $field_info_arr){
                $type_name = $field_info_arr['type']['name'];
                // Detect related fields
                if ($type_name == 'reference' || $type_name == 'owner') {
                    $ref_field_name = $field_info_arr['name'];
                    // Get CRM ID
                    $crmId = vtws_getCRMEntityId($this->data[$ref_field_name]);
                    if ($crmId > 0) {
                        // Get related Entity name
                        $this->data[$ref_field_name] = ($type_name == 'owner' || 
                                ($field_info_arr['type']['refersTo'][0] == 'Users')) ? 
                                getOwnerRealName($crmId) : getParentName($crmId);
                    }
                }
            }
	}
        // SalesPlatform.ru end
        
	/**
	 * Get the data from the entity object as an array.
	 *
	 * @return An array representation of the module data.
	 */
	function getData(){
		return $this->data;
	}

	/**
	 * Get the entity id.
	 *
	 * @return The entity id.
	 */
	function getId(){
		return $this->data['id'];
	}

	/**
	 * Get the name of the module represented by the entity data object.
	 *
	 * @return The module name.
	 */
	function getModuleName(){
		if($this->moduleName==null){
			global $adb;
			$wsId = $this->data['id'];
			$parts = explode('x', $wsId);
			$result = $adb->pquery('select name from vtiger_ws_entity where id=?',
						 array($parts[0]));
			$rowData = $adb->raw_query_result_rowdata($result, 0);
			$this->moduleName = $rowData['name'];
		}
                // SalesPlatform.ru begin Calendar workflow turn on
                if ($this->moduleName == "Events") {
                    $this->moduleName = "Calendar";
                }
                // SalesPlatform.ru end
		return $this->moduleName;
	}

	function get($fieldName){
		return $this->data[$fieldName];
	}

	function set($fieldName, $value){

		$this->data[$fieldName] = $value;
	}

	function save(){
		vtws_update($this->data,$this->user);
	}

	function isNew() {
		$wsId = $this->data['id'];
		$parts = explode('x', $wsId);
		$recordId = $parts[1];
		$entityDelta = new VTEntityDelta();
		$oldEntity = $entityDelta->getOldEntity($this->moduleName, $recordId);
		if($oldEntity == null) {
			return true;
		} else {
			return false;
		}
	}

}

class VTEntityCache{
	function __construct($user){
		$this->user = $user;
		$this->cache = array();
	}

	function forId($id){
		if($this->cache[$id]==null){
			$data  = new VTWorkflowEntity($this->user, $id);
			$this->cache[$id] = $data;
		}
		return $this->cache[$id];
	}
}
?>