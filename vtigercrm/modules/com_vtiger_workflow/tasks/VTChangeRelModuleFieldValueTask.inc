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

class VTChangeRelModuleFieldValueTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('modulename', 'relfieldname', 'fieldname', 'fieldvalue');
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

                        if(isset($data->column_fields[$this->relfieldname]) &&
                                $data->column_fields[$this->relfieldname] > 0) {

                            $old_ajxaction = $_REQUEST['ajxaction'];
                            $old_module = $_REQUEST['module'];
                            $_REQUEST['ajxaction'] = 'DETAILVIEW';
                            $_REQUEST['module'] = $this->modulename;
                            $rel_entity = CRMEntity::getInstance($this->modulename);

                            // Set new fileld value
                            if (!empty($this->fieldname)) {
                                $rel_entity->retrieve_entity_info($data->column_fields[$this->relfieldname],
                                        $this->modulename);
                                $rel_entity->column_fields[$this->fieldname] = $this->fieldvalue;
                                $rel_entity->mode = 'edit';
                                $rel_entity->id = $data->column_fields[$this->relfieldname];
                                $rel_entity->save($this->modulename);

                            }

                            $_REQUEST['ajxaction'] = $old_ajxaction;
                            $_REQUEST['module'] = $old_module;
                        }
                    }
                }
	}

}
?>