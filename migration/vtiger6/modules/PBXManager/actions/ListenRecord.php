<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class PBXManager_ListenRecord_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();

        if(!Users_Privileges_Model::isPermitted($moduleName, 'ListView', $request->get('record'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    public function process(Vtiger_Request $request) {
        $pbxRecordModel = PBXManager_Record_Model::getInstanceById($request->get('record'));
        header("Location: " . $pbxRecordModel->get('recordingurl'));
        die();
    }
}