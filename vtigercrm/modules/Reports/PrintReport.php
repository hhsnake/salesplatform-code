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
require_once('Smarty_setup.php');
// SalesPlatform.ru begin
require_once("modules/Reports/SPReportRun.php");
//require_once("modules/Reports/ReportRun.php");
// SalesPlatform.ru end
require_once("modules/Reports/Reports.php");

global $app_strings;
global $mod_strings;
$oPrint_smarty=new vtigerCRM_Smarty;
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
$filtercolumn = $_REQUEST["stdDateFilterField"];
$filter = $_REQUEST["stdDateFilter"];
// SalesPlatform.ru begin
$oReportRun = new SPReportRun($reportid);
//$oReportRun = new ReportRun($reportid);
// SalesPlatform.ru end

$startdate = DateTimeField::convertToDBFormat($_REQUEST["startdate"]);//Convert the user date format to DB date format
$enddate = DateTimeField::convertToDBFormat($_REQUEST["enddate"]);//Convert the user date format to DB date format
$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);

// SalesPlatform.ru begin
    $reportParams = array();

    if(isset($_REQUEST["ownerFilter"])) {
	$selected_owner = vtlib_purify($_REQUEST["ownerFilter"]);
	$reportParams['{$owner}'] = $selected_owner;
    } else {
	$reportParams['{$owner}'] = '';
    }

    if(isset($_REQUEST["accountFilter"])) {
        $selected_account = vtlib_purify($_REQUEST["accountFilter"]);
        $reportParams['{$account}'] = $selected_account;
    } else {
        $reportParams['{$account}'] = '';
    }

require_once 'include/Zend/Json.php';
$json = new Zend_Json();

$advft_criteria = $_REQUEST['advft_criteria'];
if(!empty($advft_criteria)) $advft_criteria = $json->decode($advft_criteria);
$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
if(!empty($advft_criteria_groups)) $advft_criteria_groups = $json->decode($advft_criteria_groups);

if(!empty($advft_criteria)) {
    $filtersql = $oReportRun->RunTimeAdvFilter($advft_criteria,$advft_criteria_groups,$reportParams);
} else {
    $filtersql = $oReportRun->getAdvFilterSql($reportid, $reportParams);
}

if($filterlist != '') {
    if($filtersql != '')
        $filtersql = $filterlist . ' AND ' . $filtersql;
    else
        $filtersql = $filterlist;
}

$arr_values = $oReportRun->GenerateReport("PRINT",$filtersql,false,$reportParams);
$caption_html = $oReportRun->GenerateReport("CAPTION_HTML",$filtersql,false,$reportParams);
$total_report = $oReportRun->GenerateReport("PRINT_TOTAL",$filtersql);

//$arr_values = $oReportRun->GenerateReport("PRINT",$filterlist);
//$total_report = $oReportRun->GenerateReport("PRINT_TOTAL",$filterlist);
// SalesPlatform.ru end
$oPrint_smarty->assign("COUNT",$arr_values[1]);
$oPrint_smarty->assign("APP",$app_strings);
$oPrint_smarty->assign("MOD",$mod_strings);
$oPrint_smarty->assign("REPORT_NAME",$oReport->reportname);
// SalesPlatform.ru begin
$oPrint_smarty->assign("CAPTION_HTML",$caption_html);
// SalesPlatform.ru end
    $oPrint_smarty->assign("PRINT_CONTENTS",$arr_values[0]);
$oPrint_smarty->assign("TOTAL_HTML",$total_report);
$oPrint_smarty->display("PrintReport.tpl");
?>
