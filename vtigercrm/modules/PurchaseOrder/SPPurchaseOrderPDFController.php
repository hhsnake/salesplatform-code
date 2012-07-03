<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'include/SalesPlatform/PDF/ProductListPDFController.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Vendors/Vendors.php';

class SalesPlatform_PurchaseOrderPDFController extends SalesPlatform_PDF_ProductListDocumentPDFController{

	function buildDocumentModel() {
	
		$model = parent::buildDocumentModel();
	
		$this->generateEntityModel($this->focus, 'PurchaseOrder', 'purchaseorder_', $model);

		if($this->focusColumnValue('contact_id')) {
            	    $entity = new Contacts();
            	    $entity->retrieve_entity_info($this->focusColumnValue('contact_id'), 'Contacts');
            	    if(!empty($entity)) {
			$this->generateEntityModel($entity, 'Contacts', 'contact_', $model);
		    }
		}

		if($this->focusColumnValue('vendor_id')) {
            	    $entity = new Vendors();
            	    $entity->retrieve_entity_info($this->focusColumnValue('vendor_id'), 'Vendors');
            	    if(!empty($entity)) {
			$this->generateEntityModel($entity, 'Vendors', 'vendor_', $model);
		    }
		}

		$model->set('purchaseorder_no', $this->focusColumnValue('purchaseorder_no'));

		return $model;
	}

	function getWatermarkContent() {
		return '';
	}

}
?>
