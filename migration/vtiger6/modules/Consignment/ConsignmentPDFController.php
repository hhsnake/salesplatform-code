<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

include_once 'include/InventoryPDFController.php';
require_once 'modules/Accounts/Accounts.php';

class Vtiger_ConsignmentPDFController extends Vtiger_InventoryPDFController {

}

//SalesPlatform.ru begin

include_once 'includes/SalesPlatform/PDF/ProductListPDFController.php';
require_once 'modules/SalesOrder/SalesOrder.php';
require_once 'modules/Invoice/Invoice.php';

class SalesPlatform_ConsignmentPDFController extends SalesPlatform_PDF_ProductListDocumentPDFController{

	function buildDocumentModel() {
	
		$model = parent::buildDocumentModel();
	
		$this->generateEntityModel($this->focus, 'Consignment', 'consignment_', $model);

                $entity = new SalesOrder();
		if($this->focusColumnValue('salesorder_id')) {
            	    $entity->retrieve_entity_info($this->focusColumnValue('salesorder_id'), 'SalesOrder');
		}
                $this->generateEntityModel($entity, 'SalesOrder', 'salesorder_', $model);

                $entity = new Contacts();
		if($this->focusColumnValue('contact_id')) {
            	    $entity->retrieve_entity_info($this->focusColumnValue('contact_id'), 'Contacts');
		}
                $this->generateEntityModel($entity, 'Contacts', 'contact_', $model);

                $entity = new Accounts();
		if($this->focusColumnValue('account_id')) {
            	    $entity->retrieve_entity_info($this->focusColumnValue('account_id'), 'Accounts');
		}
                $this->generateEntityModel($entity, 'Accounts', 'account_', $model);

                $this->generateUi10Models($model);
                $this->generateRelatedListModels($model);

		$model->set('consignment_no', $this->focusColumnValue('consignment_no'));

		return $model;
	}

	function getWatermarkContent() {
		return '';
	}

    function russianDate($date){
	$date=explode("-", $date);
	switch ($date[1]){
	    case 1: $m='Января'; break;
	    case 2: $m='Февраля'; break;
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

//SalesPlatform.ru end
?>

