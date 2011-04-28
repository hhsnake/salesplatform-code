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
// SkyAdmin begin
include_once 'modules/Invoice/SPInvoicePDFController.php';
//include_once 'modules/Invoice/InvoicePDFController.php';
// SkyAdmin end
global $currentModule;

// SkyAdmin begin
$controller = new SalesPlatform_InvoicePDFController($currentModule, $_REQUEST['pdf_template']);
//$controller = new Vtiger_InvoicePDFController($currentModule);
// SkyAdmin end
$controller->loadRecord(vtlib_purify($_REQUEST['record']));

$filenameid = $_REQUEST['record'];
if(empty($filenameid)) $filenameid = time();
$filepath="storage/Invoice_$filenameid.pdf";
//added file name to make it work in IE, also forces the download giving the user the option to save
$controller->Output($filepath,'F');

// Added to fix annoying bug that includes HTML in your PDF
echo "<script>window.history.back();</script>";
exit()
?>
