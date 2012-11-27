<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'modules/Potentials/SPPotentialsPDFController.php';
global $currentModule;

$controller = new SalesPlatform_PotentialsPDFController($currentModule, $_REQUEST['pdf_template']);
$controller->loadRecord(vtlib_purify($_REQUEST['record']));

$filenameid = $_REQUEST['record'];
if(empty($filenameid)) $filenameid = time();
$filepath="storage/Potential_$filenameid.pdf";
//added file name to make it work in IE, also forces the download giving the user the option to save
$controller->Output($filepath,'F');

// Added to fix annoying bug that includes HTML in your PDF
echo "<script>window.history.back();</script>";
exit()
?>
