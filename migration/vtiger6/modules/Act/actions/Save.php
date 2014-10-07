<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Act_Save_Action extends Inventory_Save_Action {
    
    /**
     * Save Act vith relation to Invoice if it created from it
     * @param \Vtiger_Request $request
     */  
    public function process(Vtiger_Request $request) {
        /* Parent code - thin may be better will be workflow */
        $recordModel = $this->saveRecord($request);
        if($request->get('relationOperation')) {
                $parentModuleName = $request->get('sourceModule');
                $parentRecordId = $request->get('sourceRecord');
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
                $loadUrl = $parentRecordModel->getDetailViewUrl();
        } else if ($request->get('returnToList')) {
                $loadUrl = $recordModel->getModule()->getListViewUrl();
        } else {
                $loadUrl = $recordModel->getDetailViewUrl();
        }
        
        /* If created from Invoice - add reference */
        if(($request->get('sourceModule') == 'Invoice') && $request->get('sourceRecord') != NULL) {
            $invoiceRecord = Invoice_Record_Model::getInstanceById($request->get('sourceRecord'), 'Invoice');
            if($invoiceRecord != null) {
                $invoiceRecord->set('sp_act_id', $recordModel->getId());
                $invoiceRecord->set('mode','edit');
                $invoiceRecord->save();
                $loadUrl = $invoiceRecord->getDetailViewUrl();
            }
        }
        
        header("Location: $loadUrl");
    }
}
