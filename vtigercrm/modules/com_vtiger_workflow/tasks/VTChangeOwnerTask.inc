<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 * The Original Code is: SalesPlatform.ru
 ************************************************************************************/

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');

class VTChangeOwnerTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('newownername');
	}

	public function doTask($wfEntity) {
                $util = new VTWorkflowUtils();
                $new_owner_id = $this->getUserOrGroupId(trim($this->newownername));
                if ($new_owner_id > 0) {
                    // Get the WorkflowEntity id
                    $ws_id = $wfEntity->getId();
                    if ($ws_id > 0) {
                        // Get the CRMEntity id
                        $relatedCRMid = vtws_getCRMEntityId($ws_id);
                        if ($relatedCRMid > 0) {
                            // Get the CRMEntity object
                            $data = CRMEntity::getInstance($wfEntity->getModuleName());
                            $data->retrieve_entity_info($relatedCRMid, $wfEntity->getModuleName());
                            // If retrieving OK
                            if ($data->column_fields['assigned_user_id'] > 0) {
                                // Set new owner
                                $data->column_fields['assigned_user_id'] = $new_owner_id;
                                // Save CRMEntity object
                                $data->insertIntoCrmEntity($wfEntity->getModuleName(), $relatedCRMid);
                            }
                        }
                    }
                }
	}

        private function getUserOrGroupId($name)
	{
		global $adb;
		$query = "(SELECT id AS id FROM vtiger_users WHERE user_name=? AND deleted=0)
                    UNION (SELECT groupid AS id FROM vtiger_groups WHERE groupname=?)";
		$result  =$adb->pquery($query, array($name, $name));
		$id = $adb->query_result($result, 0 , 'id');
		return $id;
	}
}
?>