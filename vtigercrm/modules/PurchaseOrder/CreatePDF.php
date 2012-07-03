<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
// SalesPlatform.ru begin
include_once 'modules/PurchaseOrder/SPPurchaseOrderPDFController.php';
//include_once 'modules/PurchaseOrder/PurchaseOrderPDFController.php';

global $currentModule;
// SalesPlatform.ru end


// SalesPlatform.ru begin
$controller = new SalesPlatform_PurchaseOrderPDFController($currentModule, $_REQUEST['pdf_template']);
//$controller = new Vtiger_PurchaseOrderPDFController($currentModule);
// SalesPlatform.ru end

$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$purchaseorder_no = getModuleSequenceNumber($currentModule,vtlib_purify($_REQUEST['record']));

// SalesPlatform.ru begin
if(isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
	$id = vtlib_purify($_REQUEST['record']);
	$filepath='test/product/'.$id.'_PurchaseOrder_'.$purchaseorder_no.'.pdf';
	$controller->Output($filepath,'F'); //added file name to make it work in IE, also forces the download giving the user the option to save
} else {
	$controller->Output('PurchaseOrder_'.$purchaseorder_no.'.pdf', 'D');//added file name to make it work in IE, also forces the download giving the user the option to save
	exit();
}
//$controller->Output('PurchaseOrder_'.$purchaseorder_no.'.pdf', 'D');//added file name to make it work in IE, also forces the download giving the user the option to save
//exit();
// SalesPlatform.ru end
?>
