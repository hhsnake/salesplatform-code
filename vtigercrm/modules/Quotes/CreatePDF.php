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
include_once 'modules/Quotes/SPQuotePDFController.php';
//include_once 'modules/Quotes/QuotePDFController.php';
global $currentModule;
// SalesPlatform.ru end

// SalesPlatform.ru begin
$controller = new SalesPlatform_QuotePDFController($currentModule, $_REQUEST['pdf_template']);
//$controller = new Vtiger_QuotePDFController($currentModule);
// SalesPlatform.ru end

$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$quote_no = getModuleSequenceNumber($currentModule,vtlib_purify($_REQUEST['record']));
if(isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
	$quote_id = vtlib_purify($_REQUEST['record']);
	$filepath='test/product/'.$quote_id.'_Quotes_'.$quote_no.'.pdf';
	//added file name to make it work in IE, also forces the download giving the user the option to save
	$controller->Output($filepath,'F');
} else {
	//added file name to make it work in IE, also forces the download giving the user the option to save
	$controller->Output('Quotes_'.$quote_no.'.pdf', 'D');
	exit();
}

?>
