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

class VTChangeFieldValueTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('fieldname', 'fieldvalue');
	}

	public function doTask($wfEntity) {
                $util = new VTWorkflowUtils();
                // Get the WorkflowEntity id
                $ws_id = $wfEntity->getId();
                if ($ws_id > 0) {
                    // Get the CRMEntity id
                    $relatedCRMid = substr($ws_id, stripos($ws_id, 'x') + 1);
                    if ($relatedCRMid > 0) {
                        // Get the CRMEntity object
                        $data = CRMEntity::getInstance($wfEntity->getModuleName());
                        $data->retrieve_entity_info($relatedCRMid, $wfEntity->getModuleName());
                        // Set new fileld value
                        if (!empty($this->fieldname)) {
                            $this->updateRecordField($wfEntity->getModuleName(), $this->fieldname,
                                    $this->fieldvalue, $data->tab_name_index, $relatedCRMid);
                        }
                        // Save CRMEntity object
                        $data->insertIntoCrmEntity($wfEntity->getModuleName(), $relatedCRMid);
                    }
                }
	}

        private function updateRecordField($module, $what, $value, $tab_name_index, $where_value) {
                $result = false;
                $tabid = getTabid($module);
                global $adb;
                $query = "SELECT tablename, columnname FROM vtiger_field WHERE fieldname = ? AND tabid = ?";
                $res = $adb->pquery($query, array($what, $tabid));
		$rows = $adb->num_rows($res);
                if ($rows > 0) {
                    $tablename = $adb->query_result($res, 0, 'tablename');
                    $columnname = $adb->query_result($res, 0, 'columnname');
                    if (!empty($tablename) && !empty($columnname)) {
                        $where = $tab_name_index[$tablename];
                        if (!empty($where)) {
                            $query = "UPDATE $tablename SET $columnname = ? WHERE $where = ?";
                            $res = $adb->pquery($query, array($value, $where_value));
                            $result = true;
                        }
                    }
                }
                return $result;
        }
}
?>