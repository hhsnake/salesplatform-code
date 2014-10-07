<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
require_once 'modules/SPSocialConnector/SPSocialConnectorHelper.php';
require_once 'modules/SPSocialConnector/SPSocialConnector.php';

class SPSocialConnector_MassSaveAjax_Action extends Vtiger_Mass_Action {
    
    function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Save')) {
			throw new AppException(vtranslate($moduleName).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}

	/**
	 * Function that saves message records
	 * @param Vtiger_Request $request
	 */
	public function process(Vtiger_Request $request) {
		$urlFieldList = $request->get('fields');
		$response = new Vtiger_Response();
		if(!empty($urlFieldList)) {
			$response->setResult(true);
		} else {
			$response->setResult(false);
		}
		return $response;
	}
}

