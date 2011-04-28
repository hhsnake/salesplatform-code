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
require_once 'modules/SalesOrder/SalesOrder.php';

class SalesPlatform_InvoicePDFController extends SalesPlatform_PDF_ProductListDocumentPDFController{

	function buildDocumentModel() {
	
		$model = parent::buildDocumentModel();
	
		$this->generateEntityModel($this->focus, 'Invoice', 'invoice_', $model);

		if($this->focusColumnValue('salesorder_id')) {
            	    $entity = new SalesOrder();
            	    $entity->retrieve_entity_info($this->focusColumnValue('salesorder_id'), 'SalesOrder');
            	    if(!empty($entity)) {
			$this->generateEntityModel($entity, 'SalesOrder', 'salesorder_', $model);
		    }
		}

		if($this->focusColumnValue('contact_id')) {
            	    $entity = new Contacts();
            	    $entity->retrieve_entity_info($this->focusColumnValue('contact_id'), 'Contacts');
            	    if(!empty($entity)) {
			$this->generateEntityModel($entity, 'Contacts', 'contact_', $model);
		    }
		}

		if($this->focusColumnValue('account_id')) {
            	    $entity = new Accounts();
            	    $entity->retrieve_entity_info($this->focusColumnValue('account_id'), 'Accounts');
            	    if(!empty($entity)) {
			$this->generateEntityModel($entity, 'Accounts', 'account_', $model);
		    }
		}

		$model->set('invoice_no', $this->focusColumnValue('invoice_no'));

		return $model;
	}

	function getWatermarkContent() {
// SkyAdmin begin
		return '';
		//return $this->focusColumnValue('invoicestatus');
// SkyAdmin end
	}

    function russianDate($date){
	$date=explode("-", $date);
	switch ($date[1]){
	    case 1: $m='Января'; break;
	    case 2: $m='Ффевраля'; break;
	    case 3: $m='Марта'; break;
	    case 4: $m='Апреля'; break;
	    case 5: $m='Мая'; break;
	    case 6: $m='Июня'; break;
	    case 7: $m='Июля'; break;
	    case 8: $m='Августа'; break;
	    case 9: $m='Сентября'; break;
	    case 10: $m='Октября'; break;
	    case 11: $m='Ноября'; break;
	    case 12: $m='Декабря'; break;
	}
	
	return $date[2].' '.$m.' '.$date[0].' г.';
    }
}
?>
