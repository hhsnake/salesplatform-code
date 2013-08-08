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
include_once 'modules/SalesOrder/SPSalesOrderPDFController.php';
//include_once 'modules/SalesOrder/SalesOrderPDFController.php';
// SalesPlatform.ru end
$currentModule = vtlib_purify($_REQUEST['module']);

// SalesPlatform.ru begin
$controller = new SalesPlatform_SalesOrderPDFController($currentModule, vtlib_purify($_REQUEST['pdf_template']));
//$controller = new Vtiger_SalesOrderPDFController($currentModule);
// SalesPlatform.ru end

$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$salesorder_no = getModuleSequenceNumber($currentModule,vtlib_purify($_REQUEST['record']));
$controller->Output('SalesOrder_'.$salesorder_no.'.pdf', 'D');//added file name to make it work in IE, also forces the download giving the user the option to save
exit();
?>
